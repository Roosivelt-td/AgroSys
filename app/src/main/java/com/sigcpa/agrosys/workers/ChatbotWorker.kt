package com.sigcpa.agrosys.workers

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import androidx.core.app.NotificationCompat
import androidx.work.CoroutineWorker
import androidx.work.WorkerParameters
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.ChatMessageHistoryEntity
import com.sigcpa.agrosys.ui.ChatbotActivity
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject
import java.util.concurrent.TimeUnit

class ChatbotWorker(context: Context, params: WorkerParameters) : CoroutineWorker(context, params) {

    private val db = AppDatabase.getDatabase(context)
    private val n8nWebhookUrl = "https://juanmaria123.app.n8n.cloud/webhook/dac9d9ca-6cbd-4f72-bdcf-9562e76682bd/chat"

    override suspend fun doWork(): Result = withContext(Dispatchers.IO) {
        val sessionId = inputData.getInt("SESSION_ID", -1)
        val userMessage = inputData.getString("USER_MESSAGE") ?: return@withContext Result.failure()

        if (sessionId == -1) return@withContext Result.failure()

        try {
            val client = OkHttpClient.Builder()
                .connectTimeout(30, TimeUnit.SECONDS)
                .readTimeout(60, TimeUnit.SECONDS)
                .build()

            val json = JSONObject().apply {
                put("action", "sendMessage")
                put("chatInput", userMessage)
                put("sessionId", sessionId.toString())
            }

            val body = json.toString().toRequestBody("application/json".toMediaType())
            val request = Request.Builder()
                .url(n8nWebhookUrl)
                .post(body)
                .build()

            val response = client.newCall(request).execute()
            if (response.isSuccessful) {
                val responseData = response.body?.string()
                val jsonResponse = JSONObject(responseData ?: "{}")
                val aiResponse = jsonResponse.optString("output", jsonResponse.optString("response", "He recibido tu mensaje."))

                // Guardar en BD
                db.assetDao().insertChatMessageHistory(ChatMessageHistoryEntity(
                    session_id = sessionId,
                    text = aiResponse,
                    is_user = false
                ))

                // Actualizar timestamp de la sesión
                val session = db.assetDao().getChatSessionById(sessionId)
                session?.let {
                    db.assetDao().updateChatSession(it.copy(last_message_at = System.currentTimeMillis() / 1000))
                }

                showNotification(sessionId, aiResponse)
                Result.success()
            } else {
                Result.retry()
            }
        } catch (e: Exception) {
            Result.retry()
        }
    }

    private fun showNotification(sessionId: Int, message: String) {
        val notificationManager = applicationContext.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        val channelId = "chatbot_responses"

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(channelId, "Chatbot AgroSys", NotificationManager.IMPORTANCE_DEFAULT)
            notificationManager.createNotificationChannel(channel)
        }

        val intent = Intent(applicationContext, ChatbotActivity::class.java).apply {
            putExtra("SESSION_ID", sessionId)
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
        }

        val pendingIntent = PendingIntent.getActivity(
            applicationContext, 
            sessionId, 
            intent, 
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        val notification = NotificationCompat.Builder(applicationContext, channelId)
            .setSmallIcon(R.drawable.ic_chatbot_02)
            .setContentTitle("AgroBot tiene una respuesta")
            .setContentText(message)
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .setPriority(NotificationCompat.PRIORITY_DEFAULT)
            .build()

        notificationManager.notify(sessionId, notification)
    }
}
