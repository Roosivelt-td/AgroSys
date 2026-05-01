package com.sigcpa.agrosys.ui

import android.Manifest
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.LinearLayout
import android.widget.PopupWindow
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.github.mikephil.charting.components.XAxis
import com.github.mikephil.charting.data.BarData
import com.github.mikephil.charting.data.BarDataSet
import com.github.mikephil.charting.data.BarEntry
import com.github.mikephil.charting.formatter.ValueFormatter
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.sigcpa.agrosys.MainActivity
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import com.sigcpa.agrosys.database.entities.CosechaEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ActivityDashboardBinding
import com.sigcpa.agrosys.databinding.DialogAddCatalogoCultivoBinding
import com.sigcpa.agrosys.databinding.DialogUserMenuBinding
import com.sigcpa.agrosys.network.GeocodingService
import com.sigcpa.agrosys.ui.adapters.CultivoCursoAdapter
import com.sigcpa.agrosys.ui.viewModel.WeatherViewModel
import com.sigcpa.agrosys.utils.CatalogImporter
import kotlinx.coroutines.launch
import java.io.File
import java.text.NumberFormat
import java.text.SimpleDateFormat
import java.util.*

class DashboardActivity : AppCompatActivity() {
    private lateinit var binding: ActivityDashboardBinding
    private lateinit var weatherViewModel: WeatherViewModel
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private lateinit var geocodingService: GeocodingService
    private val db by lazy { AppDatabase.getDatabase(this) }
    private val catalogImporter by lazy { CatalogImporter(this) }

    private var hasTerrenos = false
    private var hasCultivos = false
    
    private var currentLat: Double = 0.0
    private var currentLon: Double = 0.0

    private lateinit var cultivoCursoAdapter: CultivoCursoAdapter

    private var userName: String = ""
    private var userRole: String = ""
    private var currentUser: com.sigcpa.agrosys.database.dao.UsuarioWithRol? = null

    // Launcher para importar JSON
    private val importJsonLauncher = registerForActivityResult(
        ActivityResultContracts.GetContent()
    ) { uri ->
        uri?.let { importCatalog(it) }
    }

