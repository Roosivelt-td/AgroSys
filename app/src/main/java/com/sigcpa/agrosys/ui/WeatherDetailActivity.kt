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
        binding.tvHumidityDetail.text = "Humedad: ${weather.main.humidity}%"
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
                val timeLabel = when(i) { 0 -> "ahora"; 2 -> "dentro de +6h"; else -> "dentro de +12h" }
                val fullType = mapWeatherType(item.weather[0].main, item.weather[0].description, item.main.temp)
                val stateShort = fullType.split(" ").first()
                addStatusCard("$stateShort - $timeLabel", item)
            }
        }

        // 2. 6 PICOS TÉRMICOS INTELIGENTES
        val absMax = list.maxByOrNull { it.main.tempMax }
        val absMin = list.minByOrNull { it.main.tempMin }
        val averageTemp = list.map { it.main.temp }.average()

        // Calor fuerte
        absMax?.takeIf { it.main.tempMax > 28 }?.let { addExtremeCard("Día de Calor Fuerte", it, "#ef4444", "🔥") }
        // Media (Ambiente Estable)
        list.minByOrNull { abs(it.main.temp - averageTemp) }?.let { addExtremeCard("Clima Estable", it, "#f97316", "☀️") }
        // Templado
        list.find { it.main.temp in 19.0..24.0 }?.let { addExtremeCard("Día Templado", it, "#fbbf24", "🍃") }
        // Ambiente Frío
        list.find { it.main.temp in 8.0..14.0 }?.let { addExtremeCard("Ambiente Frío", it, "#3b82f6", "❄️") }
        // Riesgo Helada
        absMin?.takeIf { it.main.tempMin < 5 }?.let { addExtremeCard("Riesgo de Helada", it, "#06b6d4", "🧊") }
        // Frío Extremo
        absMin?.takeIf { it.main.tempMin < 1 }?.let { addExtremeCard("Peligro: Frío Extremo", it, "#1e1b4b", "🥶") }

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
            radius = 24f; setCardBackgroundColor(Color.parseColor("#1e293b"))
        }
        val layout = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; gravity = Gravity.CENTER; setPadding(16, 16, 16, 16) }
        val iconImg = ImageView(this).apply { layoutParams = LinearLayout.LayoutParams(60, 60) }
        Glide.with(this).load(weatherViewModel.getWeatherIconUrl(item.weather[0].icon)).into(iconImg)
        layout.addView(iconImg)
        layout.addView(TextView(this).apply { text = "${item.main.temp.toInt()}°"; setTextColor(Color.WHITE); setTypeface(null, android.graphics.Typeface.BOLD) })
        layout.addView(TextView(this).apply { text = label; setTextColor(Color.parseColor("#94a3b8")); textSize = 7f; gravity = Gravity.CENTER })
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
        val card = com.google.android.material.card.MaterialCardView(this).apply {
            layoutParams = LinearLayout.LayoutParams(-1, -2).apply { setMargins(0, 0, 0, 16) }
            radius = 32f; setCardBackgroundColor(Color.parseColor("#1e293b"))
        }
        val rel = android.widget.RelativeLayout(this).apply { setPadding(32, 32, 32, 32) }
        val iconImg = ImageView(this).apply { id = View.generateViewId(); layoutParams = android.widget.RelativeLayout.LayoutParams(100, 100).apply { addRule(android.widget.RelativeLayout.CENTER_VERTICAL) } }
        Glide.with(this).load(weatherViewModel.getWeatherIconUrl(item.weather[0].icon)).into(iconImg)
        val texts = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            layoutParams = android.widget.RelativeLayout.LayoutParams(-2, -2).apply { addRule(android.widget.RelativeLayout.END_OF, iconImg.id); marginStart = 24 }
            addView(TextView(this@WeatherDetailActivity).apply { text = label; setTextColor(Color.WHITE); setTypeface(null, android.graphics.Typeface.BOLD) })
            
            val probStr = if (pop > 0) " (${(pop * 100).toInt()}%)" else ""
            addView(TextView(this@WeatherDetailActivity).apply { text = mapWeatherType(item.weather[0].main, item.weather[0].description, item.main.temp) + probStr; setTextColor(Color.parseColor("#94a3b8")); textSize = 12f })
        }
        val temps = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL; gravity = Gravity.END
            layoutParams = android.widget.RelativeLayout.LayoutParams(-2, -2).apply { addRule(android.widget.RelativeLayout.ALIGN_PARENT_END) }
            addView(TextView(this@WeatherDetailActivity).apply { text = "${max.toInt()}° / ${min.toInt()}°"; setTextColor(Color.WHITE); setTypeface(null, android.graphics.Typeface.BOLD) })
            addView(TextView(this@WeatherDetailActivity).apply { text = if (pop > 0.4) "⚠️ Lluvia" else if (max > 25) "⚠️ Estrés" else "🌱 Óptimo"; setTextColor(if (pop > 0.4) Color.CYAN else if (max > 25) Color.YELLOW else Color.GREEN); textSize = 10f })
        }
        rel.addView(iconImg); rel.addView(texts); rel.addView(temps); card.addView(rel); binding.llDailyForecast.addView(card)
    }

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
            radius = 32f; setCardBackgroundColor(Color.parseColor("#1e293b"))
        }
        val layout = LinearLayout(this).apply { orientation = LinearLayout.VERTICAL; setPadding(20, 20, 20, 20); gravity = Gravity.CENTER }
        layout.addView(TextView(this).apply { text = weather.name; setTextColor(Color.WHITE); textSize = 10f; gravity = Gravity.CENTER; maxLines = 1; ellipsize = android.text.TextUtils.TruncateAt.END })
        val iconImg = ImageView(this).apply { layoutParams = LinearLayout.LayoutParams((40 * resources.displayMetrics.density).toInt(), (40 * resources.displayMetrics.density).toInt()) }
        Glide.with(this).load(weatherViewModel.getWeatherIconUrl(weather.weather[0].icon)).into(iconImg)
        layout.addView(iconImg)
        layout.addView(TextView(this).apply { text = "${weather.main.temp.toInt()}°C"; setTextColor(Color.WHITE); textSize = 14f; setTypeface(null, android.graphics.Typeface.BOLD) })
        val state = mapWeatherType(weather.weather[0].main, "", weather.main.temp).split(" ")[0]
        layout.addView(TextView(this).apply { text = state; setTextColor(Color.parseColor("#94a3b8")); textSize = 8f })
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
