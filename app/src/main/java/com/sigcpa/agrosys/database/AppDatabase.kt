package com.sigcpa.agrosys.database

import android.content.Context
import android.util.Log
import androidx.room.Database
import androidx.room.Room
import androidx.room.RoomDatabase
import androidx.sqlite.db.SupportSQLiteDatabase
import com.sigcpa.agrosys.database.dao.*
import com.sigcpa.agrosys.database.entities.*

@Database(
    entities = [
        RolEntity::class, OrganizacionEntity::class, UsuarioEntity::class,
        MiembroOrganizacionEntity::class, SolicitudUsuarioEntity::class,
        TerrenoEntity::class, CatalogoCultivoEntity::class,
        CultivoEntity::class, CatalogoLaborEntity::class, LaborRealizadaEntity::class,
        CatalogoInsumoEntity::class, ProveedorEntity::class, InsumoUsadoEntity::class,
        CosechaEntity::class, CompradorEntity::class, VentaEntity::class,
        ArchivoMultimediaEntity::class, NotificacionEntity::class,
        ManoObraTipoEntity::class, ManoObraEntity::class,
        TipoRedSocialEntity::class, RedSocialEntity::class
    ],
    version = 10,
    exportSchema = false
)
abstract class AppDatabase : RoomDatabase() {
    abstract fun userDao(): UserDao
    abstract fun assetDao(): AssetDao

    companion object {
        @Volatile
        private var INSTANCE: AppDatabase? = null

        fun getDatabase(context: Context): AppDatabase {
            return INSTANCE ?: synchronized(this) {
                val instance = Room.databaseBuilder(
                    context.applicationContext,
                    AppDatabase::class.java,
                    "agrosys_offline_db"
                )
                .addCallback(object : Callback() {
                    override fun onCreate(db: SupportSQLiteDatabase) {
                        super.onCreate(db)
                        insertInitialData(db)
                    }
                })
                .fallbackToDestructiveMigration()
                .build()
                INSTANCE = instance
                instance
            }
        }

        private fun insertInitialData(db: SupportSQLiteDatabase) {
            val now = System.currentTimeMillis() / 1000
            try {
                // Roles base
                db.execSQL("INSERT INTO roles (nombre) VALUES ('super_admin')")
                db.execSQL("INSERT INTO roles (nombre) VALUES ('admin')")
                db.execSQL("INSERT INTO roles (nombre) VALUES ('supervisor')")
                db.execSQL("INSERT INTO roles (nombre) VALUES ('usuario')")

                // Catálogo de labores base
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Preparación de terreno', 'preparacion', 'Arado, rastra y nivelación', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Siembra', 'preparacion', 'Colocación de semillas o plantones', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Riego', 'mantenimiento', 'Aplicación de agua', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Control de plagas', 'mantenimiento', 'Aplicación de pesticidas', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Aporque', 'mantenimiento', 'Acumulación de tierra en la base', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Deshierbe', 'mantenimiento', 'Eliminación de maleza', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Fertilización', 'mantenimiento', 'Aplicación de fertilizantes', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Poda', 'mantenimiento', 'Corte de ramas o plantas', 1)")
                db.execSQL("INSERT INTO catalogo_labores (nombre, categoria, descripcion, sincronizado) VALUES ('Cosecha', 'cosecha', 'Recolección de frutos', 1)")

                // Catálogo de insumos base
                db.execSQL("INSERT INTO catalogo_insumos (nombre, categoria, unidad_medida, sincronizado) VALUES ('Urea', 'fertilizante', 'kg', 1)")
                db.execSQL("INSERT INTO catalogo_insumos (nombre, categoria, unidad_medida, sincronizado) VALUES ('Fosfato diamónico', 'fertilizante', 'kg', 1)")
                db.execSQL("INSERT INTO catalogo_insumos (nombre, categoria, unidad_medida, sincronizado) VALUES ('Cloruro de potasio', 'fertilizante', 'kg', 1)")
                db.execSQL("INSERT INTO catalogo_insumos (nombre, categoria, unidad_medida, sincronizado) VALUES ('Glifosato', 'pesticida', 'litros', 1)")
                db.execSQL("INSERT INTO catalogo_insumos (nombre, categoria, unidad_medida, sincronizado) VALUES ('Semilla de maíz', 'semilla', 'kg', 1)")
                db.execSQL("INSERT INTO catalogo_insumos (nombre, categoria, unidad_medida, sincronizado) VALUES ('Semilla de papa', 'semilla', 'kg', 1)")

                // Tipos de Mano de Obra
                db.execSQL("INSERT INTO mano_obra_tipo (nombre) VALUES ('Varón')")
                db.execSQL("INSERT INTO mano_obra_tipo (nombre) VALUES ('Mujer')")
                db.execSQL("INSERT INTO mano_obra_tipo (nombre) VALUES ('Otros')")

                // Tipos de Redes Sociales
                db.execSQL("INSERT INTO tipos_red_social (nombre) VALUES ('Facebook')")
                db.execSQL("INSERT INTO tipos_red_social (nombre) VALUES ('Instagram')")
                db.execSQL("INSERT INTO tipos_red_social (nombre) VALUES ('TikTok')")
                db.execSQL("INSERT INTO tipos_red_social (nombre) VALUES ('YouTube')")
                db.execSQL("INSERT INTO tipos_red_social (nombre) VALUES ('WhatsApp')")
                db.execSQL("INSERT INTO tipos_red_social (nombre) VALUES ('LinkedIn')")

                Log.d("DB_INIT", "Catálogos base sincronizados con esquema SQL")
            } catch (e: Exception) {
                Log.e("DB_INIT", "Error al insertar catálogos: ${e.message}")
            }
        }
    }
}
