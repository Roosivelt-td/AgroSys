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
import android.view.View
import android.widget.PopupWindow
import android.widget.LinearLayout
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import com.sigcpa.agrosys.database.entities.RolEntity
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

    private var currentUser: UsuarioEntity? = null
    private var currentRol: RolEntity? = null

    private val importJsonLauncher = registerForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        uri?.let { importCatalog(it) }
    }

    private val requestPermissionLauncher = registerForActivityResult(ActivityResultContracts.RequestPermission()) { isGranted ->
        if (isGranted) getLocationAndWeather()
        else weatherViewModel.fetchWeatherByCity(getString(R.string.default_city))
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDashboardBinding.inflate(layoutInflater)
        setContentView(binding.root)

        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        weatherViewModel = ViewModelProvider(this)[WeatherViewModel::class.java]
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        geocodingService = GeocodingService(this)

        setupInsets()
        actualizarSaludo()
        loadWeatherWithLocation()
        observeWeatherData()
        
        loadUserData()
    }

    private fun setupInsets() {
        val initialBottomPadding = binding.bottomNav.paddingBottom
        ViewCompat.setOnApplyWindowInsetsListener(binding.bottomNav) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, initialBottomPadding + systemBars.bottom)
            insets
        }

        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            val header = binding.root.findViewById<LinearLayout>(R.id.header_container)
            header?.setPadding(header.paddingLeft, systemBars.top, header.paddingRight, header.paddingBottom)
            insets
        }
    }

    private fun loadUserData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        
        lifecycleScope.launch {
            val user = db.userDao().getUsuarioById(userId)
            currentUser = user
            
            // Buscar el rol global del usuario
            val rol = user?.let { db.userDao().getRolById(it.rol_global_id) }
            currentRol = rol
            
            updatePermissionsUI(user, rol)
            setupStaticUI(user, rol)
            loadProfileImage(user)
        }
    }

    private fun updatePermissionsUI(user: UsuarioEntity?, rol: RolEntity?) {
        val nivel = rol?.nivel ?: 1
        val isAdmin = nivel >= 3 // Admin (3) o Super Admin (4)
        val isSupervisor = nivel >= 2
        val isAgricultor = nivel >= 1

        // Panel de Administración
        binding.cvAdminPanel.visibility = if (isAdmin) View.VISIBLE else View.GONE
        if (isAdmin) {
            binding.btnManageCatalogs.setOnClickListener { showAddCatalogDialog() }
            binding.btnManageUsers.setOnClickListener {
                val intent = Intent(this, ManageMembersActivity::class.java)
                // Buscamos la primera organización del usuario
                lifecycleScope.launch {
                    val org = db.userDao().getOrganizacionActivaByCreador(user?.id ?: 0)
                    intent.putExtra("ORG_ID", org?.id ?: 1)
                    startActivity(intent)
                }
            }
        }

        // Visibilidad según rol
        val agricultureVisibility = if (isAgricultor) View.VISIBLE else View.GONE
        binding.navTerrenoQuick.visibility = agricultureVisibility
        binding.navCultivoQuick.visibility = agricultureVisibility
        binding.navLaborQuick.visibility = agricultureVisibility
        binding.navCosechaQuick.visibility = agricultureVisibility
        
        binding.navTerrenos.visibility = agricultureVisibility
        binding.navCultivos.visibility = agricultureVisibility
        binding.navLabores.visibility = agricultureVisibility

        // Información de Header
        user?.let {
            binding.tvWelcomeUser.text = it.nombre
            binding.tvUserInfoLine.text = rol?.nombre?.replaceFirstChar { c -> c.uppercase() } ?: "Agricultor"
        }
    }

    private fun setupStaticUI(user: UsuarioEntity?, rol: RolEntity?) {
        binding.ivUserAvatar.setOnClickListener { showUserMenu() }
        binding.btnProfileCard.setOnClickListener { showUserMenu() }

        binding.cvWeatherCard.setOnClickListener {
            startActivity(Intent(this, WeatherDetailActivity::class.java).apply {
                putExtra("CITY_NAME", binding.tvCityName.text.toString())
                putExtra("LAT", currentLat)
                putExtra("LON", currentLon)
            })
        }

        binding.navTerrenos.setOnClickListener { startActivity(Intent(this, TerrenosListActivity::class.java)) }
        binding.navTerrenoQuick.setOnClickListener { startActivity(Intent(this, TerrenosListActivity::class.java)) }
        
        binding.navCultivos.setOnClickListener {
            if (hasTerrenos) startActivity(Intent(this, CultivosListActivity::class.java))
            else showLockMessage("terreno")
        }

        binding.navLabores.setOnClickListener {
            if (hasCultivos) startActivity(Intent(this, LaboresListActivity::class.java))
            else showLockMessage("cultivo")
        }

        binding.navReportes.setOnClickListener {
            Toast.makeText(this, getString(R.string.msg_reportes_dev), Toast.LENGTH_SHORT).show()
        }
    }

    private fun loadProfileImage(user: UsuarioEntity?) {
        user?.foto_perfil_url?.let { path ->
            val file = File(path)
            if (file.exists()) {
                Glide.with(this).load(file).circleCrop()
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
                    .into(binding.ivUserAvatar)
                binding.ivUserAvatar.imageTintList = null
                binding.ivUserAvatar.setPadding(0, 0, 0, 0)
            }
        }
    }

    private fun showAddCatalogDialog() {
        val dialogBinding = DialogAddCatalogoCultivoBinding.inflate(layoutInflater)
        val dialog = androidx.appcompat.app.AlertDialog.Builder(this)
            .setView(dialogBinding.root).create()
        dialog.window?.setBackgroundDrawableResource(android.R.color.transparent)

        dialogBinding.btnImportJson.setOnClickListener {
            importJsonLauncher.launch("application/json")
            dialog.dismiss()
        }
        dialogBinding.btnSave.setOnClickListener {
            val nombre = dialogBinding.etCatNombre.text.toString().trim()
            val cientifico = dialogBinding.etCatCientifico.text.toString().trim()
            val ciclo = dialogBinding.etCatCiclo.text.toString().trim()

            if (nombre.isNotEmpty()) {
                lifecycleScope.launch {
                    val nuevo = CatalogoCultivoEntity(
                        nombre = nombre,
                        nombre_cientifico = if (cientifico.isEmpty()) null else cientifico,
                        tipo_ciclo = if (ciclo.isEmpty()) "ciclo_corto" else ciclo,
                        vida_util_estimada_meses = if (ciclo.lowercase().contains("perenne")) 60 else null,
                        dias_a_cosecha_promedio = dialogBinding.etCatDias.text.toString().toIntOrNull(),
                        instrucciones_base_riego = null,
                        instrucciones_base_plagas = null
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
            if (result.isSuccess) Toast.makeText(this@DashboardActivity, "✅ Importados", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onResume() {
        super.onResume()
        updateStatsAndLocks()
        actualizarSaludo()
        loadUserData()
    }

    private fun updateStatsAndLocks() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        lifecycleScope.launch {
            val user = db.userDao().getUsuarioById(userId)
            user?.let {
                val numTerrenos = db.assetDao().countTerrenosByAgricultor(it.id)
                val cultivos = db.assetDao().getCultivosByAgricultor(it.id)
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
    }

    private fun showLockMessage(type: String) {
        val msg = if (type == "terreno") "⚠️ Registra un terreno primero." else "⚠️ Registra un cultivo primero."
        Toast.makeText(this, msg, Toast.LENGTH_LONG).show()
    }

    private fun showUserMenu() {
        val popupBinding = DialogUserMenuBinding.inflate(layoutInflater)
        val popupWindow = PopupWindow(popupBinding.root, LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT, true)
        popupWindow.elevation = 20f
        popupBinding.btnLogout.setOnClickListener { logout() }
        popupWindow.showAsDropDown(binding.ivUserAvatar, 0, 10)
    }

    private fun logout() {
        getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE).edit().remove("USER_ID").apply()
        startActivity(Intent(this, MainActivity::class.java).apply { flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK })
        finish()
    }

    private fun loadWeatherWithLocation() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) getLocationAndWeather()
        else requestPermissionLauncher.launch(Manifest.permission.ACCESS_FINE_LOCATION)
    }

    private fun getLocationAndWeather() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) return
        fusedLocationClient.lastLocation.addOnSuccessListener { location: Location? ->
            location?.let {
                currentLat = it.latitude
                currentLon = it.longitude
                lifecycleScope.launch {
                    val info = geocodingService.getLocationInfo(it.latitude, it.longitude)
                    weatherViewModel.fetchWeatherByLocation(it.latitude, it.longitude, info)
                }
            } ?: weatherViewModel.fetchWeatherByCity(getString(R.string.default_city))
        }
    }

    private fun observeWeatherData() {
        weatherViewModel.weatherData.observe(this) { weather ->
            weather?.let {
                binding.tvTemperature.text = weatherViewModel.formatTemperature(it.main.temp)
                binding.tvWeatherDescription.text = weatherViewModel.getWeatherDescription(it.weather.firstOrNull()?.description ?: "")
                Glide.with(this).load(weatherViewModel.getWeatherIconUrl(it.weather.firstOrNull()?.icon ?: "01d")).into(binding.ivWeatherIcon)
            }
        }
        weatherViewModel.locationName.observe(this) { binding.tvCityName.text = it }
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
}
