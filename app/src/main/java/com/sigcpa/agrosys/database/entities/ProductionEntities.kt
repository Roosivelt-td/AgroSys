package com.sigcpa.agrosys.database.entities

import androidx.room.Entity
import androidx.room.PrimaryKey
import androidx.room.ForeignKey
import androidx.room.Index

@Entity(
    tableName = "cosechas",
    foreignKeys = [
        ForeignKey(
            entity = CultivoEntity::class,
            parentColumns = ["id"],
            childColumns = ["cultivo_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["cultivo_id"])]
)
data class CosechaEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val cultivo_id: Int,
    val fecha_cosecha: Long,
    val cantidad_kg: Double,
    val calidad: String?,
    val lote_codigo: String?,
    val costo_operativo_cosecha: Double = 0.0,
    val observaciones: String?,
    val created_at: Long = System.currentTimeMillis() / 1000,
    val sincronizado: Int = 0
)

@Entity(tableName = "compradores")
data class CompradorEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val nombre: String,
    val ruc_dni: String?,
    val telefono: String?,
    val email: String?,
    val direccion: String?,
    val created_at: Long = System.currentTimeMillis() / 1000,
    val sincronizado: Int = 0
)

@Entity(
    tableName = "ventas",
    foreignKeys = [
        ForeignKey(
            entity = CosechaEntity::class,
            parentColumns = ["id"],
            childColumns = ["cosecha_id"],
            onDelete = ForeignKey.RESTRICT
        ),
        ForeignKey(
            entity = CompradorEntity::class,
            parentColumns = ["id"],
            childColumns = ["comprador_id"],
            onDelete = ForeignKey.RESTRICT
        )
    ],
    indices = [Index(value = ["cosecha_id"]), Index(value = ["comprador_id"])]
)
data class VentaEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val cosecha_id: Int,
    val comprador_id: Int,
    val fecha_venta: Long,
    val cantidad_vendida_kg: Double,
    val precio_por_kg: Double,
    val costo_flete: Double = 0.0,
    val impuestos: Double = 0.0,
    val comprobante_tipo: String?,
    val comprobante_numero: String?,
    val created_at: Long = System.currentTimeMillis() / 1000,
    val updated_at: Long = System.currentTimeMillis() / 1000,
    val sincronizado: Int = 0
)
