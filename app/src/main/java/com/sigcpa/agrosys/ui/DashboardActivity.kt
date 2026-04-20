package com.sigcpa.agrosys.ui

import android.Manifest
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import java.io.File
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.sigcpa.agrosys.MainActivity
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import com.sigcpa.agrosys.databinding.ActivityDashboardBinding
import com.sigcpa.agrosys.databinding.DialogAddCatalogoCultivoBinding
import com.sigcpa.agrosys.databinding.DialogUserMenuBinding
import android.view.LayoutInflater
import android.widget.PopupWindow
import android.widget.LinearLayout
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.network.GeocodingService
import com.sigcpa.agrosys.ui.viewModel.WeatherViewModel
import com.sigcpa.agrosys.utils.CatalogImporter
import kotlinx.coroutines.launch
import java.util.Calendar

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

    private var userName: String = ""
    private var userRole: String = ""
    private var currentUser: com.sigcpa.agrosys.database.entities.UsuarioEntity? = null

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
            Toast.makeText(this, "Para clima exacto, activa la ubicación", Toast.LENGTH_LONG).show()
            weatherViewModel.fetchWeatherByCity("Ayacucho")
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDashboardBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        // SOLUCIÓN: Ajustar el menú inferior para que no lo tapen los botones del sistema (triángulo/círculo)
        val initialBottomPadding = binding.bottomNav.paddingBottom
        ViewCompat.setOnApplyWindowInsetsListener(binding.bottomNav) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, initialBottomPadding + systemBars.bottom)
            insets
        }

        // También ajustamos el Header para que respete la barra de estado superior
        val initialHeaderTopPadding = binding.root.findViewById<LinearLayout>(R.id.header_container)?.paddingTop ?: 0
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            val header = binding.root.findViewById<LinearLayout>(R.id.header_container)
            header?.setPadding(header.paddingLeft, initialHeaderTopPadding + systemBars.top, header.paddingRight, header.paddingBottom)
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
        observeWeatherData()
        checkAdminAccess(userRole)
    }

    private fun checkAdminAccess(role: String) {
        if (role == "admin" || role == "administrador" || role == "supervisor") {
            binding.cvAdminPanel.visibility = android.view.View.VISIBLE
            binding.btnManageCatalogs.setOnClickListener {
                showAddCatalogDialog()
            }
            binding.btnManageUsers.setOnClickListener {
                Toast.makeText(this, "Gestión de Usuarios habilitada", Toast.LENGTH_SHORT).show()
            }
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
                    Toast.makeText(this@DashboardActivity, "✅ $nombre añadido", Toast.LENGTH_SHORT).show()
                    dialog.dismiss()
                }
            } else {
                Toast.makeText(this, "⚠️ El nombre es obligatorio", Toast.LENGTH_SHORT).show()
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
            user?.let {
                binding.tvWelcomeUser.text = it.nombre
                binding.tvUserInfoLine.text = it.rol.replaceFirstChar { char -> char.uppercase() }
                
                // Cargar foto de perfil
                it.foto_perfil_url?.let { path ->
                    val file = java.io.File(path)
                    if (file.exists()) {
                        Glide.with(this@DashboardActivity)
                            .load(file)
                            .circleCrop()
                            .diskCacheStrategy(com.bumptech.glide.load.engine.DiskCacheStrategy.NONE)
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
            weatherViewModel.fetchWeatherByCity("Ayacucho")
            return
        }

        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) return

        binding.progressWeather.visibility = android.view.View.VISIBLE
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
                weatherViewModel.fetchWeatherByCity("Ayacucho")
            }
        }.addOnFailureListener {
            weatherViewModel.fetchWeatherByCity("Ayacucho")
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
            if (!isLoading) binding.progressWeather.visibility = android.view.View.GONE
        }
    }

    private fun updateWeatherUI(weather: com.sigcpa.agrosys.models.WeatherResponse) {
        binding.tvTemperature.text = weatherViewModel.formatTemperature(weather.main.temp)
        binding.tvWeatherDescription.text = weatherViewModel.getWeatherDescription(weather.weather.firstOrNull()?.description ?: "")
        Glide.with(this).load(weatherViewModel.getWeatherIconUrl(weather.weather.firstOrNull()?.icon ?: "01d")).into(binding.ivWeatherIcon)
    }

    private fun actualizarSaludo() {
        val hora = Calendar.getInstance().get(Calendar.HOUR_OF_DAY)
        val (saludo, icono) = when (hora) {
            in 0..5 -> "Buenas noches" to "🌙"
            in 6..11 -> "Buenos días" to "🌅"
            in 12..18 -> "Buenas tardes" to "🌤️"
            else -> "Buenas noches" to "🌙"
        }
        binding.saludoTexto.text = saludo
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
                Toast.makeText(this, "Selecciona un cultivo para cosechar", Toast.LENGTH_SHORT).show()
            } else {
                showLockMessage("cultivo")
            }
        }

        binding.navReportes.setOnClickListener {
            Toast.makeText(this, "Módulo de Reportes próximamente", Toast.LENGTH_SHORT).show()
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
            val fotoUrl = user?.foto_perfil_url
            
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
            val agricultor = db.userDao().getAgricultorByUserId(userId)
            if (agricultor != null) {
                val numTerrenos = db.assetDao().countTerrenosByAgricultor(agricultor.id)
                val cultivos = db.assetDao().getCultivosByAgricultor(agricultor.id)
                hasTerrenos = numTerrenos > 0
                hasCultivos = cultivos.isNotEmpty()
                binding.tvStatTerrenos.text = numTerrenos.toString()
                binding.tvStatCultivos.text = cultivos.size.toString()
                applyLockStyles()
            }
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
