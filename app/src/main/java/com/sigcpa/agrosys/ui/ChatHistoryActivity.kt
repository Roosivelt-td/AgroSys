package com.sigcpa.agrosys.ui

import android.content.Context
import android.content.Intent
import android.graphics.Canvas
import android.graphics.Color
import android.graphics.Paint
import android.graphics.RectF
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.EditText
import android.widget.PopupMenu
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.ItemTouchHelper
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.ChatSessionEntity
import com.sigcpa.agrosys.databinding.ActivityChatHistoryBinding
import com.sigcpa.agrosys.databinding.ItemChatSessionBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class ChatHistoryActivity : AppCompatActivity() {
    private lateinit var binding: ActivityChatHistoryBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var userId: Int = -1
    private lateinit var sessionAdapter: ChatSessionAdapter
    private var showingArchived = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityChatHistoryBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Forzar barra de estado verde
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        val initialHeaderPaddingTop = binding.headerChatHistory.paddingTop
        val initialBottomPadding = binding.bottomOptions.paddingBottom

        // Manejar Insets
        ViewCompat.setOnApplyWindowInsetsListener(binding.root) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            val ime = insets.getInsets(WindowInsetsCompat.Type.ime())
            
            binding.headerChatHistory.setPadding(
                binding.headerChatHistory.paddingLeft,
                initialHeaderPaddingTop + systemBars.top,
                binding.headerChatHistory.paddingRight,
                binding.headerChatHistory.paddingBottom
            )

            val bottomPadding = if (ime.bottom > 0) ime.bottom else systemBars.bottom
            binding.bottomOptions.setPadding(
                binding.bottomOptions.paddingLeft,
                binding.bottomOptions.paddingTop,
                binding.bottomOptions.paddingRight,
                initialBottomPadding + bottomPadding
            )
            insets
        }

        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        userId = sharedPref.getInt("USER_ID", -1)

        binding.btnBack.setOnClickListener { 
            if (showingArchived) {
                showingArchived = false
                loadChatHistory()
            } else {
                finish()
            }
        }
        
        binding.btnMenuMore.setOnClickListener { view ->
            val popup = PopupMenu(this, view)
            popup.menu.add(if (showingArchived) "Ver chats activos" else "Ver chats archivados")
            popup.setOnMenuItemClickListener {
                showingArchived = !showingArchived
                loadChatHistory()
                true
            }
            popup.show()
        }

        binding.fabChatIA.setOnClickListener {
            startNewChatWithIA()
        }

        binding.fabContacts.setOnClickListener {
            startActivity(Intent(this, ContactsActivity::class.java))
        }

        binding.btnHelpSiembra.setOnClickListener { startNewChatWithIA("¿Cómo sembrar correctamente mis cultivos?") }
        binding.btnHelpRiego.setOnClickListener { startNewChatWithIA("¿Cuál es el mejor sistema de riego?") }
        binding.btnHelpPlagas.setOnClickListener { startNewChatWithIA("¿Cómo puedo controlar las plagas de forma natural?") }
    }

    override fun onResume() {
        super.onResume()
        lifecycleScope.launch {
            db.assetDao().cleanEmptyChatSessions()
            loadChatHistory()
        }
    }

    private fun startNewChatWithIA(initialMessage: String? = null) {
        lifecycleScope.launch {
            val newSession = ChatSessionEntity(
                usuario_id = userId,
                titulo = "Consulta IA " + SimpleDateFormat("dd/MM HH:mm", Locale.getDefault()).format(Date()),
                last_message_at = System.currentTimeMillis() / 1000,
                is_ai = true
            )
            val sessionId = db.assetDao().insertChatSession(newSession)
            
            val intent = Intent(this@ChatHistoryActivity, ChatbotActivity::class.java)
            intent.putExtra("SESSION_ID", sessionId.toInt())
            if (initialMessage != null) {
                intent.putExtra("INITIAL_MESSAGE", initialMessage)
            }
            startActivity(intent)
        }
    }

    private fun loadChatHistory() {
        lifecycleScope.launch {
            val sessions = if (showingArchived) {
                db.assetDao().getArchivedChatSessionsByUsuario(userId)
            } else {
                db.assetDao().getChatSessionsByUsuario(userId)
            }.toMutableList()
            
            sessionAdapter = ChatSessionAdapter(sessions)
            binding.rvChatHistory.layoutManager = LinearLayoutManager(this@ChatHistoryActivity)
            binding.rvChatHistory.adapter = sessionAdapter

            if (!showingArchived) setupSwipeToDelete()
        }
    }

    private fun setupSwipeToDelete() {
        val swipeHandler = object : ItemTouchHelper.SimpleCallback(0, ItemTouchHelper.LEFT) {
            private val deleteIcon = ContextCompat.getDrawable(this@ChatHistoryActivity, R.drawable.ic_delet)
            private val intrinsicWidth = deleteIcon?.intrinsicWidth ?: 0
            private val intrinsicHeight = deleteIcon?.intrinsicHeight ?: 0
            private val paint = Paint()

            override fun onMove(recyclerView: RecyclerView, viewHolder: RecyclerView.ViewHolder, target: RecyclerView.ViewHolder): Boolean = false

            override fun onSwiped(viewHolder: RecyclerView.ViewHolder, direction: Int) {
                sessionAdapter.removeAt(viewHolder.adapterPosition)
            }
            
            override fun onChildDraw(c: Canvas, recyclerView: RecyclerView, viewHolder: RecyclerView.ViewHolder, dX: Float, dY: Float, actionState: Int, isCurrentlyActive: Boolean) {
                val itemView = viewHolder.itemView
                val itemHeight = itemView.bottom - itemView.top
                val threshold = -itemView.width * 0.4f
                val isPastThreshold = dX < threshold

                if (dX < 0) {
                    paint.color = if (isPastThreshold) Color.parseColor("#44EF4444") else Color.parseColor("#11000000")
                    val background = RectF(itemView.right.toFloat() + dX, itemView.top.toFloat(), itemView.right.toFloat(), itemView.bottom.toFloat())
                    c.drawRect(background, paint)

                    deleteIcon?.let {
                        val iconTop = itemView.top + (itemHeight - intrinsicHeight) / 2
                        val iconMargin = (itemHeight - intrinsicHeight) / 2
                        val iconLeft = itemView.right - iconMargin - intrinsicWidth
                        val iconRight = itemView.right - iconMargin
                        val iconBottom = iconTop + intrinsicHeight
                        it.setBounds(iconLeft, iconTop, iconRight, iconBottom)
                        it.setTint(if (isPastThreshold) Color.RED else Color.GRAY)
                        it.draw(c)
                    }
                }
                super.onChildDraw(c, recyclerView, viewHolder, dX, dY, actionState, isCurrentlyActive)
            }
        }
        ItemTouchHelper(swipeHandler).attachToRecyclerView(binding.rvChatHistory)
    }

    private fun showChatMenu(session: ChatSessionEntity, position: Int) {
        val optionsList = mutableListOf<String>()
        
        // Punto: Solo permitir editar si es IA
        if (session.is_ai) {
            optionsList.add("Editar título")
        }
        
        optionsList.add(if (showingArchived) "Desarchivar chat" else "Archivar chat")
        optionsList.add("Eliminar chat")
        
        val options = optionsList.toTypedArray()
        
        AlertDialog.Builder(this)
            .setTitle(session.titulo)
            .setItems(options) { _, which ->
                val selectedOption = options[which]
                when (selectedOption) {
                    "Editar título" -> showEditDialog(session)
                    "Archivar chat", "Desarchivar chat" -> toggleArchiveChat(session, position)
                    "Eliminar chat" -> showDeleteConfirmDialog(session, position)
                }
            }
            .show()
    }

    private fun toggleArchiveChat(session: ChatSessionEntity, position: Int) {
        lifecycleScope.launch {
            db.assetDao().updateChatSession(session.copy(is_archived = !showingArchived))
            sessionAdapter.removeLocalAt(position)
            val msg = if (showingArchived) "Chat desarchivado" else "Chat archivado"
            Toast.makeText(this@ChatHistoryActivity, msg, Toast.LENGTH_SHORT).show()
        }
    }

    private fun showDeleteConfirmDialog(session: ChatSessionEntity, position: Int) {
        AlertDialog.Builder(this)
            .setTitle("Eliminar chat")
            .setMessage("¿Estás seguro de que deseas eliminar este chat permanentemente?")
            .setPositiveButton("Eliminar") { _, _ ->
                sessionAdapter.removeAt(position)
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun showEditDialog(session: ChatSessionEntity) {
        val editText = EditText(this)
        editText.setText(session.titulo)
        editText.setSelection(session.titulo.length)
        
        AlertDialog.Builder(this)
            .setTitle("Editar título")
            .setView(editText)
            .setPositiveButton("Guardar") { _, _ ->
                val newTitle = editText.text.toString().trim()
                if (newTitle.isNotEmpty()) {
                    lifecycleScope.launch {
                        db.assetDao().updateChatSession(session.copy(titulo = newTitle))
                        loadChatHistory()
                    }
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    inner class ChatSessionAdapter(private val sessions: MutableList<ChatSessionEntity>) : RecyclerView.Adapter<ChatSessionAdapter.SessionVH>() {
        
        inner class SessionVH(val binding: ItemChatSessionBinding) : RecyclerView.ViewHolder(binding.root)

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SessionVH {
            return SessionVH(ItemChatSessionBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        }

        override fun onBindViewHolder(holder: SessionVH, position: Int) {
            val session = sessions[position]
            holder.binding.tvSessionTitle.text = session.titulo

            // Punto 1: IA (ic_chatbot_02) vs Contacto (Foto/ic_perfil)
            if (session.is_ai) {
                holder.binding.ivChatIcon.setImageResource(R.drawable.ic_chatbot_02)
                holder.binding.ivChatIcon.imageTintList = android.content.res.ColorStateList.valueOf(Color.parseColor("#15803d"))
            } else {
                holder.binding.ivChatIcon.setImageResource(R.drawable.ic_perfil)
                holder.binding.ivChatIcon.imageTintList = android.content.res.ColorStateList.valueOf(Color.parseColor("#64748b"))
            }

            lifecycleScope.launch {
                val lastMsg = db.assetDao().getLastMessageBySession(session.id)
                holder.binding.tvLastMessage.text = lastMsg?.text ?: "Sin mensajes"
                val sdf = SimpleDateFormat("HH:mm", Locale.getDefault())
                holder.binding.tvSessionTime.text = sdf.format(Date(session.last_message_at * 1000))
            }

            holder.binding.btnEditSession.visibility = View.GONE
            holder.binding.btnDeleteSession.visibility = View.GONE
            
            holder.itemView.setOnClickListener {
                val intent = Intent(this@ChatHistoryActivity, ChatbotActivity::class.java)
                intent.putExtra("SESSION_ID", session.id)
                startActivity(intent)
            }
            
            holder.itemView.setOnLongClickListener {
                showChatMenu(session, position)
                true
            }
        }

        override fun getItemCount() = sessions.size

        fun removeAt(position: Int) {
            val session = sessions[position]
            lifecycleScope.launch {
                db.assetDao().deleteChatSession(session.id)
                sessions.removeAt(position)
                notifyItemRemoved(position)
            }
        }

        fun removeLocalAt(position: Int) {
            sessions.removeAt(position)
            notifyItemRemoved(position)
        }
    }
}
