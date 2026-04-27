package com.sigcpa.agrosys.database

import android.content.Context
import androidx.sqlite.db.SupportSQLiteDatabase
import androidx.test.core.app.ApplicationProvider
import androidx.test.ext.junit.runners.AndroidJUnit4
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.joinAll
import kotlinx.coroutines.launch
import kotlinx.coroutines.runBlocking
import org.junit.After
import org.junit.Assert.*
import org.junit.Before
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class DatabasePerformanceTest {

    private lateinit var db: SupportSQLiteDatabase
    private lateinit var appDatabase: AppDatabase

    @Before
    fun setUp() {
        val context = ApplicationProvider.getApplicationContext<Context>()
        // Usamos la base de datos real o una en memoria para pruebas
        appDatabase = AppDatabase.getDatabase(context)
        db = appDatabase.openHelper.writableDatabase
        
        // ACTIVAR CLAVES FORÁNEAS
        db.execSQL("PRAGMA foreign_keys = ON;")
        
        cleanDatabase()
        insertTestData()
    }

    @After
    fun tearDown() {
        appDatabase.close()
    }

    private fun cleanDatabase() {
        db.execSQL("DELETE FROM labores_realizadas")
        db.execSQL("DELETE FROM cultivos")
        db.execSQL("DELETE FROM terrenos")
        db.execSQL("DELETE FROM usuarios")
        db.execSQL("DELETE FROM organizaciones")
    }

    private fun insertTestData() {
        // 1. Organización
        db.execSQL("""
            INSERT INTO organizaciones (id, nombre, ruc, direccion, tipo) 
            VALUES (1, 'Cooperativa AgroPrueba', '20123456789', 'Av. Prueba 123', 'cooperativa')
        """)
        
        // 2. Usuario
        db.execSQL("""
            INSERT INTO usuarios (id, organizacion_id, nombre, apellidos, email, password, rol, is_activo)
            VALUES (1, 1, 'Juan', 'Perez Prueba', 'juan@test.com', 'hash123', 'usuario', 1)
        """)
        
        // 3. Terreno
        db.execSQL("""
            INSERT INTO terrenos (id, usuario_id, nombre, area_hectareas, tipo_tenencia)
            VALUES (1, 1, 'Terreno Prueba', 5.0, 'propio')
        """)
        
        // 4. Catálogo de cultivo (ya insertado por el callback de la DB, pero aseguramos uno)
        db.execSQL("INSERT OR IGNORE INTO catalogo_cultivos (id, nombre, tipo_ciclo) VALUES (1, 'Papa Yungay', 'ciclo_corto')")
        
        // 5. Cultivo
        db.execSQL("""
            INSERT INTO cultivos (id, terreno_id, catalogo_cultivo_id, nombre_lote, fecha_siembra, estado)
            VALUES (1, 1, 1, 'Campaña Prueba 2025', ${System.currentTimeMillis() / 1000}, 'activo')
        """)
    }

    @Test
    fun testInsercionMasivaLabores() {
        val cantidadRegistros = 1000
        val startTime = System.currentTimeMillis()
        
        db.beginTransaction()
        try {
            repeat(cantidadRegistros) { index ->
                db.execSQL("""
                    INSERT INTO labores_realizadas (
                        cultivo_id, catalogo_labor_id, fecha_realizacion, 
                        sincronizado, observaciones
                    ) VALUES (1, 1, ${System.currentTimeMillis() / 1000}, 0, 'Labor de prueba #$index')
                """)
            }
            db.setTransactionSuccessful()
        } finally {
            db.endTransaction()
        }
        
        val endTime = System.currentTimeMillis()
        val tiempoTotal = endTime - startTime
        
        val cursor = db.query("SELECT COUNT(*) FROM labores_realizadas")
        cursor.moveToFirst()
        val count = cursor.getInt(0)
        cursor.close()
        
        assertEquals(cantidadRegistros, count)
        
        println("=== PRUEBA INSERCIÓN MASIVA ===")
        println("Registros insertados: $cantidadRegistros")
        println("Tiempo total: ${tiempoTotal}ms")
        println("Promedio por registro: ${tiempoTotal.toFloat() / cantidadRegistros}ms")
        println("================================")
        
        assertTrue("Inserción muy lenta: ${tiempoTotal}ms", tiempoTotal < 2000)
    }

    @Test(expected = android.database.sqlite.SQLiteConstraintException::class)
    fun testForeignKeyConstraint() {
        // Intentar insertar cultivo con terreno_id inexistente (999)
        db.execSQL("""
            INSERT INTO cultivos (terreno_id, catalogo_cultivo_id, nombre_lote, fecha_siembra, estado)
            VALUES (999, 1, 'Cultivo Huérfano', ${System.currentTimeMillis() / 1000}, 'activo')
        """)
    }

    @Test
    fun testCascadeDeleteTerreno() {
        // Eliminar terreno (debe eliminar cultivos en cascada si el esquema está bien definido)
        db.execSQL("DELETE FROM terrenos WHERE id = 1")
        
        val cursor = db.query("SELECT COUNT(*) FROM cultivos WHERE terreno_id = 1")
        cursor.moveToFirst()
        val count = cursor.getInt(0)
        cursor.close()
        
        assertEquals(0, count)
    }

    @Test
    fun testRendimientoConcurrente() = runBlocking {
        val numThreads = 5
        val operacionesPorThread = 100
        val startTime = System.currentTimeMillis()
        
        val jobs = List(numThreads) { threadId ->
            launch(Dispatchers.IO) {
                repeat(operacionesPorThread) { opIndex ->
                    synchronized(db) {
                        db.execSQL("""
                            INSERT INTO labores_realizadas (
                                cultivo_id, catalogo_labor_id, fecha_realizacion, sincronizado
                            ) VALUES (1, 1, ${System.currentTimeMillis() / 1000}, 0)
                        """)
                    }
                }
            }
        }
        
        jobs.joinAll()
        
        val endTime = System.currentTimeMillis()
        println("=== PRUEBA RENDIMIENTO CONCURRENTE ===")
        println("Tiempo total: ${endTime - startTime}ms")
        println("======================================")
    }
}
