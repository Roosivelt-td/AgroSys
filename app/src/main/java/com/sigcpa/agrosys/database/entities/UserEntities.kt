package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(tableName = "roles")
data class RolEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String // super_admin, admin, supervisor, usuario
)

@Entity(
    tableName = "usuarios",
    indices = [
        Index(value = ["email"], unique = true),
        Index(value = ["firebase_id"], unique = true),
        Index(value = ["dni"], unique = true),
        Index(value = ["rol_id"])
    ],
    foreignKeys = [
        ForeignKey(
            entity = RolEntity::class,
            parentColumns = ["id"],
            childColumns = ["rol_id"],
            onDelete = ForeignKey.RESTRICT
        )
    ]
)
data class UsuarioEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val rol_id: Int, // Relación obligatoria con la tabla roles
    val nombre: String,
    val apellidos: String,
    val email: String,
    val password: String,
    val dni: String? = null, // Datos de agricultor integrados
    val experiencia_anios: Int = 0,
    val nivel_educativo: String? = null,
    val telefono: String? = null,
    val ubicacion: String? = null,
    val foto_perfil_url: String? = null,
    val foto_portada_url: String? = null,
    @ColumnInfo(defaultValue = "1") val is_activo: Int = 1,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    val deleted_at: Long? = null,
    val firebase_id: String? = null,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "organizaciones",
    indices = [
        Index(value = ["ruc"], unique = true),
        Index(value = ["administrador_id"], unique = true)
    ],
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["administrador_id"],
            onDelete = ForeignKey.RESTRICT
        )
    ]
)
data class OrganizacionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val administrador_id: Int,
    val ruc: String? = null,
    val direccion: String? = null,
    val rubro: String? = null, // A qué se dedica
    val descripcion: String? = null,
    val telefono_contacto: String? = null,
    @ColumnInfo(defaultValue = "empresa") val tipo: String = "empresa",
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    val deleted_at: Long? = null
)

@Entity(
    tableName = "miembros_organizacion",
    primaryKeys = ["usuario_id", "organizacion_id"],
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.CASCADE)
    ]
)
data class MiembroOrganizacionEntity(
    val usuario_id: Int,
    val organizacion_id: Int,
    val estado: String = "activo",
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val fecha_union: Long = System.currentTimeMillis() / 1000
)

@Entity(
    tableName = "solicitudes_usuario",
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = OrganizacionEntity::class, parentColumns = ["id"], childColumns = ["organizacion_id"], onDelete = ForeignKey.CASCADE)
    ]
)
data class SolicitudUsuarioEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val organizacion_id: Int,
    val tipo_solicitud: String, // invitacion, unirse, ascenso_supervisor
    val estado: String = "pendiente",
    val mensaje: String? = null,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000
)

@Entity(tableName = "tipos_red_social")
data class TipoRedSocialEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val icono_url: String? = null,
    val color_hex: String? = null
)

@Entity(
    tableName = "redes_sociales",
    foreignKeys = [
        ForeignKey(entity = UsuarioEntity::class, parentColumns = ["id"], childColumns = ["usuario_id"], onDelete = ForeignKey.CASCADE),
        ForeignKey(entity = TipoRedSocialEntity::class, parentColumns = ["id"], childColumns = ["tipo_red_id"], onDelete = ForeignKey.CASCADE)
    ]
)
data class RedSocialEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val tipo_red_id: Int,
    val link: String,
    val usuario_social: String,
    val descripcion: String? = null
)
