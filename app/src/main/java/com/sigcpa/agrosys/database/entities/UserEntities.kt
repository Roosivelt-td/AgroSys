package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(
    tableName = "organizaciones",
    indices = [Index(value = ["ruc"], unique = true)]
)
data class OrganizacionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val ruc: String? = null,
    val direccion: String? = null,
    @ColumnInfo(defaultValue = "individual") val tipo: String = "individual",
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    val deleted_at: Long? = null
)

@Entity(
    tableName = "usuarios",
    foreignKeys = [
        ForeignKey(
            entity = OrganizacionEntity::class,
            parentColumns = ["id"],
            childColumns = ["organizacion_id"],
            onDelete = ForeignKey.SET_NULL
        )
    ],
    indices = [
        Index(value = ["email"], unique = true), 
        Index(value = ["firebase_id"], unique = true)
    ]
)
data class UsuarioEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val organizacion_id: Int? = null,
    val nombre: String,
    val apellidos: String,
    val email: String,
    val password: String,
    val rol: String, // administrador, supervisor, agricultor
    val telefono: String? = null,
    val foto_perfil_url: String? = null,
    @ColumnInfo(defaultValue = "1") val is_activo: Int = 1,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    val deleted_at: Long? = null,
    val firebase_id: String? = null
)

@Entity(
    tableName = "agricultores",
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["usuario_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [
        Index(value = ["usuario_id"], unique = true), 
        Index(value = ["dni"], unique = true)
    ]
)
data class AgricultorEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val dni: String,
    @ColumnInfo(defaultValue = "0") val experiencia_anios: Int = 0,
    val nivel_educativo: String? = null,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "asignaciones_supervisor",
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["supervisor_id"],
            onDelete = ForeignKey.CASCADE
        ),
        ForeignKey(
            entity = AgricultorEntity::class,
            parentColumns = ["id"],
            childColumns = ["agricultor_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["supervisor_id", "agricultor_id"], unique = true)]
)
data class AsignacionSupervisorEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val supervisor_id: Int,
    val agricultor_id: Int,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val fecha_asignacion: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "1") val activo: Int = 1
)
