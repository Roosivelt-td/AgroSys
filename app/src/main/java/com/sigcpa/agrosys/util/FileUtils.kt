package com.sigcpa.agrosys.util

import android.content.Context
import android.net.Uri
import java.io.File
import java.io.FileOutputStream
import java.io.InputStream

object FileUtils {

    /**
     * Guarda una imagen en el almacenamiento interno siguiendo la estructura:
     * id_nombreuser/subfolder/filename
     */
    fun saveImageLocally(
        context: Context,
        uri: Uri,
        userId: Int,
        userName: String,
        subFolder: String,
        fileName: String
    ): String? {
        return try {
            val rootDir = File(context.filesDir, "usuarioAgroSys")
            val userDirName = "${userId}_${userName.replace(" ", "_").lowercase()}"
            val baseDir = File(rootDir, userDirName)
            val targetDir = File(baseDir, subFolder)
            
            if (!targetDir.exists()) {
                targetDir.mkdirs()
            }

            val file = File(targetDir, fileName)
            val inputStream: InputStream? = context.contentResolver.openInputStream(uri)
            val outputStream = FileOutputStream(file)
            
            inputStream?.use { input ->
                outputStream.use { output ->
                    input.copyTo(output)
                }
            }
            
            file.absolutePath
        } catch (e: Exception) {
            e.printStackTrace()
            null
        }
    }

    /**
     * Estructuras predefinidas según requerimiento:
     * id_nombreuser/perfil/(foto, portada)
     * id_nombreuser/terreno
     * id_nombreuser/cultivo
     * id_nombreuser/labores/(arado, siembra, riego.....cada uno en sus carpetas)
     * id_nombreuser/venta
     */
    
    fun getProfileFolder(userId: Int, userName: String) = "perfil"
    fun getTerrenoFolder(userId: Int, userName: String) = "terreno"
    fun getCultivoFolder(userId: Int, userName: String) = "cultivo"
    fun getLaboresFolder(laborName: String) = "labores/${laborName.replace(" ", "_").lowercase()}"
    fun getVentaFolder(userId: Int, userName: String) = "venta"
}
