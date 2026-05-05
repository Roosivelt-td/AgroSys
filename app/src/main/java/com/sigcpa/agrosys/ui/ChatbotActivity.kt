package com.sigcpa.agrosys.ui

import android.graphics.Color
import android.os.Bundle
import android.view.Gravity
import android.view.LayoutInflater
import android.view.ViewGroup
import android.widget.LinearLayout
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.databinding.ActivityChatbotBinding
import com.sigcpa.agrosys.databinding.ItemChatMessageBinding
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject

data class ChatMessage(val text: String, val isUser: Boolean)

class ChatbotActivity : AppCompatActivity() {
    private lateinit var binding: ActivityChatbotBinding
    private val messages = mutableListOf<ChatMessage>()
    private lateinit var adapter: ChatAdapter

    // REEMPLAZA ESTO CON TU URL DE WEBHOOK DE n8n
    //private val n8nWebhookUrl = "https://juanmaria123.app.n8n.cloud/webhook/7b1f428c-a0f9-4762-9468-89a466c25752"
    private val n8nWebhookUrl = "https://juanmaria123.app.n8n.cloud/webhook/7b1f428c-a0f9-4762-9468-89a466c25752"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityChatbotBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.btnBack.setOnClickListener { finish() }
        setupChat()

        binding.btnSend.setOnClickListener {
            val text = binding.etMessage.text.toString().trim()
            if (text.isNotEmpty()) {
                sendMessage(text)
            }
        }
        
        // Mensaje inicial
        addMessage(ChatMessage("¡Hola! Soy tu asistente de AgroSys. ¿En qué puedo ayudarte hoy?", false))
    }

    private fun setupChat() {
        adapter = ChatAdapter(messages)
        binding.rvChat.layoutManager = LinearLayoutManager(this).apply {
            stackFromEnd = true
        }
        binding.rvChat.adapter = adapter
    }

    private fun sendMessage(text: String) {
        addMessage(ChatMessage(text, true))
        binding.etMessage.setText("")
        
        lifecycleScope.launch {
            val response = callN8n(text)
            addMessage(ChatMessage(response, false))
        }
    }

    private fun addMessage(msg: ChatMessage) {
        messages.add(msg)
        adapter.notifyItemInserted(messages.size - 1)
        binding.rvChat.scrollToPosition(messages.size - 1)
    }

    private suspend fun callN8n(userMessage: String): String {
        return withContext(Dispatchers.IO) {
            try {
                val client = OkHttpClient()
                val json = JSONObject().apply {
                    put("message", userMessage)
                    put("timestamp", System.currentTimeMillis())
                }
                
                val body = json.toString().toRequestBody("application/json".toMediaType())
                val request = Request.Builder()
                    .url(n8nWebhookUrl)
                    .post(body)
                    .build()

                val response = client.newCall(request).execute()
                if (response.isSuccessful) {
                    val responseData = response.body?.string()
                    // n8n suele devolver un JSON, aquí extraemos el campo "output" o "response"
                    // Ajusta esto según lo que devuelva tu workflow
                    val jsonResponse = JSONObject(responseData ?: "{}")
                    jsonResponse.optString("output", jsonResponse.optString("response", "He recibido tu mensaje."))
                } else {
                    "Error de conexión con el asistente."
                }
            } catch (e: Exception) {
                "Lo siento, no puedo responder en este momento."
            }
        }
    }

    inner class ChatAdapter(private val list: List<ChatMessage>) : RecyclerView.Adapter<ChatAdapter.ViewHolder>() {
        inner class ViewHolder(val binding: ItemChatMessageBinding) : RecyclerView.ViewHolder(binding.root)

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            return ViewHolder(ItemChatMessageBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        }

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val msg = list[position]
            holder.binding.tvMessage.text = msg.text
            
            val params = holder.binding.cardMessage.layoutParams as LinearLayout.LayoutParams
            if (msg.isUser) {
                params.gravity = Gravity.END
                holder.binding.cardMessage.setCardBackgroundColor(Color.parseColor("#dcfce7"))
            } else {
                params.gravity = Gravity.START
                holder.binding.cardMessage.setCardBackgroundColor(Color.parseColor("#ffffff"))
            }
            holder.binding.cardMessage.layoutParams = params
        }

        override fun getItemCount() = list.size
    }
}