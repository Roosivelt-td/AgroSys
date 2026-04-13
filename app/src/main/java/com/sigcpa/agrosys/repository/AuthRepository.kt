package com.sigcpa.agrosys.repository

import android.content.Context
import android.util.Log
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.AgricultorEntity
import com.sigcpa.agrosys.database.entities.OrganizacionEntity
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class AuthRepository(context: Context) {
    private val db = AppDatabase.getDatabase(context)
    private val userDao = db.userDao()

    suspend fun checkEmailExists(email: String): Boolean = withContext(Dispatchers.IO) {
        userDao.countUsersByEmail(email) > 0
    }

    // Registro consolidado (Usado por el nuevo diseño de RegisterActivity)
    suspend fun registerFullUser(
        userData: Map<String, String>,
        role: String,
        orgData: Map<String, String>,
        extraData: Map<String, String>
    ): Int = withContext(Dispatchers.IO) {
        try {
            var orgId: Int? = null
            if (!orgData["name"].isNullOrBlank()) {
                val org = OrganizacionEntity(
                    nombre = orgData["name"]!!,
                    ruc = orgData["ruc"],
                    direccion = orgData["address"]
                )
                orgId = userDao.insertOrganizacion(org).toInt()
            }

            val usuario = UsuarioEntity(
                organizacion_id = orgId,
                nombre = userData["name"] ?: "",
                apellidos = userData["lastName"] ?: "",
                email = userData["email"] ?: "",
                password = userData["password"] ?: "",
                rol = role,
                telefono = userData["phone"]
            )
            val userId = userDao.insertUsuario(usuario).toInt()

            if (role == "agricultor") {
                val agricultor = AgricultorEntity(
                    usuario_id = userId,
                    dni = extraData["dni"] ?: "",
                    experiencia_anios = extraData["experience"]?.toIntOrNull() ?: 0,
                    nivel_educativo = extraData["education"]
                )
                userDao.insertAgricultor(agricultor)
            }
            userId
        } catch (e: Exception) {
            Log.e("AUTH_ERROR", "Error en registro: ${e.message}")
            -1
        }
    }

    // Función compatible con UserTypeActivity (para evitar errores de compilación)
    suspend fun completeProfile(
        userId: Int,
        role: String,
        orgData: Map<String, String>,
        extraData: Map<String, String>
    ): Boolean = withContext(Dispatchers.IO) {
        try {
            var orgId: Int? = null
            if (!orgData["name"].isNullOrBlank()) {
                val org = OrganizacionEntity(
                    nombre = orgData["name"]!!,
                    ruc = orgData["ruc"],
                    direccion = orgData["address"]
                )
                orgId = userDao.insertOrganizacion(org).toInt()
            }

            val usuario = userDao.getUsuarioById(userId)
            if (usuario != null) {
                val updatedUser = usuario.copy(
                    rol = role,
                    organizacion_id = orgId,
                    updated_at = System.currentTimeMillis() / 1000
                )
                userDao.updateUsuario(updatedUser)

                if (role == "agricultor") {
                    val agricultor = AgricultorEntity(
                        usuario_id = userId,
                        dni = extraData["dni"] ?: "",
                        experiencia_anios = extraData["experience"]?.toIntOrNull() ?: 0,
                        nivel_educativo = extraData["education"]
                    )
                    userDao.insertAgricultor(agricultor)
                }
                true
            } else false
        } catch (e: Exception) {
            false
        }
    }

    suspend fun getUserById(id: Int): UsuarioEntity? = withContext(Dispatchers.IO) {
        userDao.getUsuarioById(id)
    }

    suspend fun loginLocal(email: String, pass: String): UsuarioEntity? = withContext(Dispatchers.IO) {
        val user = userDao.getUsuarioByEmail(email)
        if (user != null && user.password == pass) user else null
    }
}
