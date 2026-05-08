package com.sigcpa.agrosys.database.dao

import androidx.room.*
import com.sigcpa.agrosys.database.entities.*

@Dao
interface UserDao {
    @Insert(onConflict = OnConflictStrategy.ABORT)
    suspend fun insertUsuario(usuario: UsuarioEntity): Long

    @Update
    suspend fun updateUsuario(usuario: UsuarioEntity)

    @Query("""
        SELECT u.*, r.nombre as nombre_rol 
        FROM usuarios u 
        JOIN roles r ON u.rol_id = r.id 
        WHERE u.email = :email LIMIT 1
    """)
    suspend fun getUsuarioByEmail(email: String): UsuarioWithRol?

    @Query("""
        SELECT u.*, r.nombre as nombre_rol 
        FROM usuarios u 
        JOIN roles r ON u.rol_id = r.id 
        WHERE u.id = :id
    """)
    suspend fun getUsuarioById(id: Int): UsuarioWithRol?

    @Query("SELECT COUNT(*) FROM usuarios")
    suspend fun countUsuarios(): Int

    @Query("SELECT * FROM roles")
    suspend fun getAllRoles(): List<RolEntity>

    @Query("SELECT * FROM roles WHERE nombre = :nombreRol LIMIT 1")
    suspend fun getRolByName(nombreRol: String): RolEntity?

    @Query("SELECT COUNT(*) FROM usuarios WHERE email = :email")
    suspend fun countUsersByEmail(email: String): Int

    @Update
    suspend fun updateRedSocial(redSocial: RedSocialEntity)

    @Insert
    suspend fun insertOrganizacion(organizacion: OrganizacionEntity): Long

    @Update
    suspend fun updateOrganizacion(organizacion: OrganizacionEntity)

    @Delete
    suspend fun deleteOrganizacion(organizacion: OrganizacionEntity)

    @Query("""
        SELECT o.* FROM organizaciones o
        JOIN miembros_organizacion mo ON o.id = mo.organizacion_id
        WHERE mo.usuario_id = :userId AND mo.estado = 'activo'
    """)
    suspend fun getOrganizacionesByUsuario(userId: Int): List<OrganizacionEntity>

    @Query("SELECT * FROM organizaciones WHERE administrador_id = :adminId LIMIT 1")
    suspend fun getOrganizacionByAdmin(adminId: Int): OrganizacionEntity?

    @Query("SELECT * FROM solicitudes_usuario WHERE usuario_id = :userId AND estado = 'pendiente' LIMIT 1")
    suspend fun getSolicitudPendienteByUser(userId: Int): SolicitudUsuarioEntity?

    @Query("SELECT * FROM organizaciones WHERE deleted_at IS NULL")
    suspend fun getAllOrganizaciones(): List<OrganizacionEntity>

    @Insert
    suspend fun insertSolicitud(solicitud: SolicitudUsuarioEntity): Long

    @Update
    suspend fun updateSolicitud(solicitud: SolicitudUsuarioEntity)

    @Query("""
        SELECT s.*, u.nombre as user_nombre, u.apellidos as user_apellidos, o.nombre as org_nombre 
        FROM solicitudes_usuario s
        JOIN usuarios u ON s.usuario_id = u.id
        JOIN organizaciones o ON s.organizacion_id = o.id
        WHERE s.estado = 'pendiente'
    """)
    suspend fun getSolicitudesPendientes(): List<SolicitudWithDetails>

    @Query("""
        SELECT s.*, u.nombre as user_nombre, u.apellidos as user_apellidos, o.nombre as org_nombre 
        FROM solicitudes_usuario s
        JOIN usuarios u ON s.usuario_id = u.id
        JOIN organizaciones o ON s.organizacion_id = o.id
        WHERE o.administrador_id = :adminId AND s.estado = 'pendiente'
    """)
    suspend fun getSolicitudesParaAdmin(adminId: Int): List<SolicitudWithDetails>

    @Insert
    suspend fun insertMiembroOrganizacion(miembro: MiembroOrganizacionEntity): Long

    @Query("""
        SELECT COUNT(*) FROM miembros_organizacion 
        WHERE organizacion_id = :orgId AND estado = 'activo'
    """)
    suspend fun countMiembrosByOrganizacion(orgId: Int): Int

    @Query("SELECT * FROM organizaciones WHERE id = :id LIMIT 1")
    suspend fun getOrganizacionById(id: Int): OrganizacionEntity?

    @Query("""
        SELECT u.* FROM usuarios u
        JOIN miembros_organizacion mo ON u.id = mo.usuario_id
        WHERE mo.organizacion_id = :orgId AND mo.estado = 'activo'
    """)
    suspend fun getMiembrosByOrganizacion(orgId: Int): List<UsuarioEntity>

    // --- Redes Sociales ---
    @Query("SELECT * FROM tipos_red_social")
    suspend fun getAllTiposRedSocial(): List<TipoRedSocialEntity>

    @Query("""
        SELECT rs.*, trs.nombre as tipo_nombre, trs.color_hex 
        FROM redes_sociales rs 
        JOIN tipos_red_social trs ON rs.tipo_red_id = trs.id 
        WHERE rs.usuario_id = :userId
    """)
    suspend fun getRedesSocialesByUsuario(userId: Int): List<RedSocialWithMetadata>

    @Query("SELECT u.* FROM usuarios u WHERE u.id != :currentUserId")
    suspend fun getAllOtherUsers(currentUserId: Int): List<UsuarioEntity>

    @Insert
    suspend fun insertRedSocial(redSocial: RedSocialEntity): Long

    @Delete
    suspend fun deleteRedSocial(redSocial: RedSocialEntity)
}

data class UsuarioWithRol(
    @Embedded val usuario: UsuarioEntity,
    val nombre_rol: String
)

data class RedSocialWithMetadata(
    @Embedded val redSocial: RedSocialEntity,
    val tipo_nombre: String,
    val color_hex: String?
)

data class SolicitudWithDetails(
    @Embedded val solicitud: SolicitudUsuarioEntity,
    val user_nombre: String,
    val user_apellidos: String,
    val org_nombre: String
)
