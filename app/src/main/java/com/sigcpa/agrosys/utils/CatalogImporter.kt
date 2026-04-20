package com.sigcpa.agrosys.utils

import android.content.Context
import android.net.Uri
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.io.InputStreamReader

class CatalogImporter(private val context: Context) {
    private val db = AppDatabase.getDatabase(context)

    suspend fun importCultivosFromJson(uri: Uri): Result<Int> = withContext(Dispatchers.IO) {
        try {
            val inputStream = context.contentResolver.openInputStream(uri)
            val reader = InputStreamReader(inputStream)
            val listType = object : TypeToken<List<CatalogoCultivoEntity>>() {}.type
            val cultivos: List<CatalogoCultivoEntity> = Gson().fromJson(reader, listType)

            var importedCount = 0
            cultivos.forEach { cultivo ->
                db.assetDao().insertCatalogoCultivo(cultivo)
                importedCount++
            }
            Result.success(importedCount)
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
