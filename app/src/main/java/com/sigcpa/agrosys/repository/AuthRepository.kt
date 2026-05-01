package com.sigcpa.agrosys.repository

import android.content.Context
import android.util.Log
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.OrganizacionEntity
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import com.sigcpa.agrosys.database.entities.SolicitudUsuarioEntity
import com.sigcpa.agrosys.database.dao.UsuarioWithRol
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class AuthRepository(context: Context) {
    private val db = AppDatabase.getDatabase(context)
    private val userDao = db.userDao()

    suspend fun checkEmailExists(email: String): Boolean = withContext(Dispatchers.IO) {
        userDao.countUsersByEmail(email) > 0
    }

    suspend fun registerUser(
        userData: Map<String, String>,
        orgId: Int? = null,
        requestSupervisor: Boolean = false
    ): Int = withContext(Dispatchers.IO) {
        try {
            // Siempre buscar el rol de agricultor ("usuario")
            val rolName = "usuario"
            val rol = userDao.getRolByName(rolName)
            
            // Si el rol no existe (error de base de datos), no permitimos el registro
            if (rol == null) {
                Log.e("AUTH_ERROR", "Error crítico: El rol '$rolName' no existe en la base de datos.")
                return@withContext -1
            }
            
            val usuario = UsuarioEntity(
                rol_id = rol.id, // Ahora es seguro porque validamos el null arriba
                nombre = userData["name"] ?: "",
                apellidos = userData["lastName"] ?: "",
                email = userData["email"] ?: "",
                password = userData["password"] ?: "",
                dni = userData["dni"],
                experiencia_anios = userData["experience"]?.toIntOrNull() ?: 0,
                nivel_educativo = userData["education"],
                telefono = userData["phone"],
                es_admin_puro = false, // SEGURIDAD: Nadie entra como admin puro
                organizacion_aprobada = true 
            )
            
            val userId = userDao.insertUsuario(usuario).toInt()
            Log.d("AUTH_SUCCESS", "Usuario registrado: $userId con Rol ID: ${rol.id}")

            if (orgId != null) {
                val tipoSolicitud = if (requestSupervisor) "ascenso_supervisor" else "unirse"
                val solicitud = SolicitudUsuarioEntity(
                    usuario_id = userId,
                    organizacion_id = orgId,
                    tipo_solicitud = tipoSolicitud,
                    mensaje = if (requestSupervisor) "Deseo unirme como supervisor" else "Deseo unirme a la organización"
                )
                userDao.insertSolicitud(solicitud)
            }
            
            userId
        } catch (e: Exception) {
            Log.e("AUTH_ERROR", "Error en registro: ${e.message}")
            -1
        }
    }

    suspend fun getAllOrganizaciones(): List<OrganizacionEntity> = withContext(Dispatchers.IO) {
        userDao.getAllOrganizaciones()
    }

    suspend fun getUserById(id: Int): UsuarioWithRol? = withContext(Dispatchers.IO) {
        userDao.getUsuarioById(id)
    }

    suspend fun loginLocal(email: String, pass: String): UsuarioWithRol? = withContext(Dispatchers.IO) {
        val userWithRol = userDao.getUsuarioByEmail(email)
        if (userWithRol != null && userWithRol.usuario.password == pass) {
            userWithRol
        } else {
            null
        }
    }
}
