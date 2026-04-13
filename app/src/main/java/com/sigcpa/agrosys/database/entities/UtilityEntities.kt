package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(
    tableName = "archivos_multimedia",
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["subido_por_usuario_id"],
            onDelete = ForeignKey.SET_NULL
        )
    ],
    indices = [
        Index(value = ["entidad_tipo", "entidad_id"], name = "idx_archivos_entidad"), 
        Index(value = ["subido_por_usuario_id"])
    ]
)
data class ArchivoMultimediaEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val entidad_tipo: String,
    val entidad_id: Int,
    val ruta_archivo: String,
    val nombre_archivo: String?,
    val tipo_mime: String?,
    val peso_kb: Int?,
    val descripcion: String?,
    val subido_por_usuario_id: Int?,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "notificaciones",
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["usuario_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["usuario_id"])]
)
data class NotificacionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val titulo: String,
    val mensaje: String,
    @ColumnInfo(defaultValue = "info") val tipo: String = "info",
    @ColumnInfo(defaultValue = "0") val leido: Int = 0,
    val fecha_programada: Long?,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)
