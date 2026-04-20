package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(
    tableName = "terrenos",
    foreignKeys = [
        ForeignKey(
            entity = AgricultorEntity::class,
            parentColumns = ["id"],
            childColumns = ["agricultor_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["agricultor_id"])]
)
data class TerrenoEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val agricultor_id: Int,
    val nombre: String,
    val ubicacion_geo: String?,
    val direccion_referencia: String?,
    val area_hectareas: Double,
    val tipo_tenencia: String,
    @ColumnInfo(defaultValue = "0") val costo_alquiler_anual: Double = 0.0,
    val calidad_suelo: String?,
    val fuente_agua: String?,
    @ColumnInfo(defaultValue = "activo") val estado: String = "activo",
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    val deleted_at: Long? = null,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "catalogo_cultivos",
    indices = [Index(value = ["nombre"], unique = true)]
)
data class CatalogoCultivoEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val nombre_cientifico: String?,
    val tipo_ciclo: String, // 'ciclo_corto', 'perenne'
    val vida_util_estimada_meses: Int?,
    val dias_a_cosecha_promedio: Int?,
    val instrucciones_base_riego: String?,
    val instrucciones_base_plagas: String?,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "cultivos",
    foreignKeys = [
        ForeignKey(
            entity = TerrenoEntity::class,
            parentColumns = ["id"],
            childColumns = ["terreno_id"],
            onDelete = ForeignKey.CASCADE
        ),
        ForeignKey(
            entity = CatalogoCultivoEntity::class,
            parentColumns = ["id"],
            childColumns = ["catalogo_cultivo_id"],
            onDelete = ForeignKey.RESTRICT
        )
    ],
    indices = [Index(value = ["terreno_id"]), Index(value = ["catalogo_cultivo_id"])]
)
data class CultivoEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val terreno_id: Int,
    val catalogo_cultivo_id: Int,
    val nombre_lote: String?,
    val fecha_planificada: Long? = null,
    val fecha_siembra: Long,
    val fecha_finalizacion: Long?,
    @ColumnInfo(defaultValue = "activo") val estado: String = "activo",
    val area_destinada: Double?,
    val variedad: String?,
    val plantas_estimadas: Int?,
    val observaciones: String?,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    val deleted_at: Long? = null,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)