    // Permisos de ubicación
    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        if (isGranted) {
            getLocationAndWeather()
        } else {
            Toast.makeText(this, getString(R.string.msg_location_permission_denied), Toast.LENGTH_LONG).show()
            weatherViewModel.fetchWeatherByCity(getString(R.string.default_city))
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDashboardBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        // SOLUCIÓN: Ajustar el menú inferior para que no lo tapen los botones del sistema
        val initialBottomPadding = binding.bottomNav.paddingBottom
        ViewCompat.setOnApplyWindowInsetsListener(binding.bottomNav) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, initialBottomPadding + systemBars.bottom)
            insets
        }

        // También ajustamos el Header para que respete la barra de estado superior
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            val header = binding.root.findViewById<LinearLayout>(R.id.header_container)
            header?.setPadding(
                header.paddingLeft,
                systemBars.top,
                header.paddingRight,
                header.paddingBottom
            )
            insets
        }

        weatherViewModel = ViewModelProvider(this)[WeatherViewModel::class.java]
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        geocodingService = GeocodingService(this)

        actualizarSaludo()
        loadWeatherWithLocation()
        
        userName = intent.getStringExtra("USER_NAME") ?: "Usuario"
        userRole = intent.getStringExtra("USER_ROLE") ?: "agricultor"
        
        setupUI(userName, userRole)
        setupRecyclerView()
        observeWeatherData()
        // El acceso admin se verificará dentro de loadUserData para tener el objeto Usuario completo
    }

    private fun setupRecyclerView() {
        cultivoCursoAdapter = CultivoCursoAdapter(emptyList())
        binding.rvCultivosEnCurso.apply {
            layoutManager = LinearLayoutManager(this@DashboardActivity)
            adapter = cultivoCursoAdapter
        }
    }

    private fun checkAdminAccess(userWithRol: com.sigcpa.agrosys.database.dao.UsuarioWithRol?) {
        // Solo entra si es admin_puro (el dueño del sistema) 
        // o si es un supervisor (rol de rango medio)
        val isAdmin = userWithRol?.usuario?.es_admin_puro == true
        val isSupervisor = userWithRol?.nombre_rol?.lowercase() == "supervisor"
        
        if (isAdmin || isSupervisor) {
            binding.cvAdminPanel.visibility = View.VISIBLE
            binding.btnManageCatalogs.setOnClickListener {
                showAddCatalogDialog()
            }
            binding.btnManageUsers.setOnClickListener {
                Toast.makeText(this, getString(R.string.msg_admin_users_enabled), Toast.LENGTH_SHORT).show()
            }
        } else {
            // SI NO ES NINGUNO DE LOS ANTERIORES, OCULTAR SIEMPRE
            binding.cvAdminPanel.visibility = View.GONE
        }
    }

    private fun showAddCatalogDialog() {
        val dialogBinding = DialogAddCatalogoCultivoBinding.inflate(layoutInflater)
        val dialog = androidx.appcompat.app.AlertDialog.Builder(this)
            .setView(dialogBinding.root)
            .create()

        dialog.window?.setBackgroundDrawableResource(android.R.color.transparent)

        dialogBinding.btnImportJson.setOnClickListener {
            importJsonLauncher.launch("application/json")
            dialog.dismiss()
        }

        dialogBinding.btnCancel.setOnClickListener { dialog.dismiss() }

        dialogBinding.btnSave.setOnClickListener {
            val nombre = dialogBinding.etCatNombre.text.toString().trim()
            val cientifico = dialogBinding.etCatCientifico.text.toString().trim()
            val ciclo = dialogBinding.etCatCiclo.text.toString().trim()
            val dias = dialogBinding.etCatDias.text.toString().toIntOrNull()
            val riego = dialogBinding.etCatRiego.text.toString().trim()
            val plagas = dialogBinding.etCatPlagas.text.toString().trim()

            if (nombre.isNotEmpty()) {
                lifecycleScope.launch {
                    val nuevo = CatalogoCultivoEntity(
                        nombre = nombre,
                        nombre_cientifico = if (cientifico.isEmpty()) null else cientifico,
                        tipo_ciclo = ciclo,
                        vida_util_estimada_meses = if (ciclo.lowercase().contains("perenne")) 60 else null,
                        dias_a_cosecha_promedio = dias,
                        instrucciones_base_riego = if (riego.isEmpty()) null else riego,
                        instrucciones_base_plagas = if (plagas.isEmpty()) null else plagas
                    )
                    db.assetDao().insertCatalogoCultivo(nuevo)
                    Toast.makeText(this@DashboardActivity, "✅ $nombre", Toast.LENGTH_SHORT).show()
                    dialog.dismiss()
                }
            } else {
                Toast.makeText(this, getString(R.string.msg_name_required), Toast.LENGTH_SHORT).show()
            }
        }

        dialog.show()
    }

    private fun importCatalog(uri: android.net.Uri) {
        lifecycleScope.launch {
            val result = catalogImporter.importCultivosFromJson(uri)
            if (result.isSuccess) {
                Toast.makeText(this@DashboardActivity, "✅ Importados ${result.getOrNull()} cultivos", Toast.LENGTH_LONG).show()
            } else {
                Toast.makeText(this@DashboardActivity, "❌ Error al importar: ${result.exceptionOrNull()?.message}", Toast.LENGTH_LONG).show()
            }
        }
    }

    override fun onResume() {
        super.onResume()
        updateStatsAndLocks()
        actualizarSaludo()
        loadUserData()
    }

    private fun loadUserData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        
        lifecycleScope.launch {
            val user = db.userDao().getUsuarioById(userId)
            currentUser = user
            
            // Verificar acceso administrativo aquí con el objeto completo
            checkAdminAccess(user)

            user?.let {
                binding.tvWelcomeUser.text = it.usuario.nombre
                binding.tvUserInfoLine.text = it.nombre_rol.replaceFirstChar { char -> char.uppercase() }
                
                // Cargar foto de perfil
                it.usuario.foto_perfil_url?.let { path ->
                    val file = File(path)
                    if (file.exists()) {
                        Glide.with(this@DashboardActivity)
                            .load(file)
                            .circleCrop()
                            .diskCacheStrategy(DiskCacheStrategy.NONE)
                            .skipMemoryCache(true)
                            .into(binding.ivUserAvatar)
                        
                        // Eliminar tinte para ver la foto real
                        binding.ivUserAvatar.imageTintList = null
                        binding.ivUserAvatar.setPadding(0, 0, 0, 0)
                    }
                }
            }
        }
    }

    private fun loadWeatherWithLocation() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
            getLocationAndWeather()
        } else {
            requestPermissionLauncher.launch(Manifest.permission.ACCESS_FINE_LOCATION)
        }
    }

    private fun getLocationAndWeather() {
        val locationManager = getSystemService(Context.LOCATION_SERVICE) as LocationManager
        if (!locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
            weatherViewModel.fetchWeatherByCity(getString(R.string.default_city))
            return
        }

        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) return

        binding.progressWeather.visibility = View.VISIBLE
        fusedLocationClient.lastLocation.addOnSuccessListener { location: Location? ->
            if (location != null) {
                currentLat = location.latitude
                currentLon = location.longitude
                lifecycleScope.launch {
                    val locationInfo = geocodingService.getLocationInfo(currentLat, currentLon)
                    if (locationInfo != null) {
                        weatherViewModel.fetchWeatherByLocation(currentLat, currentLon, locationInfo)
                    } else {
                        weatherViewModel.fetchWeatherByLocation(currentLat, currentLon)
                    }
                }
            } else {
                weatherViewModel.fetchWeatherByCity(getString(R.string.default_city))
            }
        }.addOnFailureListener {
            weatherViewModel.fetchWeatherByCity(getString(R.string.default_city))
        }
    }

    private fun observeWeatherData() {
        weatherViewModel.weatherData.observe(this) { weather ->
            weather?.let { updateWeatherUI(it) }
        }
        weatherViewModel.locationName.observe(this) { location ->
            binding.tvCityName.text = location
        }
        weatherViewModel.isLoading.observe(this) { isLoading ->
            if (!isLoading) binding.progressWeather.visibility = View.GONE
        }
    }

    private fun updateWeatherUI(weather: com.sigcpa.agrosys.models.WeatherResponse) {
        binding.tvTemperature.text = weatherViewModel.formatTemperature(weather.main.temp)
        binding.tvWeatherDescription.text = weatherViewModel.getWeatherDescription(weather.weather.firstOrNull()?.description ?: "")
        Glide.with(this).load(weatherViewModel.getWeatherIconUrl(weather.weather.firstOrNull()?.icon ?: "01d")).into(binding.ivWeatherIcon)

        // Actualizar burbuja de consejo
        updateWeatherTip(weather)
    }

    private fun updateWeatherTip(weather: com.sigcpa.agrosys.models.WeatherResponse) {
        val mainCondition = weather.weather.firstOrNull()?.main?.lowercase() ?: ""
        val description = weather.weather.firstOrNull()?.description?.lowercase() ?: ""
        
        val tip = when {
            mainCondition.contains("rain") || mainCondition.contains("drizzle") || description.contains("lluvia") -> {
                getString(R.string.weather_tip_rain)
            }
            mainCondition.contains("clear") || description.contains("despejado") || description.contains("soleado") -> {
                getString(R.string.weather_tip_sunny)
            }
            mainCondition.contains("cloud") || description.contains("nubes") || description.contains("nublado") -> {
                getString(R.string.weather_tip_cloudy)
            }
            else -> getString(R.string.weather_tip_default)
        }
        
        binding.tvWeatherTip.apply {
            text = tip
            visibility = View.VISIBLE
        }
    }

    private fun actualizarSaludo() {
        val hora = Calendar.getInstance().get(Calendar.HOUR_OF_DAY)
        val (saludoRes, icono) = when (hora) {
            in 0..5 -> R.string.saludo_buenas_noches to "🌙"
            in 6..11 -> R.string.saludo_buenos_dias to "🌅"
            in 12..18 -> R.string.saludo_buenas_tardes to "🌤️"
            else -> R.string.saludo_buenas_noches to "🌙"
        }
        binding.saludoTexto.text = getString(saludoRes)
        binding.ivSaludoIcono.text = icono
    }

    private fun setupUI(name: String, role: String) {
        binding.tvWelcomeUser.text = name
        binding.tvUserInfoLine.text = role.replaceFirstChar { it.uppercase() }
        
        binding.ivUserAvatar.setOnClickListener { showUserMenu() }
        binding.btnProfileCard.setOnClickListener { showUserMenu() }

        // Abrir detalles del clima al tocar la tarjeta
        binding.cvWeatherCard.setOnClickListener {
            val intent = Intent(this, WeatherDetailActivity::class.java)
            intent.putExtra("CITY_NAME", binding.tvCityName.text.toString())
            intent.putExtra("LAT", currentLat)
            intent.putExtra("LON", currentLon)
            startActivity(intent)
        }

        // Navegación a Terrenos
        val goToTerrenos = { startActivity(Intent(this, TerrenosListActivity::class.java)) }
        binding.navTerrenoQuick.setOnClickListener { goToTerrenos() }
        binding.navTerrenos.setOnClickListener { goToTerrenos() }
        
        // Navegación a Cultivos
        val goToCultivos = {
            if (hasTerrenos) startActivity(Intent(this, CultivosListActivity::class.java))
            else showLockMessage("terreno")
        }
        binding.navCultivoQuick.setOnClickListener { goToCultivos() }
        binding.navCultivos.setOnClickListener { goToCultivos() }

        // Navegación a Labores
        val goToLabores = {
            if (hasCultivos) startActivity(Intent(this, LaboresListActivity::class.java))
            else showLockMessage("cultivo")
        }
        binding.navLaborQuick.setOnClickListener { goToLabores() }
        binding.navLabores.setOnClickListener { goToLabores() }

        binding.navCosechaQuick.setOnClickListener { 
            if (hasCultivos) {
                startActivity(Intent(this, CultivosListActivity::class.java))
                Toast.makeText(this, getString(R.string.msg_select_cultivo_harvest), Toast.LENGTH_SHORT).show()
            } else {
                showLockMessage("cultivo")
            }
        }

        binding.navVentaQuick.setOnClickListener {
            if (hasCultivos) {
                startActivity(Intent(this, RegisterVentaActivity::class.java))
            } else {
                showLockMessage("cultivo")
            }
        }

        binding.navReportes.setOnClickListener {
            startActivity(Intent(this, ReportesActivity::class.java))
        }

        binding.btnVerTodosCultivos.setOnClickListener {
            startActivity(Intent(this, CultivosListActivity::class.java))
        }
    }

    private fun showUserMenu() {
        val popupBinding = DialogUserMenuBinding.inflate(layoutInflater)
        val popupView = popupBinding.root

        val popupWindow = PopupWindow(
            popupView,
            LinearLayout.LayoutParams.WRAP_CONTENT,
            LinearLayout.LayoutParams.WRAP_CONTENT,
            true
        )

        popupWindow.elevation = 20f

        // Cargar foto de perfil en el menú
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        
        lifecycleScope.launch {
            val user = db.userDao().getUsuarioById(userId)
            val fotoUrl = user?.usuario?.foto_perfil_url
            
            val file = if (fotoUrl != null) File(fotoUrl) else {
                val safeName = userName.replace(" ", "_")
                File(getExternalFilesDir(null), "perfil/${userId}_${safeName}/profile.jpg")
            }

            if (file.exists()) {
                Glide.with(this@DashboardActivity)
                    .load(file)
                    .circleCrop()
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
                    .into(popupBinding.imgMenuProfile)
                
                // Limpiar padding y tinte para que la foto se vea a pantalla completa
                popupBinding.imgMenuProfile.setPadding(0, 0, 0, 0)
                popupBinding.imgMenuProfile.imageTintList = null
            }
        }

        popupBinding.btnViewProfile.setOnClickListener {
            popupWindow.dismiss()
            startActivity(Intent(this, ProfileActivity::class.java))
        }

        popupBinding.btnLogout.setOnClickListener {
            popupWindow.dismiss()
            logout()
        }

        popupWindow.showAsDropDown(binding.ivUserAvatar, 0, 10)
    }

    private fun updateStatsAndLocks() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        lifecycleScope.launch {
            val userWithRol = db.userDao().getUsuarioById(userId)
            if (userWithRol != null) {
                val agricultorId = userWithRol.usuario.id
                val numTerrenos = db.assetDao().countTerrenosByAgricultor(agricultorId)
                val cultivos = db.assetDao().getCultivosByAgricultor(agricultorId)
                hasTerrenos = numTerrenos > 0
                hasCultivos = cultivos.isNotEmpty()
                binding.tvStatTerrenos.text = numTerrenos.toString()
                binding.tvStatCultivos.text = cultivos.size.toString()
                
                // Actualizar resumen financiero
                val ingresos = db.assetDao().getIngresosTotalesByAgricultor(agricultorId) ?: 0.0
                val costos = db.assetDao().getCostosTotalesByAgricultor(agricultorId) ?: 0.0
                val neto = ingresos - costos
                
                val currencyFormat = NumberFormat.getCurrencyInstance(Locale("es", "PE"))
                binding.tvIngresosDash.text = currencyFormat.format(ingresos)
                binding.tvCostosDash.text = currencyFormat.format(costos)
                binding.tvNetoDash.text = currencyFormat.format(neto)
                binding.tvStatProfit.text = currencyFormat.format(neto)
                
                // Actualizar cultivos en curso
                val cultivosEnCurso = db.assetDao().getCultivosEnCursoByAgricultor(agricultorId)
                val tripleList = cultivosEnCurso.map { cul ->
                    val terreno = db.assetDao().getTerrenoById(cul.terreno_id)
                    val cat = db.assetDao().getCatalogoCultivoById(cul.catalogo_cultivo_id)
                    Triple(cul, terreno, cat)
                }
                cultivoCursoAdapter.updateData(tripleList)
                
                // Actualizar gráfico de producción
                val cosechas = db.assetDao().getCosechasRecientesByAgricultor(agricultorId)
                setupProductionChart(cosechas)
                
                // Alertas de alquiler
                val terrenos = db.assetDao().getTerrenosByAgricultor(agricultorId)
                checkAlquilerAlerts(terrenos)
                
                applyLockStyles()
            }
        }
    }

    private fun checkAlquilerAlerts(terrenos: List<com.sigcpa.agrosys.database.entities.TerrenoEntity>) {
        binding.layoutAlertasAlquiler.removeAllViews()
        val now = System.currentTimeMillis() / 1000
        val warnings = mutableListOf<View>()

        for (terreno in terrenos) {
            if (terreno.tipo_tenencia == "alquilado" && terreno.fecha_vencimiento_alquiler != null) {
                val vencimiento = terreno.fecha_vencimiento_alquiler
                val diffSeconds = vencimiento - now
                val diffDays = (diffSeconds / (24 * 60 * 60)).toInt()

                if (diffDays <= 15) {
                    val alertView = LayoutInflater.from(this).inflate(R.layout.item_alerta_alquiler, binding.layoutAlertasAlquiler, false)
                    val tvTitle = alertView.findViewById<android.widget.TextView>(R.id.tvAlertaTitulo)
                    val tvMsg = alertView.findViewById<android.widget.TextView>(R.id.tvAlertaMensaje)
                    val card = alertView.findViewById<com.google.android.material.card.MaterialCardView>(R.id.cardAlerta)

                    tvTitle.text = terreno.nombre
                    if (diffSeconds < 0) {
                        tvMsg.text = getString(R.string.label_alerta_vencido)
                        card.setCardBackgroundColor(android.graphics.Color.parseColor("#FEF2F2"))
                        tvMsg.setTextColor(android.graphics.Color.parseColor("#B91C1C"))
                    } else {
                        tvMsg.text = getString(R.string.label_alerta_vencimiento, diffDays)
                        card.setCardBackgroundColor(android.graphics.Color.parseColor("#FFFBEB"))
                        tvMsg.setTextColor(android.graphics.Color.parseColor("#B45309"))
                    }

                    alertView.setOnClickListener {
                        val intent = Intent(this, DetalleTerrenoActivity::class.java)
                        intent.putExtra("TERRENO_ID", terreno.id)
                        startActivity(intent)
                    }

                    binding.layoutAlertasAlquiler.addView(alertView)
                    warnings.add(alertView)
                }
            }
        }

        binding.layoutAlertasAlquiler.visibility = if (warnings.isNotEmpty()) View.VISIBLE else View.GONE
    }

    private fun setupProductionChart(cosechas: List<CosechaEntity>) {
        if (cosechas.isEmpty()) {
            binding.barChartProduccion.clear()
            binding.barChartProduccion.setNoDataText("Sin datos de cosecha")
            return
        }

        val entries = mutableListOf<BarEntry>()
        val labels = mutableListOf<String>()
        val sdf = SimpleDateFormat("dd/MM", Locale.getDefault())

        cosechas.reversed().forEachIndexed { index, cosecha ->
            entries.add(BarEntry(index.toFloat(), cosecha.cantidad_kg.toFloat()))
            labels.add(sdf.format(Date(cosecha.fecha_cosecha * 1000)))
        }

        val dataSet = BarDataSet(entries, "Producción (kg)")
        dataSet.color = android.graphics.Color.parseColor("#16a34a")
        dataSet.valueTextColor = android.graphics.Color.parseColor("#1e293b")
        dataSet.valueTextSize = 10f

        val barData = BarData(dataSet)
        binding.barChartProduccion.apply {
            data = barData
            description.isEnabled = false
            legend.isEnabled = false
            
            xAxis.apply {
                position = XAxis.XAxisPosition.BOTTOM
                valueFormatter = object : ValueFormatter() {
                    override fun getFormattedValue(value: Float): String {
                        return labels.getOrNull(value.toInt()) ?: ""
                    }
                }
                granularity = 1f
                setDrawGridLines(false)
            }
            
            axisLeft.apply {
                axisMinimum = 0f
                setDrawGridLines(true)
            }
            
            axisRight.isEnabled = false
            animateY(1000)
            invalidate()
        }
    }

    private fun applyLockStyles() {
        val alphaCultivo = if (hasTerrenos) 1.0f else 0.4f
        binding.navCultivoQuick.alpha = alphaCultivo
        binding.navCultivos.alpha = alphaCultivo
        
        val alphaLabor = if (hasCultivos) 1.0f else 0.4f
        binding.navLaborQuick.alpha = alphaLabor
        binding.navLabores.alpha = alphaLabor
        binding.navCosechaQuick.alpha = alphaLabor
    }

    private fun showLockMessage(type: String) {
        val msg = if (type == "terreno") "⚠️ Registra un terreno primero." else "⚠️ Registra un cultivo primero."
        Toast.makeText(this, msg, Toast.LENGTH_LONG).show()
    }

    private fun logout() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        with(sharedPref.edit()) {
            remove("USER_ID")
            apply()
        }
        startActivity(Intent(this, MainActivity::class.java).apply { flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK })
        finish()
    }
}
