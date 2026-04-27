package com.sigcpa.agrosys.repository

import android.content.Context
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class AssetRepository(context: Context) {
    private val db = AppDatabase.getDatabase(context)
    private val assetDao = db.assetDao()
    private val userDao = db.userDao()

    suspend fun saveTerreno(terreno: TerrenoEntity): Boolean = withContext(Dispatchers.IO) {
        try {
            assetDao.insertTerreno(terreno)
            true
        } catch (e: Exception) {
            e.printStackTrace()
            false
        }
    }

    suspend fun getAgricultorIdByUserId(userId: Int): Int? = withContext(Dispatchers.IO) {
        userDao.getUsuarioById(userId)?.usuario?.id
    }
}
