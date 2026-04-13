package com.sigcpa.agrosys.database.dao

import androidx.room.*
import com.sigcpa.agrosys.database.entities.*

@Dao
interface AssetDao {
    @Insert
    suspend fun insertTerreno(terreno: TerrenoEntity): Long

    @Update
    suspend fun updateTerreno(terreno: TerrenoEntity)

    @Delete
    suspend fun deleteTerreno(terreno: TerrenoEntity)

    @Query("SELECT * FROM terrenos WHERE agricultor_id = :agricultorId AND deleted_at IS NULL")
    suspend fun getTerrenosByAgricultor(agricultorId: Int): List<TerrenoEntity>

    @Query("SELECT COUNT(*) FROM terrenos WHERE agricultor_id = :agricultorId AND deleted_at IS NULL")
    suspend fun countTerrenosByAgricultor(agricultorId: Int): Int

    @Insert
    suspend fun insertCultivo(cultivo: CultivoEntity): Long

    @Update
    suspend fun updateCultivo(cultivo: CultivoEntity)

    @Delete
    suspend fun deleteCultivo(cultivo: CultivoEntity)

    @Query("""
        SELECT c.* FROM cultivos c 
        JOIN terrenos t ON c.terreno_id = t.id 
        WHERE t.agricultor_id = :agricultorId AND c.deleted_at IS NULL
    """)
    suspend fun getCultivosByAgricultor(agricultorId: Int): List<CultivoEntity>

    @Query("SELECT * FROM cultivos WHERE id = :id")
    suspend fun getCultivoById(id: Int): CultivoEntity?

    @Query("SELECT * FROM cultivos WHERE terreno_id = :terrenoId AND deleted_at IS NULL")
    suspend fun getCultivosByTerreno(terrenoId: Int): List<CultivoEntity>

    @Query("SELECT * FROM cultivos WHERE terreno_id = :terrenoId AND estado != 'cosechado' AND deleted_at IS NULL")
    suspend fun getCultivosActivosByTerreno(terrenoId: Int): List<CultivoEntity>

    @Query("SELECT * FROM catalogo_cultivos")
    suspend fun getCatalogoCultivos(): List<CatalogoCultivoEntity>
    
    @Query("SELECT * FROM catalogo_cultivos WHERE id = :id")
    suspend fun getCatalogoCultivoById(id: Int): CatalogoCultivoEntity?

    @Query("SELECT * FROM terrenos WHERE id = :id")
    suspend fun getTerrenoById(id: Int): TerrenoEntity?

    @Query("""
        SELECT lr.* FROM labores_realizadas lr
        JOIN cultivos c ON lr.cultivo_id = c.id
        JOIN terrenos t ON c.terreno_id = t.id
        WHERE t.agricultor_id = :agricultorId
        ORDER BY lr.fecha_realizacion DESC
    """)
    suspend fun getLaboresByAgricultor(agricultorId: Int): List<LaborRealizadaEntity>

    @Query("SELECT * FROM labores_realizadas WHERE cultivo_id = :cultivoId")
    suspend fun getLaboresByCultivo(cultivoId: Int): List<LaborRealizadaEntity>

    @Insert
    suspend fun insertLabor(labor: LaborRealizadaEntity): Long

    @Query("SELECT * FROM catalogo_labores WHERE id = :id")
    suspend fun getCatalogoLaborById(id: Int): CatalogoLaborEntity?

    @Query("SELECT * FROM catalogo_labores")
    suspend fun getCatalogoLabores(): List<CatalogoLaborEntity>

    @Insert
    suspend fun insertCosecha(cosecha: CosechaEntity): Long

    @Query("SELECT * FROM cosechas WHERE cultivo_id = :cultivoId")
    suspend fun getCosechasByCultivo(cultivoId: Int): List<CosechaEntity>

    @Query("SELECT * FROM ventas WHERE cosecha_id IN (SELECT id FROM cosechas WHERE cultivo_id = :cultivoId)")
    suspend fun getVentasByCultivo(cultivoId: Int): List<VentaEntity>

    @Insert
    suspend fun insertInsumoUsado(insumo: InsumoUsadoEntity): Long

    @Query("SELECT * FROM catalogo_insumos")
    suspend fun getCatalogoInsumos(): List<CatalogoInsumoEntity>

    @Query("""
        SELECT iu.* FROM insumos_usados iu 
        JOIN labores_realizadas lr ON iu.labor_id = lr.id 
        WHERE lr.cultivo_id = :cultivoId
    """)
    suspend fun getInsumosByCultivo(cultivoId: Int): List<InsumoUsadoEntity>

    @Query("SELECT SUM(cantidad * costo_unitario) FROM insumos_usados WHERE labor_id = :laborId")
    suspend fun getCostoInsumosByLabor(laborId: Int): Double?
}
