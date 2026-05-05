package com.sigcpa.agrosys.ui

import android.graphics.Color
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.BuildConfig
import com.sigcpa.agrosys.databinding.ActivityWeatherDetailBinding
import com.sigcpa.agrosys.models.WeatherResponse
import com.sigcpa.agrosys.network.ForecastResponse
import com.sigcpa.agrosys.network.RetrofitInstance
import com.sigcpa.agrosys.ui.viewModel.WeatherViewModel
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.text.SimpleDateFormat
import java.util.*
import kotlin.math.*

class WeatherDetailActivity : AppCompatActivity() {
    private lateinit var binding: ActivityWeatherDetailBinding
    private lateinit var weatherViewModel: WeatherViewModel
    private val API_KEY = BuildConfig.OPEN_WEATHER_MAP_API_KEY

    // Ciudades regionales para cálculo de cercanía
    private val regionCities = listOf(
        CityCoord("Lima", -12.0464, -77.0428),
        CityCoord("Arequipa", -16.4090, -71.5375),
        CityCoord("Cusco", -13.5320, -71.9675),
        CityCoord("Trujillo", -8.1160, -79.0300),
        CityCoord("Ayacucho", -13.1588, -74.2239),
        CityCoord("Huancayo", -12.0651, -75.2049),
        CityCoord("Ica", -14.0678, -75.7286),
        CityCoord("Piura", -5.1945, -80.6328),
        CityCoord("Tarapoto", -6.4894, -76.3732),
        CityCoord("Puno", -15.8402, -70.0219)
    )

