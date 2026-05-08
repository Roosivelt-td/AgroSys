package com.sigcpa.agrosys.ui

import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Color
import android.os.Build
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.constraintlayout.widget.ConstraintLayout
import androidx.core.content.ContextCompat
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import androidx.work.Data
import androidx.work.OneTimeWorkRequestBuilder
import androidx.work.WorkManager
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.ChatMessageHistoryEntity
import com.sigcpa.agrosys.databinding.ActivityChatbotBinding
import com.sigcpa.agrosys.databinding.ItemChatMessageBinding
import com.sigcpa.agrosys.workers.ChatbotWorker
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.text.SimpleDateFormat
import java.util.*

class ChatbotActivity : AppCompatActivity() {
    private lateinit var binding: ActivityChatbotBinding
    private val messages = mutableListOf<ChatMessage>()
    private lateinit var adapter: ChatAdapter
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentSessionId: Int = -1

    private val requestNotificationPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { _ -> }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityChatbotBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Forzar barra de estado verde
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        val initialHeaderPaddingTop = binding.headerChat.paddingTop
        val initialInputPaddingBottom = binding.inputContainer.paddingBottom

        // Manejar Insets
        ViewCompat.setOnApplyWindowInsetsListener(binding.root) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            val ime = insets.getInsets(WindowInsetsCompat.Type.ime())
            binding.headerChat.setPadding(
                binding.headerChat.paddingLeft,
                initialHeaderPaddingTop + systemBars.top,
                binding.headerChat.paddingRight,
                binding.headerChat.paddingBottom
            )
            val bottomPadding = if (ime.bottom > 0) ime.bottom else systemBars.bottom
            binding.inputContainer.setPadding(
                binding.inputContainer.paddingLeft,
                binding.inputContainer.paddingTop,
                binding.inputContainer.paddingRight,
                initialInputPaddingBottom + bottomPadding
            )
            insets
        }

        currentSessionId = intent.getIntExtra("SESSION_ID", -1)
        binding.btnBack.setOnClickListener { finish() }
        setupChat()

        binding.btnSend.setOnClickListener {
            val text = binding.etMessage.text.toString().trim()
            if (text.isNotEmpty()) {
                sendMessage(text)
            }
        }
        
        val initialMessage = intent.getStringExtra("INITIAL_MESSAGE")
        if (initialMessage != null) {
            sendMessage(initialMessage)
        }
        
        observeMessages()
        checkNotificationPermission()
    }

    private fun checkNotificationPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                requestNotificationPermissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
            }
        }
    }

    private fun setupChat() {
        adapter = ChatAdapter(messages)
        binding.rvChat.layoutManager = LinearLayoutManager(this).apply {
            stackFromEnd = true
        }
        binding.rvChat.adapter = adapter
    }

    private fun observeMessages() {
        lifecycleScope.launch {
            if (currentSessionId != -1) {
                val session = db.assetDao().getChatSessionById(currentSessionId)
                session?.let { binding.tvChatTitle.text = it.titulo }

                db.assetDao().getChatMessagesBySessionFlow(currentSessionId).collectLatest { history ->
                    messages.clear()
                    if (history.isEmpty()) {
                        messages.add(ChatMessage("¡Hola! Soy tu asistente de AgroSys. ¿En qué puedo ayudarte hoy?", false))
                    } else {
                        history.forEach { 
                            messages.add(ChatMessage(it.text, it.is_user, it.created_at * 1000))
                        }
                    }
                    adapter.notifyDataSetChanged()
                    binding.rvChat.scrollToPosition(messages.size - 1)
                }
            }
        }
    }

    private fun sendMessage(text: String) {
        // El mensaje del usuario se guarda localmente de inmediato para feedback rápido
        binding.etMessage.setText("")
        
        lifecycleScope.launch {
            if (currentSessionId != -1) {
                db.assetDao().insertChatMessageHistory(ChatMessageHistoryEntity(
                    session_id = currentSessionId,
                    text = text,
                    is_user = true
                ))
                
                val history = db.assetDao().getChatMessagesBySession(currentSessionId)
                if (history.size == 1) { 
                    updateSessionTitle(text)
                }
                
                updateSessionTimestamp()

                // Programar el trabajo en segundo plano para la respuesta de la IA
                val data = Data.Builder()
                    .putInt("SESSION_ID", currentSessionId)
                    .putString("USER_MESSAGE", text)
                    .build()

                val workRequest = OneTimeWorkRequestBuilder<ChatbotWorker>()
                    .setInputData(data)
                    .build()

                WorkManager.getInstance(this@ChatbotActivity).enqueue(workRequest)
            }
        }
    }

    private suspend fun updateSessionTitle(firstMessage: String) {
        if (currentSessionId == -1) return
        val session = db.assetDao().getChatSessionById(currentSessionId)
        session?.let {
            if (!it.is_ai) return@let 
            var summary = firstMessage.trim()
            if (summary.length > 35) {
                val words = summary.split(" ")
                summary = if (words.size > 5) words.take(5).joinToString(" ") + "..." else summary.substring(0, 32) + "..."
            }
            summary = summary.removePrefix("¿").removePrefix("?").replaceFirstChar { it.uppercase() }
            db.assetDao().updateChatSession(it.copy(titulo = summary))
            withContext(Dispatchers.Main) {
                binding.tvChatTitle.text = summary
            }
        }
    }

    private suspend fun updateSessionTimestamp() {
        if (currentSessionId != -1) {
            val session = db.assetDao().getChatSessionById(currentSessionId)
            session?.let {
                db.assetDao().updateChatSession(it.copy(last_message_at = System.currentTimeMillis() / 1000))
            }
        }
    }

    inner class ChatAdapter(private val list: List<ChatMessage>) : RecyclerView.Adapter<ChatAdapter.ViewHolder>() {
        inner class ViewHolder(val binding: ItemChatMessageBinding) : RecyclerView.ViewHolder(binding.root)
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder = ViewHolder(ItemChatMessageBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun getItemCount() = list.size
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val msg = list[position]
            holder.binding.tvMessage.text = msg.text
            val params = holder.binding.cardMessage.layoutParams as ConstraintLayout.LayoutParams
            val sdf = SimpleDateFormat("HH:mm", Locale.getDefault())
            holder.binding.tvTime.text = sdf.format(Date(msg.timestamp))

            if (msg.isUser) {
                params.startToStart = ConstraintLayout.LayoutParams.UNSET
                params.endToEnd = ConstraintLayout.LayoutParams.PARENT_ID
                params.horizontalBias = 1.0f
                holder.binding.cardMessage.setCardBackgroundColor(Color.parseColor("#dcfce7")) 
                holder.binding.tvMessage.setTextColor(Color.parseColor("#1e293b"))
            } else {
                params.startToStart = ConstraintLayout.LayoutParams.PARENT_ID
                params.endToEnd = ConstraintLayout.LayoutParams.UNSET
                params.horizontalBias = 0.0f
                holder.binding.cardMessage.setCardBackgroundColor(Color.parseColor("#f1f5f9"))
                holder.binding.tvMessage.setTextColor(Color.parseColor("#1e293b"))
            }
            holder.binding.cardMessage.layoutParams = params
        }
    }
}
