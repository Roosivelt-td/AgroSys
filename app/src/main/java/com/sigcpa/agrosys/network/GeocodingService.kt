package com.sigcpa.agrosys.network

import android.content.Context
import android.location.Address
import android.location.Geocoder
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.util.Locale

data class LocationInfo(
    val localidad: String = "",
    val distrito: String = "",
    val provincia: String = "",
    val ciudad: String = "",
    val pais: String = "",
    val nombreCompleto: String = ""
)

class GeocodingService(private val context: Context) {

    suspend fun getLocationInfo(latitude: Double, longitude: Double): LocationInfo? =
        withContext(Dispatchers.IO) {
            try {
                val geocoder = Geocoder(context, Locale("es", "PE"))
                val addresses: List<Address>? = geocoder.getFromLocation(latitude, longitude, 1)

                addresses?.firstOrNull()?.let { address ->
                    // Obtener cada componente de la dirección
                    val localidad = address.locality ?: ""                    // Distrito/Ciudad pequeña
                    val subAdminArea = address.subAdminArea ?: ""             // Provincia
                    val adminArea = address.adminArea ?: ""                   // Departamento/Región
                    val pais = address.countryName ?: "Perú"
                    val featureName = address.featureName ?: ""               // Nombre del lugar específico

                    // Construir nombre completo jerárquico
                    val nombreCompleto = buildString {
                        if (featureName.isNotEmpty() && featureName != localidad) {
                            append(featureName)
                            if (localidad.isNotEmpty()) append(", ")
                        }
                        if (localidad.isNotEmpty()) {
                            append(localidad)
                            if (subAdminArea.isNotEmpty()) append(", ")
                        }
                        if (subAdminArea.isNotEmpty()) {
                            append(subAdminArea)
                            if (adminArea.isNotEmpty() && adminArea != subAdminArea) append(", ")
                        }
                        if (adminArea.isNotEmpty() && adminArea != subAdminArea) {
                            append(adminArea)
                        }
                        if (pais.isNotEmpty()) append(", $pais")
                    }

                    LocationInfo(
                        localidad = localidad,
                        distrito = localidad,  // En Perú, locality suele ser el distrito
                        provincia = subAdminArea,
                        ciudad = if (adminArea.isNotEmpty()) adminArea else localidad,
                        pais = pais,
                        nombreCompleto = nombreCompleto
                    )
                }
            } catch (e: Exception) {
                e.printStackTrace()
                null
            }
        }

    // Método antiguo para compatibilidad
    suspend fun getCityNameFromLocation(latitude: Double, longitude: Double): String? {
        return getLocationInfo(latitude, longitude)?.nombreCompleto
    }
}