    data class CityCoord(val name: String, val lat: Double, val lon: Double)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityWeatherDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Insets
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        val initialHeaderPaddingTop = binding.headerContainer.paddingTop
        val initialPaddingTop = binding.scrollContainer.paddingTop
        val initialPaddingBottom = binding.scrollContainer.paddingBottom
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                initialHeaderPaddingTop + systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            binding.scrollContainer.setPadding(
                binding.scrollContainer.paddingLeft,
                initialPaddingTop,
                binding.scrollContainer.paddingRight,
                initialPaddingBottom + systemBars.bottom
            )
            insets
        }

        weatherViewModel = ViewModelProvider(this)[WeatherViewModel::class.java]
        setupObservers()

        binding.btnBack.setOnClickListener { finish() }

        val lat = intent.getDoubleExtra("LAT", 0.0)
        val lon = intent.getDoubleExtra("LON", 0.0)

        if (lat != 0.0 && lon != 0.0) {
            weatherViewModel.fetchWeatherByLocation(lat, lon, null)
            loadNearbyCitiesWeather(lat, lon)
        } else {
            weatherViewModel.fetchWeatherByCity("Ayacucho")
            loadNearbyCitiesWeather(-13.1588, -74.2239)
        }
        
        binding.root.setOnClickListener { finish() }
    }

    private fun setupObservers() {
        weatherViewModel.weatherData.observe(this) { it?.let { updateCurrentUI(it) } }
        weatherViewModel.forecastData.observe(this) { it?.let { updateForecastUI(it) } }
    }

    private fun updateCurrentUI(weather: WeatherResponse) {
        binding.tvCurrentTempDetail.text = "${weather.main.temp.toInt()}°"
        binding.tvCurrentDescDetail.text = mapWeatherType(weather.weather[0].main, weather.weather[0].description, weather.main.temp)
        
        binding.tvFeelsLike.text = "${weather.main.feelsLike.toInt()}°"
        binding.tvHumidityDetail.text = "${weather.main.humidity}%"
        binding.tvWindDetail.text = "${weather.wind?.speed?.toInt() ?: "--"} km/h"
        binding.tvVisibilityDetail.text = "${(weather.visibility?.div(1000) ?: "--")} km"

        Glide.with(this).load(weatherViewModel.getWeatherIconUrl(weather.weather[0].icon)).into(binding.ivWeatherMain)
        generate5Recommendations(weather)
    }

    private fun updateForecastUI(forecast: ForecastResponse) {
        binding.llWeatherStatusGrid.removeAllViews()
        binding.llExtremeTempsList.removeAllViews()
        binding.llDailyForecast.removeAllViews()

        val list = forecast.list

        // 1. ESTADO CADA 6h CON ETIQUETA (Estado - tiempo)
        val indices = listOf(0, 2, 4) // Corresponden a 0h, 6h, 12h aprox
        indices.forEach { i ->
            if (i < list.size) {
                val item = list[i]
                val timeLabel = when(i) { 0 -> "ahora"; 2 -> "+6h"; else -> "+12h" }
                addStatusCard(timeLabel, item)
            }
        }

        // 2. ALERTAS DE PICOS TÉRMICOS (En lugar de tarjetas separadas, una lista en texto)
        val absMax = list.maxByOrNull { it.main.tempMax }
        val absMin = list.minByOrNull { it.main.tempMin }
        val alerts = mutableListOf<String>()
        
        absMax?.takeIf { it.main.tempMax > 28 }?.let { alerts.add("Día de Calor Fuerte") }
        alerts.add("Clima Estable")
        alerts.add("Día Templado")
        absMin?.takeIf { it.main.tempMin < 5 }?.let { alerts.add("Riesgo de Helada") }
        
        binding.tvAlertasPicos.text = "Alertas de picos térmicos · ${alerts.joinToString(" · ")}"

        // 3. PRÓXIMOS DÍAS (Buscando datos del mediodía para precisión real)
        val dailyGroups = list.groupBy { 
            val cal = Calendar.getInstance().apply { timeInMillis = it.timestamp * 1000L }
            cal.get(Calendar.DAY_OF_YEAR)
        }.values.toList()

        dailyGroups.take(5).forEachIndexed { i, dayList ->
            val label = when(i) { 1 -> "Mañana"; 2 -> "Pasado mañana"; 3 -> "En 3 días"; 4 -> "Semana próxima"; else -> "" }
            if (i > 0 && label.isNotEmpty()) {
                val middayItem = dayList.minByOrNull { 
                    val cal = Calendar.getInstance().apply { timeInMillis = it.timestamp * 1000L }
                    abs(cal.get(Calendar.HOUR_OF_DAY) - 14) 
                } ?: dayList[0]
                
                val max = dayList.maxOf { it.main.tempMax }
                val min = dayList.minOf { it.main.tempMin }
                val probValue = middayItem.pop ?: 0.0
                
                addDailyCard(label, middayItem, max, min, probValue)
            }
        }
    }

    private fun mapWeatherType(main: String, desc: String, temp: Double? = null): String {
        val m = main.lowercase()
        val d = desc.lowercase()
        if (temp != null && temp > 30) return "Calor Intenso"
        return when {
            m.contains("thunderstorm") && d.contains("rain") -> "Tormenta + Lluvia"
            m.contains("thunderstorm") -> "Tormenta"
            m.contains("rain") || m.contains("drizzle") -> "Lluvioso"
            m.contains("fog") || m.contains("mist") || m.contains("haze") -> "Niebla"
            m.contains("clouds") -> "Nublado"
            m.contains("clear") -> "Soleado"
            d.contains("wind") || d.contains("gale") -> "Ventoso"
            else -> "Variado"
        }
    }

    private fun addStatusCard(label: String, item: WeatherResponse) {
        val card = com.google.android.material.card.MaterialCardView(this).apply {
            layoutParams = LinearLayout.LayoutParams(0, -2, 1f).apply { setMargins(8, 0, 8, 0) }
            radius = 44f; setCardBackgroundColor(Color.parseColor("#F4F9EF"))
            strokeWidth = 2; strokeColor = Color.parseColor("#DAE8D0")
        }
        val layout = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; gravity = Gravity.CENTER; setPadding(12, 24, 12, 24) }
        
        layout.addView(TextView(this).apply { 
            text = "${item.main.temp.toInt()}°"
            setTextColor(Color.parseColor("#BC6A32"))
            textSize = 24f
            setTypeface(null, android.graphics.Typeface.BOLD) 
        })
        
        val desc = mapWeatherType(item.weather[0].main, item.weather[0].description, item.main.temp).split(" ").first()
        layout.addView(TextView(this).apply { 
            text = desc
            setTextColor(Color.parseColor("#598055"))
            textSize = 10f
            setTypeface(null, android.graphics.Typeface.BOLD)
            gravity = Gravity.CENTER 
        })
        
        layout.addView(TextView(this).apply { 
            text = label
            setTextColor(Color.parseColor("#89A47A"))
            textSize = 9f
            setTypeface(null, android.graphics.Typeface.BOLD)
            gravity = Gravity.CENTER 
        })
        
        card.addView(layout); binding.llWeatherStatusGrid.addView(card)
    }

    private fun addExtremeCard(title: String, item: WeatherResponse, color: String, emoji: String) {
        val dateStr = SimpleDateFormat("EEE d MMM HH:mm", Locale("es", "PE")).format(Date(item.timestamp * 1000L))
        val card = com.google.android.material.card.MaterialCardView(this).apply {
            layoutParams = LinearLayout.LayoutParams(350, -2).apply { setMargins(8, 0, 8, 0) }
            radius = 32f; setCardBackgroundColor(Color.parseColor(color))
        }
        val layout = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(20, 20, 20, 20); gravity = Gravity.CENTER }
        layout.addView(TextView(this).apply { text = emoji; textSize = 18f })
        layout.addView(TextView(this).apply { text = "${item.main.temp.toInt()}°"; setTextColor(Color.WHITE); textSize = 20f; setTypeface(null, android.graphics.Typeface.BOLD) })
        layout.addView(TextView(this).apply { text = title; setTextColor(Color.WHITE); textSize = 10f; gravity = Gravity.CENTER; setTypeface(null, android.graphics.Typeface.BOLD) })
        layout.addView(TextView(this).apply { text = "Para el $dateStr"; setTextColor(Color.parseColor("#e2e8f0")); textSize = 7f; gravity = Gravity.CENTER })
        card.addView(layout); binding.llExtremeTempsList.addView(card)
    }

    private fun addDailyCard(label: String, item: WeatherResponse, max: Double, min: Double, pop: Double) {
        val layout = LinearLayout(this).apply {
            orientation = LinearLayout.HORIZONTAL
            gravity = Gravity.CENTER_VERTICAL
            setPadding(0, 20, 0, 20)
        }
        
        // Divider view logic is handled by parent's divider or manual border if needed
        // but for list style we just add a divider at the end if not the last one
        
        val tvLabel = TextView(this).apply {
            layoutParams = LinearLayout.LayoutParams(0, -2, 1.2f)
            text = label
            setTextColor(Color.parseColor("#3A6235"))
            setTypeface(null, android.graphics.Typeface.BOLD)
            textSize = 14f
        }
        
        val iconEmoji = when(item.weather[0].main.lowercase()) {
            "rain", "drizzle" -> "🌧️"
            "clouds" -> "🌥️"
            "clear" -> "☀️"
            else -> "🌥️"
        }
        val probStr = if (pop > 0.1) " (${(pop * 100).toInt()}%)" else ""
        val tvClima = TextView(this).apply {
            layoutParams = LinearLayout.LayoutParams(0, -2, 1.5f)
            text = "$iconEmoji " + mapWeatherType(item.weather[0].main, item.weather[0].description, item.main.temp) + probStr
            setTextColor(Color.parseColor("#608A52"))
            textSize = 12f
        }
        
        val tvTemp = TextView(this).apply {
            layoutParams = LinearLayout.LayoutParams(wrapContent, wrapContent)
            text = "${max.toInt()}°/${min.toInt()}°"
            setTextColor(Color.parseColor("#3D7037"))
            setPadding(16, 4, 16, 4)
            background = getRoundedDrawable("#EEF6E8", 40f)
            textSize = 12f
            setTypeface(null, android.graphics.Typeface.BOLD)
        }
        
        val tvBadge = TextView(this).apply {
            val lp = LinearLayout.LayoutParams(wrapContent, wrapContent)
            lp.marginStart = 16
            layoutParams = lp
            text = if (pop > 0.4) "Lluvia" else if (max > 25) "Estrés" else "Óptimo"
            setPadding(24, 8, 24, 8)
            background = getRoundedDrawable("#C8E0B5", 60f)
            setTextColor(Color.parseColor("#2C6B26"))
            textSize = 11f
            setTypeface(null, android.graphics.Typeface.BOLD)
        }
        
        layout.addView(tvLabel)
        layout.addView(tvClima)
        layout.addView(tvTemp)
        layout.addView(tvBadge)
        
        binding.llDailyForecast.addView(layout)
        
        // Add divider
        val divider = View(this).apply {
            layoutParams = LinearLayout.LayoutParams(-1, 2)
            setBackgroundColor(Color.parseColor("#E3EDD9"))
        }
        binding.llDailyForecast.addView(divider)
    }

    private fun getRoundedDrawable(color: String, radius: Float): android.graphics.drawable.GradientDrawable {
        return android.graphics.drawable.GradientDrawable().apply {
            shape = android.graphics.drawable.GradientDrawable.RECTANGLE
            cornerRadius = radius
            setColor(Color.parseColor(color))
        }
    }
    
    private val wrapContent = LinearLayout.LayoutParams.WRAP_CONTENT


    private fun loadNearbyCitiesWeather(lat: Double, lon: Double) {
        binding.llRegionalForecast.removeAllViews()
        val nearby = regionCities.sortedBy { calculateDistance(lat, lon, it.lat, it.lon) }.take(3)
        
        lifecycleScope.launch {
            nearby.forEach { city ->
                try {
                    val response = withContext(Dispatchers.IO) { RetrofitInstance.api.getWeatherByLocation(city.lat, city.lon, API_KEY) }
                    if (response.isSuccessful) response.body()?.let { addRegionalCard(it) }
                } catch (e: Exception) { e.printStackTrace() }
            }
        }
    }

    private fun addRegionalCard(weather: WeatherResponse) {
        val card = com.google.android.material.card.MaterialCardView(this).apply {
            layoutParams = LinearLayout.LayoutParams(0, -2, 1f).apply { setMargins(8, 0, 8, 0) }
            radius = 48f; setCardBackgroundColor(Color.parseColor("#EBF3E5"))
            strokeWidth = 2; strokeColor = Color.parseColor("#D3E2C6")
        }
        val layout = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(16, 24, 16, 24); gravity = Gravity.CENTER }
        layout.addView(TextView(this).apply { text = weather.name; setTextColor(Color.parseColor("#3F6B3A")); textSize = 12f; gravity = Gravity.CENTER; maxLines = 1; ellipsize = android.text.TextUtils.TruncateAt.END; setTypeface(null, android.graphics.Typeface.BOLD) })
        
        layout.addView(TextView(this).apply { text = "${weather.main.temp.toInt()}°C"; setTextColor(Color.parseColor("#C16232")); textSize = 18f; setTypeface(null, android.graphics.Typeface.BOLD) })
        
        val state = mapWeatherType(weather.weather[0].main, "", weather.main.temp).split(" ")[0]
        layout.addView(TextView(this).apply { text = state; setTextColor(Color.parseColor("#71935F")); textSize = 10f; setTypeface(null, android.graphics.Typeface.BOLD) })
        card.addView(layout); binding.llRegionalForecast.addView(card)
    }

    private fun generate5Recommendations(weather: WeatherResponse) {
        val t = weather.main.temp; val c = weather.weather[0].main.lowercase(); val h = weather.main.humidity
        val list = mutableListOf<String>()
        list.add(if (c.contains("rain")) "🌧️ Lluvia detectada: Suspende aplicaciones foliares hoy." else "✅ Clima seco: Buen momento para fumigación preventiva.")
        list.add(if (t > 28) "💧 Alerta Riego: Aumenta frecuencia en la madrugada." else if (h > 80) "💧 Suelo saturado: Evita el riego para prevenir pudrición." else "🌱 Mantén el riego regular según etapa del cultivo.")
        list.add(if (h > 75) "🍄 Riesgo de Hongos: Revisa el envés de las hojas hoy." else "🦗 Plagas: Calor seco favorece ácaros, realiza monitoreos.")
        list.add(if (c.contains("rain")) "🚜 Suelo blando: No entres con tractor para no compactar." else "🛠️ Suelo firme: Ideal para aporque o limpieza de surcos.")
        list.add(if (t < 12) "❄️ Alerta Helada: Protege almácigos con coberturas plásticas." else "🍎 Cosecha: Recolecta temprano para evitar mermas por sol.")

        binding.tvRec1.text = list[0]; binding.tvRec2.text = list[1]; binding.tvRec3.text = list[2]; binding.tvRec4.text = list[3]; binding.tvRec5.text = list[4]
    }

    private fun calculateDistance(lat1: Double, lon1: Double, lat2: Double, lon2: Double): Double {
        val r = 6371.0; val dLat = Math.toRadians(lat2 - lat1); val dLon = Math.toRadians(lon2 - lon1)
        val a = sin(dLat / 2).pow(2) + cos(Math.toRadians(lat1)) * cos(Math.toRadians(lat2)) * sin(dLon / 2).pow(2)
        return 2 * r * atan2(sqrt(a), sqrt(1 - a))
    }
}
