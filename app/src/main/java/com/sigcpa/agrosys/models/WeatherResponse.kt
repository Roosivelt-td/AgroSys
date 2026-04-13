package com.sigcpa.agrosys.models

import com.google.gson.annotations.SerializedName

data class WeatherResponse(
    val name: String,
    val main: Main,
    val weather: List<Weather>,
    @SerializedName("dt")
    val timestamp: Long,
    @SerializedName("pop")
    val pop: Double? = 0.0, // Probabilidad de precipitación
    val wind: Wind?,
    val visibility: Int?
)

data class Main(
    val temp: Double,
    @SerializedName("feels_like")
    val feelsLike: Double,
    val humidity: Int,
    @SerializedName("temp_min")
    val tempMin: Double,
    @SerializedName("temp_max")
    val tempMax: Double,
    val pressure: Int?
)

data class Weather(
    val description: String,
    val icon: String,
    val main: String
)

data class Wind(
    val speed: Double,
    val deg: Int
)