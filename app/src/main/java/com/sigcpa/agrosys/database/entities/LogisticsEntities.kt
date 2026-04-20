package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(tableName = "catalogo_labores")
data class CatalogoLaborEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val categoria: String?,
    val descripcion: String?,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "labores_realizadas",
    foreignKeys = [
        ForeignKey(
            entity = CultivoEntity::class,
            parentColumns = ["id"],
            childColumns = ["cultivo_id"],
            onDelete = ForeignKey.CASCADE
        ),
        ForeignKey(
            entity = CatalogoLaborEntity::class,
            parentColumns = ["id"],
            childColumns = ["catalogo_labor_id"],
            onDelete = ForeignKey.RESTRICT
        ),
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["creado_por_usuario_id"],
            onDelete = ForeignKey.SET_NULL
        )
    ],
    indices = [
        Index(value = ["cultivo_id"]), 
        Index(value = ["catalogo_labor_id"]), 
        Index(value = ["creado_por_usuario_id"]),
        Index(value = ["fecha_realizacion"], name = "idx_labores_fecha")
    ]
)
data class LaborRealizadaEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val cultivo_id: Int,
    val catalogo_labor_id: Int,
    val fecha_realizacion: Long,
    @ColumnInfo(defaultValue = "0") val costo_mano_obra_total: Double = 0.0,
    @ColumnInfo(defaultValue = "0") val costo_maquinaria_total: Double = 0.0,
    val observaciones: String?,
    val creado_por_usuario_id: Int?,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val updated_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(tableName = "mano_obra_tipo")
data class ManoObraTipoEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String // Varon, Mujer, Otros
)

@Entity(
    tableName = "mano_obra",
    foreignKeys = [
        ForeignKey(
            entity = LaborRealizadaEntity::class,
            parentColumns = ["id"],
            childColumns = ["labor_realizada_id"],
            onDelete = ForeignKey.CASCADE
        ),
        ForeignKey(
            entity = ManoObraTipoEntity::class,
            parentColumns = ["id"],
            childColumns = ["tipo_id"],
            onDelete = ForeignKey.RESTRICT
        )
    ],
    indices = [Index(value = ["labor_realizada_id"]), Index(value = ["tipo_id"])]
)
data class ManoObraEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val labor_realizada_id: Int,
    val tipo_id: Int,
    val cantidad_trabajadores: Int,
    val dias_trabajados: Int,
    val costo_por_dia: Double,
    val subtotal: Double
)

@Entity(tableName = "catalogo_insumos")
data class CatalogoInsumoEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val categoria: String?,
    val unidad_medida: String?,
    val descripcion: String?,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(tableName = "proveedores")
data class ProveedorEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre_empresa: String,
    val ruc: String?,
    val telefono: String?,
    val tipo_servicio: String?,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)

@Entity(
    tableName = "insumos_usados",
    foreignKeys = [
        ForeignKey(
            entity = LaborRealizadaEntity::class,
            parentColumns = ["id"],
            childColumns = ["labor_id"],
            onDelete = ForeignKey.CASCADE
        ),
        ForeignKey(
            entity = CatalogoInsumoEntity::class,
            parentColumns = ["id"],
            childColumns = ["catalogo_insumo_id"],
            onDelete = ForeignKey.RESTRICT
        ),
        ForeignKey(
            entity = ProveedorEntity::class,
            parentColumns = ["id"],
            childColumns = ["proveedor_id"],
            onDelete = ForeignKey.SET_NULL
        )
    ],
    indices = [Index(value = ["labor_id"]), Index(value = ["catalogo_insumo_id"]), Index(value = ["proveedor_id"])]
)
data class InsumoUsadoEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val labor_id: Int,
    val catalogo_insumo_id: Int,
    val proveedor_id: Int?,
    val cantidad: Double,
    val costo_unitario: Double,
    @ColumnInfo(defaultValue = "0") val costo_flete: Double = 0.0,
    val nombre_proveedor_manual: String? = null,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    @ColumnInfo(defaultValue = "0") val sincronizado: Int = 0
)
