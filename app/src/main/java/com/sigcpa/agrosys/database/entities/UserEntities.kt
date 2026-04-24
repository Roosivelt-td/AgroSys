package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(tableName = "roles")
data class RolEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String, // agricultor, supervisor, admin, super_admin
    val nivel: Int,     // 1, 2, 3, 4
    val descripcion: String? = null,
    val estado: Boolean = true
)

@Entity(
    tableName = "usuarios",
    indices = [
        Index(value = ["email"], unique = true), 
        Index(value = ["dni"], unique = true)
    ]
)
data class UsuarioEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val apellidos: String,
    val email: String,
    val password: String,
    val dni: String? = null,
    val telefono: String? = null,
    val direccion: String? = null,
    val ciudad: String? = null,
    val foto_perfil_url: String? = null,
    
    @ColumnInfo(name = "rol_global_id", defaultValue = "1") val rol_global_id: Int = 1,
    @ColumnInfo(name = "is_activo", defaultValue = "1") var is_activo: Int = 1,
    
    @ColumnInfo(name = "created_at", defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    val firebase_id: String? = null,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "organizaciones",
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class, 
            parentColumns = ["id"], 
            childColumns = ["creado_por"],
            onDelete = ForeignKey.RESTRICT
        )
    ],
    indices = [Index(value = ["creado_por"])]
)
data class OrganizacionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val descripcion: String? = null,
    val ruc: String? = null,
    val logo_url: String? = null,
    val creado_por: Int, // El Super Admin original
    val codigo_acceso: String? = null, // Para solicitar unión
    @ColumnInfo(name = "activa", defaultValue = "1") val activa: Boolean = true,
    @ColumnInfo(name = "created_at", defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000
)

@Entity(
    tableName = "miembros_organizacion",
    primaryKeys = ["usuario_id", "organizacion_id"],
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = RolEntity::class, parentColumns = ["id"], childColumns = ["rol_id"])
    ],
    indices = [Index(value = ["organizacion_id"]), Index(value = ["rol_id"])]
)
data class MiembroOrganizacionEntity(
    val usuario_id: Int,
    val organizacion_id: Int,
    val rol_id: Int, // Cambia según ascenso (1 al 4)
    @ColumnInfo(name = "fecha_ingreso", defaultValue = "(strftime('%s', 'now'))") val fecha_ingreso: Long = System.currentTimeMillis() / 1000,
    val activo: Boolean = true
)

@Entity(
    tableName = "invitaciones",
    foreignKeys = [
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_invitado_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_invitador_id"], onDelete = ForeignKey.CASCADE)
    ],
    indices = [Index(value = ["organizacion_id"]), Index(value = ["usuario_invitado_id"]), Index(value = ["usuario_invitador_id"])]
)
data class InvitacionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val organizacion_id: Int,
    val usuario_invitado_id: Int,
    val usuario_invitador_id: Int,
    val estado: String = "PENDIENTE", // PENDIENTE, ACEPTADA, RECHAZADA
    val fecha_envio: Long = System.currentTimeMillis() / 1000
)

@Entity(
    tableName = "solicitudes_union",
    foreignKeys = [
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_solicitante_id"], onDelete = ForeignKey.CASCADE)
    ],
    indices = [Index(value = ["organizacion_id"]), Index(value = ["usuario_solicitante_id"])]
)
data class SolicitudUnionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val organizacion_id: Int,
    val usuario_solicitante_id: Int,
    val mensaje: String? = null,
    val estado: String = "PENDIENTE", // PENDIENTE, APROBADA, RECHAZADA
    val fecha_solicitud: Long = System.currentTimeMillis() / 1000
)

@Entity(
    tableName = "notificaciones",
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE)
    ],
    indices = [Index(value = ["usuario_id"])]
)
data class NotificacionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val titulo: String,
    val mensaje: String,
    val tipo: String = "info", // info, alerta, invitacion
    val leido: Int = 0,
    val datos_relacionados: String? = null, // JSON para IDs
    val created_at: Long = System.currentTimeMillis() / 1000
)

@Entity(
    tableName = "votos_eliminacion",
    primaryKeys = ["organizacion_id", "usuario_id"],
    foreignKeys = [
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE)
    ],
    indices = [Index(value = ["usuario_id"])]
)
data class VotoEliminacionEntity(
    val organizacion_id: Int,
    val usuario_id: Int,
    val voto: Boolean,
    val fecha_voto: Long = System.currentTimeMillis() / 1000
)

@Entity(
    tableName = "actividades",
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.SET_NULL)
    ],
    indices = [Index(value = ["usuario_id"]), Index(value = ["organizacion_id"])]
)
data class ActividadEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val organizacion_id: Int? = null,
    val tipo_accion: String,
    val descripcion: String,
    val fecha: Long = System.currentTimeMillis() / 1000
)

@Entity(tableName = "tipos_red_social")
data class TipoRedSocialEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val color_hex: String? = null
)

@Entity(
    tableName = "redes_sociales",
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = TipoRedSocialEntity::class, parentColumns = ["id"], childColumns = ["tipo_red_id"], onDelete = ForeignKey.CASCADE)
    ],
    indices = [Index(value = ["usuario_id"]), Index(value = ["tipo_red_id"])]
)
data class RedSocialEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val tipo_red_id: Int,
    val url: String
)
