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

    @Query("""
        SELECT 
            t.*,
            (SELECT COUNT(*) FROM cultivos c WHERE c.terreno_id = t.id AND c.deleted_at IS NULL) as cultivosCount,
            (SELECT COALESCE(SUM(c2.area_destinada), 0.0) FROM cultivos c2 WHERE c2.terreno_id = t.id AND c2.estado != 'cosechado' AND c2.deleted_at IS NULL) as areaOcupada
        FROM terrenos t
        WHERE t.agricultor_id = :agricultorId AND t.deleted_at IS NULL
    """)
    suspend fun getTerrenosConStats(agricultorId: Int): List<com.sigcpa.agrosys.database.entities.TerrenoConStats>

    @Query("SELECT * FROM terrenos WHERE agricultor_id = :agricultorId AND estado IN ('activo', 'planificado') AND deleted_at IS NULL")
    suspend fun getTerrenosActivosYPlanificados(agricultorId: Int): List<TerrenoEntity>

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

    @Query("SELECT * FROM cultivos WHERE terreno_id = :terrenoId AND deleted_at IS NULL ORDER BY COALESCE(fecha_siembra, fecha_planificada, 0) DESC")
    suspend fun getCultivosByTerreno(terrenoId: Int): List<CultivoEntity>

    @Query("SELECT * FROM cultivos WHERE terreno_id = :terrenoId AND estado != 'cosechado' AND deleted_at IS NULL")
    suspend fun getCultivosActivosByTerreno(terrenoId: Int): List<CultivoEntity>

    @Query("SELECT * FROM catalogo_cultivos WHERE es_personalizado = 0 OR usuario_creador_id = :userId")
    suspend fun getCatalogoByUsuario(userId: Int): List<CatalogoCultivoEntity>

    @Query("SELECT * FROM catalogo_cultivos")
    suspend fun getCatalogoCultivos(): List<CatalogoCultivoEntity>

    @Query("""
        SELECT cc.* FROM catalogo_cultivos cc
        LEFT JOIN (
            SELECT catalogo_cultivo_id, COUNT(*) as usage_count 
            FROM cultivos 
            GROUP BY catalogo_cultivo_id
        ) usage ON cc.id = usage.catalogo_cultivo_id
        ORDER BY usage.usage_count DESC
        LIMIT 8
    """)
    suspend fun getMostUsedCatalogoCultivos(): List<CatalogoCultivoEntity>
    
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertCatalogoCultivo(catalogo: CatalogoCultivoEntity): Long

    @Query("SELECT * FROM catalogo_cultivos WHERE id = :id")
    suspend fun getCatalogoCultivoById(id: Int): CatalogoCultivoEntity?

    @Query("SELECT * FROM terrenos WHERE id = :id")
    suspend fun getTerrenoById(id: Int): TerrenoEntity?

    @Query("""
        SELECT SUM(area_destinada) FROM cultivos 
        WHERE terreno_id = :terrenoId AND estado != 'cosechado' AND deleted_at IS NULL
    """)
    suspend fun getAreaOcupadaByTerreno(terrenoId: Int): Double?

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

    @Delete
    suspend fun deleteLabor(labor: LaborRealizadaEntity)

    @Update
    suspend fun updateLabor(labor: LaborRealizadaEntity)

    @Query("DELETE FROM insumos_usados WHERE labor_id = :laborId")
    suspend fun deleteInsumosByLabor(laborId: Int)

    @Query("DELETE FROM mano_obra WHERE labor_realizada_id = :laborId")
    suspend fun deleteManoObraByLabor(laborId: Int)

    @Query("SELECT * FROM catalogo_labores WHERE id = :id")
    suspend fun getCatalogoLaborById(id: Int): CatalogoLaborEntity?

    @Query("SELECT * FROM catalogo_labores")
    suspend fun getCatalogoLabores(): List<CatalogoLaborEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertCosecha(cosecha: CosechaEntity): Long

    @Query("SELECT * FROM cosechas WHERE id = :id")
    suspend fun getCosechaById(id: Int): CosechaEntity?

    @Query("SELECT * FROM cosechas WHERE cultivo_id = :cultivoId")
    suspend fun getCosechasByCultivo(cultivoId: Int): List<CosechaEntity>

    @Query("SELECT * FROM compradores")
    suspend fun getAllCompradores(): List<CompradorEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertComprador(comprador: CompradorEntity): Long

    @Insert
    suspend fun insertVenta(venta: VentaEntity): Long

    @Query("SELECT * FROM ventas")
    suspend fun getAllVentas(): List<VentaEntity>

    @Query("SELECT * FROM ventas WHERE cosecha_id IN (SELECT id FROM cosechas WHERE cultivo_id = :cultivoId)")
    suspend fun getVentasByCultivo(cultivoId: Int): List<VentaEntity>

    @Insert
    suspend fun insertInsumoUsado(insumo: InsumoUsadoEntity): Long

    @Update
    suspend fun updateInsumoUsado(insumo: InsumoUsadoEntity)

    @Delete
    suspend fun deleteInsumoUsado(insumo: InsumoUsadoEntity)

    @Insert(onConflict = OnConflictStrategy.IGNORE)
    suspend fun insertCatalogoInsumo(insumo: CatalogoInsumoEntity): Long

    @Query("SELECT * FROM catalogo_insumos")
    suspend fun getCatalogoInsumos(): List<CatalogoInsumoEntity>

    @Query("SELECT * FROM catalogo_insumos WHERE id = :id")
    suspend fun getCatalogoInsumoById(id: Int): CatalogoInsumoEntity?

    @Query("""
        SELECT iu.* FROM insumos_usados iu 
        JOIN labores_realizadas lr ON iu.labor_id = lr.id 
        WHERE lr.cultivo_id = :cultivoId
    """)
    suspend fun getInsumosByCultivo(cultivoId: Int): List<InsumoUsadoEntity>

    @Query("SELECT * FROM insumos_usados WHERE labor_id = :laborId")
    suspend fun getInsumosByLaborId(laborId: Int): List<InsumoUsadoEntity>

    @Query("SELECT SUM(cantidad * costo_unitario) FROM insumos_usados WHERE labor_id = :laborId")
    suspend fun getCostoInsumosByLabor(laborId: Int): Double?

    @Insert
    suspend fun insertManoObra(manoObra: ManoObraEntity): Long

    @Query("SELECT * FROM mano_obra_tipo")
    suspend fun getManoObraTipos(): List<ManoObraTipoEntity>

    @Query("SELECT * FROM mano_obra WHERE labor_realizada_id = :laborId")
    suspend fun getManoObraByLabor(laborId: Int): List<ManoObraEntity>

    @Query("SELECT unidad_medida FROM catalogo_insumos WHERE id = :id")
    suspend fun getUnidadInsumo(id: Int): String?

    // Consultas para Reportes
    @Query("SELECT * FROM catalogo_labores WHERE id = :id")
    suspend fun getLaborTypeById(id: Int): CatalogoLaborEntity?

    @Query("""
        SELECT SUM(v.cantidad_vendida_kg * v.precio_por_kg) FROM ventas v
        JOIN cosechas c ON v.cosecha_id = c.id
        JOIN cultivos cul ON c.cultivo_id = cul.id
        JOIN terrenos t ON cul.terreno_id = t.id
        WHERE t.agricultor_id = :agricultorId
    """)
    suspend fun getIngresosTotalesByAgricultor(agricultorId: Int): Double?

    @Query("""
        SELECT 
            (SELECT SUM(lr.costo_mano_obra_total + lr.costo_maquinaria_total) FROM labores_realizadas lr
             JOIN cultivos cul ON lr.cultivo_id = cul.id
             JOIN terrenos t ON cul.terreno_id = t.id
             WHERE t.agricultor_id = :agricultorId)
            +
            (SELECT SUM(iu.cantidad * iu.costo_unitario + iu.costo_flete) FROM insumos_usados iu
             JOIN labores_realizadas lr ON iu.labor_id = lr.id
             JOIN cultivos cul ON lr.cultivo_id = cul.id
             JOIN terrenos t ON cul.terreno_id = t.id
             WHERE t.agricultor_id = :agricultorId)
    """)
    suspend fun getCostosTotalesByAgricultor(agricultorId: Int): Double?

    @Query("""
        SELECT cul.* FROM cultivos cul
        JOIN terrenos t ON cul.terreno_id = t.id
        WHERE t.agricultor_id = :agricultorId AND cul.estado = 'activo' AND cul.deleted_at IS NULL
        ORDER BY COALESCE(cul.fecha_siembra, cul.fecha_planificada, 0) DESC
        LIMIT 5
    """)
    suspend fun getCultivosEnCursoByAgricultor(agricultorId: Int): List<CultivoEntity>

    @Query("""
        SELECT c.* FROM cosechas c
        JOIN cultivos cul ON c.cultivo_id = cul.id
        JOIN terrenos t ON cul.terreno_id = t.id
        WHERE t.agricultor_id = :agricultorId
        ORDER BY c.fecha_cosecha DESC
        LIMIT 10
    """)
    suspend fun getCosechasRecientesByAgricultor(agricultorId: Int): List<CosechaEntity>
}
