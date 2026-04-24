package com.sigcpa.agrosys.database.dao

import androidx.room.*
import com.sigcpa.agrosys.database.entities.*

@Dao
interface UserDao {
    // --- Roles ---
    @Insert
    suspend fun insertRol(rol: RolEntity): Long

    @Query("SELECT * FROM roles ORDER BY nivel ASC")
    suspend fun getAllRoles(): List<RolEntity>

    @Query("SELECT * FROM roles WHERE id = :id")
    suspend fun getRolById(id: Int): RolEntity?

    @Query("SELECT * FROM roles WHERE nombre = :nombre LIMIT 1")
    suspend fun getRolByNombre(nombre: String): RolEntity?

    // --- Usuarios ---
    @Insert(onConflict = OnConflictStrategy.ABORT)
    suspend fun insertUsuario(usuario: UsuarioEntity): Long

    @Update
    suspend fun updateUsuario(usuario: UsuarioEntity)

    @Query("SELECT * FROM usuarios WHERE email = :email LIMIT 1")
    suspend fun getUsuarioByEmail(email: String): UsuarioEntity?

    @Query("SELECT * FROM usuarios WHERE id = :id")
    suspend fun getUsuarioById(id: Int): UsuarioEntity?

    // --- Organizaciones ---
    @Insert
    suspend fun insertOrganizacion(organizacion: OrganizacionEntity): Long

    @Update
    suspend fun updateOrganizacion(organizacion: OrganizacionEntity)

    @Query("SELECT * FROM organizaciones WHERE creado_por = :userId AND activa = 1 LIMIT 1")
    suspend fun getOrganizacionActivaByCreador(userId: Int): OrganizacionEntity?

    @Query("SELECT * FROM organizaciones WHERE id = :id")
    suspend fun getOrganizacionById(id: Int): OrganizacionEntity?

    @Query("SELECT * FROM organizaciones WHERE nombre LIKE :query")
    suspend fun searchOrganizaciones(query: String): List<OrganizacionEntity>

    // --- Miembros ---
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertMiembro(miembro: MiembroOrganizacionEntity)

    @Update
    suspend fun updateMiembro(miembro: MiembroOrganizacionEntity)

    @Query("""
        SELECT u.*, r.id as rol_id, r.nombre as rol_nombre, r.nivel as rol_nivel
        FROM usuarios u
        JOIN miembros_organizacion mo ON u.id = mo.usuario_id
        JOIN roles r ON mo.rol_id = r.id
        WHERE mo.organizacion_id = :orgId AND mo.activo = 1
    """)
    suspend fun getMiembrosConRol(orgId: Int): List<UsuarioConRol>

    @Query("SELECT * FROM miembros_organizacion WHERE usuario_id = :userId AND organizacion_id = :orgId LIMIT 1")
    suspend fun getMiembro(userId: Int, orgId: Int): MiembroOrganizacionEntity?

    // --- Invitaciones y Solicitudes ---
    @Insert
    suspend fun insertInvitacion(invitacion: InvitacionEntity): Long

    @Update
    suspend fun updateInvitacion(invitacion: InvitacionEntity)

    @Insert
    suspend fun insertSolicitudUnion(solicitud: SolicitudUnionEntity): Long

    @Update
    suspend fun updateSolicitudUnion(solicitud: SolicitudUnionEntity)

    @Query("SELECT * FROM invitaciones WHERE usuario_invitado_id = :userId AND estado = 'PENDIENTE'")
    suspend fun getInvitacionesPendientes(userId: Int): List<InvitacionEntity>

    @Query("SELECT * FROM solicitudes_union WHERE organizacion_id = :orgId AND estado = 'PENDIENTE'")
    suspend fun getSolicitudesPendientesByOrg(orgId: Int): List<SolicitudUnionEntity>

    // --- Votos ---
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertVoto(voto: VotoEliminacionEntity)

    @Query("SELECT COUNT(*) FROM votos_eliminacion WHERE organizacion_id = :orgId AND voto = 1")
    suspend fun countVotosFavor(orgId: Int): Int

    @Query("SELECT COUNT(*) FROM miembros_organizacion WHERE organizacion_id = :orgId AND activo = 1")
    suspend fun countMiembrosActivos(orgId: Int): Int

    // --- Notificaciones ---
    @Insert
    suspend fun insertNotificacion(notificacion: NotificacionEntity): Long

    @Query("SELECT * FROM notificaciones WHERE usuario_id = :userId ORDER BY created_at DESC")
    suspend fun getNotificacionesByUser(userId: Int): List<NotificacionEntity>

    @Update
    suspend fun updateNotificacion(notificacion: NotificacionEntity)

    // --- Actividades ---
    @Insert
    suspend fun insertActividad(actividad: ActividadEntity): Long

    // --- Redes Sociales ---
    @Query("""
        SELECT rs.*, trs.nombre as tipo_nombre, trs.color_hex 
        FROM redes_sociales rs 
        JOIN tipos_red_social trs ON rs.tipo_red_id = trs.id 
        WHERE rs.usuario_id = :userId
    """)
    suspend fun getRedesSocialesByUsuario(userId: Int): List<RedSocialWithMetadata>

    @Insert
    suspend fun insertRedSocial(redSocial: RedSocialEntity): Long

    @Query("SELECT * FROM tipos_red_social")
    suspend fun getAllTiposRedSocial(): List<TipoRedSocialEntity>

    @Query("SELECT * FROM roles WHERE nivel = :nivel LIMIT 1")
    suspend fun getRolByNivel(nivel: Int): RolEntity?

    @Query("SELECT * FROM solicitudes_union WHERE id = :id")
    suspend fun getSolicitudUnionById(id: Int): SolicitudUnionEntity?

    @Query("""
        SELECT u.* FROM usuarios u
        INNER JOIN miembros_organizacion mo ON u.id = mo.usuario_id
        WHERE mo.organizacion_id = :orgId
    """)
    suspend fun getTodosUsuariosPorOrganizacion(orgId: Int): List<UsuarioEntity>

    @Query("SELECT * FROM miembros_organizacion WHERE usuario_id = :usuarioId")
    suspend fun getMiembrosPorUsuario(usuarioId: Int): List<MiembroOrganizacionEntity>

    @Transaction
    @Query("""
        SELECT u.*, r.id as rol_id, r.nombre as rol_nombre, r.nivel as rol_nivel 
        FROM usuarios u
        INNER JOIN miembros_organizacion mo ON u.id = mo.usuario_id
        INNER JOIN roles r ON mo.rol_id = r.id
        WHERE u.id = :usuarioId
    """)
    suspend fun getMiembrosConRolPorUsuario(usuarioId: Int): List<UsuarioConRol>
}

data class UsuarioConRol(
    @Embedded val usuario: UsuarioEntity,
    val rol_id: Int,
    val rol_nombre: String,
    val rol_nivel: Int
)

data class RedSocialWithMetadata(
    @Embedded val redSocial: RedSocialEntity,
    val tipo_nombre: String,
    val color_hex: String?
)
