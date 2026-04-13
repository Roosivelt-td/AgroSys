package com.sigcpa.agrosys.ui.viewModel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.sigcpa.agrosys.BuildConfig
import com.sigcpa.agrosys.models.WeatherResponse
import com.sigcpa.agrosys.network.ForecastResponse
import com.sigcpa.agrosys.network.LocationInfo
import com.sigcpa.agrosys.network.RetrofitInstance
import kotlinx.coroutines.launch

class WeatherViewModel : ViewModel() {
    private val _weatherData = MutableLiveData<WeatherResponse?>()
    val weatherData: LiveData<WeatherResponse?> = _weatherData

    private val _forecastData = MutableLiveData<ForecastResponse?>()
    val forecastData: LiveData<ForecastResponse?> = _forecastData

    private val _isLoading = MutableLiveData(false)
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    private val _locationName = MutableLiveData<String>()
    val locationName: LiveData<String> = _locationName

    private val _locationInfo = MutableLiveData<LocationInfo?>()
    val locationInfo: LiveData<LocationInfo?> = _locationInfo

    private val API_KEY = BuildConfig.OPEN_WEATHER_MAP_API_KEY

    fun fetchWeatherByCity(cityName: String) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            try {
                val response = RetrofitInstance.api.getCurrentWeather(cityName, API_KEY)
                if (response.isSuccessful) {
                    _weatherData.value = response.body()
                    _locationName.value = cityName
                } else {
                    _error.value = "Error: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun fetchWeatherByLocation(latitude: Double, longitude: Double, locationInfo: LocationInfo? = null) {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            try {
                // Fetch Current Weather
                val weatherRes = RetrofitInstance.api.getWeatherByLocation(latitude, longitude, API_KEY)
                if (weatherRes.isSuccessful) {
                    _weatherData.value = weatherRes.body()
                    if (locationInfo != null) {
                        _locationInfo.value = locationInfo
                        _locationName.value = locationInfo.nombreCompleto
                    } else {
                        _locationName.value = weatherRes.body()?.name ?: "Ubicación"
                    }
                }

                // Fetch Forecast
                val forecastRes = RetrofitInstance.api.getForecastByLocation(latitude, longitude, API_KEY)
                if (forecastRes.isSuccessful) {
                    _forecastData.value = forecastRes.body()
                }
            } catch (e: Exception) {
                _error.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun formatTemperature(temp: Double): String = "${temp.toInt()}°C"
    fun getWeatherIconUrl(iconCode: String): String = "https://openweathermap.org/img/w/${iconCode}.png"
    fun getWeatherDescription(description: String): String = description.replaceFirstChar { it.uppercase() }
}
