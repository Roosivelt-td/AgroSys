package com.sigcpa.agrosys.database.entities

import androidx.room.Embedded

data class TerrenoConStats(
    @Embedded val terreno: TerrenoEntity,
    val cultivosCount: Int,
    val areaOcupada: Double
)
