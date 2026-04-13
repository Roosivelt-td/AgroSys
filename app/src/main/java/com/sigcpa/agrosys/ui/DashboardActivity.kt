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
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.sigcpa.agrosys.MainActivity
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.databinding.ActivityDashboardBinding
import com.sigcpa.agrosys.network.GeocodingService
import com.sigcpa.agrosys.ui.viewModel.WeatherViewModel
import kotlinx.coroutines.launch
import java.util.Calendar

class DashboardActivity : AppCompatActivity() {
    private lateinit var binding: ActivityDashboardBinding
    private lateinit var weatherViewModel: WeatherViewModel
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private lateinit var geocodingService: GeocodingService
    private val db by lazy { AppDatabase.getDatabase(this) }

    private var hasTerrenos = false
    private var hasCultivos = false
    
    private var currentLat: Double = 0.0
    private var currentLon: Double = 0.0

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

        weatherViewModel = ViewModelProvider(this)[WeatherViewModel::class.java]
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        geocodingService = GeocodingService(this)

        actualizarSaludo()
        loadWeatherWithLocation()
        setupUI(intent.getStringExtra("USER_NAME") ?: "Usuario", intent.getStringExtra("USER_ROLE") ?: "agricultor")
        observeWeatherData()
    }

    override fun onResume() {
        super.onResume()
        updateStatsAndLocks()
        actualizarSaludo()
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
        binding.btnProfile.setOnClickListener { logout() }

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
