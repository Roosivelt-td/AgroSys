package com.sigcpa.agrosys.repository

import android.content.Context
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class AuthRepository(context: Context) {
    private val db = AppDatabase.getDatabase(context)
    private val userDao = db.userDao()

    suspend fun checkEmailExists(email: String): Boolean = withContext(Dispatchers.IO) {
        userDao.getUsuarioByEmail(email) != null
    }

    suspend fun registerFullUser(
        userData: Map<String, String>,
        roleName: String,
        orgId: Int? = null,
        orgData: Map<String, String>? = null
    ): Int = withContext(Dispatchers.IO) {
        // 1. Crear Usuario
        val userId = userDao.insertUsuario(UsuarioEntity(
            nombre = userData["nombre"] ?: "",
            apellidos = userData["apellidos"] ?: "",
            email = userData["email"] ?: "",
            password = userData["password"] ?: "",
            dni = userData["dni"],
            telefono = userData["telefono"]
        )).toInt()

        // 2. Manejar Rol y Organización
        val rol = userDao.getRolByNombre(roleName) ?: return@withContext -1
        
        if (roleName == "super_admin" && orgData != null) {
            // Crear nueva organización
            val newOrgId = userDao.insertOrganizacion(OrganizacionEntity(
                nombre = orgData["nombre"] ?: "Mi Finca",
                creado_por = userId,
                codigo_acceso = orgData["codigo"]
            )).toInt()
            
            userDao.insertMiembro(MiembroOrganizacionEntity(
                usuario_id = userId,
                organizacion_id = newOrgId,
                rol_id = rol.id,
                activo = true
            ))
        } else if (orgId != null) {
            // Unirse a organización existente (pendiente de aprobación)
            userDao.insertMiembro(MiembroOrganizacionEntity(
                usuario_id = userId,
                organizacion_id = orgId,
                rol_id = rol.id,
                activo = false
            ))
            
            userDao.insertSolicitudUnion(SolicitudUnionEntity(
                organizacion_id = orgId,
                usuario_solicitante_id = userId,
                mensaje = "Solicitud de ingreso como $roleName"
            ))
        }

        userId
    }

    suspend fun getOrganizaciones(query: String) = withContext(Dispatchers.IO) {
        userDao.searchOrganizaciones("%$query%")
    }

    suspend fun getOrganizacionById(id: Int) = withContext(Dispatchers.IO) {
        userDao.getOrganizacionById(id)
    }

    suspend fun getUserById(id: Int) = withContext(Dispatchers.IO) {
        userDao.getUsuarioById(id)
    }

    suspend fun loginLocal(email: String, pass: String): UsuarioEntity? = withContext(Dispatchers.IO) {
        val user = userDao.getUsuarioByEmail(email)
        if (user != null && user.password == pass) user else null
    }

    suspend fun getSolicitudesUnionPendientes(orgId: Int) = withContext(Dispatchers.IO) {
        userDao.getSolicitudesPendientesByOrg(orgId)
    }

    suspend fun getMiembrosByOrganizacion(orgId: Int) = withContext(Dispatchers.IO) {
        userDao.getMiembrosConRol(orgId)
    }

    suspend fun getTodosUsuarios(orgId: Int) = withContext(Dispatchers.IO) {
        userDao.getTodosUsuariosPorOrganizacion(orgId)
    }

    suspend fun aprobarSolicitudUnion(solicitudId: Int, adminId: Int) = withContext(Dispatchers.IO) {
        val solicitud = userDao.getSolicitudUnionById(solicitudId) ?: return@withContext
        userDao.updateSolicitudUnion(solicitud.copy(estado = "APROBADA"))
        
        val miembro = userDao.getMiembro(solicitud.usuario_solicitante_id, solicitud.organizacion_id)
        if (miembro == null) {
            val rolAgricultor = userDao.getRolByNivel(1)
            userDao.insertMiembro(MiembroOrganizacionEntity(
                usuario_id = solicitud.usuario_solicitante_id,
                organizacion_id = solicitud.organizacion_id,
                rol_id = rolAgricultor?.id ?: 1,
                activo = true
            ))
        } else {
            userDao.updateMiembro(miembro.copy(activo = true))
        }
    }

    suspend fun rechazarSolicitudUnion(solicitudId: Int) = withContext(Dispatchers.IO) {
        val solicitud = userDao.getSolicitudUnionById(solicitudId) ?: return@withContext
        userDao.updateSolicitudUnion(solicitud.copy(estado = "RECHAZADA"))
    }

    suspend fun ascenderMiembro(usuarioId: Int, orgId: Int) = withContext(Dispatchers.IO) {
        val miembro = userDao.getMiembro(usuarioId, orgId) ?: return@withContext
        val rolActual = userDao.getRolById(miembro.rol_id)
        val siguienteNivel = (rolActual?.nivel ?: 1) + 1
        
        if (siguienteNivel <= 3) {
            val nuevoRol = userDao.getRolByNivel(siguienteNivel)
            if (nuevoRol != null) {
                userDao.updateMiembro(miembro.copy(rol_id = nuevoRol.id))
            }
        }
    }

    suspend fun getRoles() = withContext(Dispatchers.IO) {
        userDao.getAllRoles()
    }
}
