package com.sigcpa.agrosys.ui

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.ChatSessionEntity
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import com.sigcpa.agrosys.databinding.ActivityContactsBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class ContactsActivity : AppCompatActivity() {
    private lateinit var binding: ActivityContactsBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var userId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityContactsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        userId = sharedPref.getInt("USER_ID", -1)

        binding.btnBack.setOnClickListener { finish() }

        binding.btnChatIA.setOnClickListener {
            startNewChatWithIA()
        }
        
        loadContacts()
    }

    private fun loadContacts() {
        lifecycleScope.launch {
            val contacts = db.userDao().getAllOtherUsers(userId)
            binding.tvContactCount.text = "${contacts.size} contactos"
            
            binding.rvContacts.layoutManager = LinearLayoutManager(this@ContactsActivity)
            binding.rvContacts.adapter = object : RecyclerView.Adapter<ContactVH>() {
                override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ContactVH {
                    val view = LayoutInflater.from(parent.context).inflate(android.R.layout.simple_list_item_2, parent, false)
                    return ContactVH(view)
                }

                override fun onBindViewHolder(holder: ContactVH, position: Int) {
                    val contact = contacts[position]
                    val text1 = holder.itemView.findViewById<android.widget.TextView>(android.R.id.text1)
                    val text2 = holder.itemView.findViewById<android.widget.TextView>(android.R.id.text2)
                    
                    text1.text = "${contact.nombre} ${contact.apellidos}"
                    text2.text = "Disponible"
                    
                    holder.itemView.setOnClickListener {
                        startNewChatWithContact(contact)
                    }
                }

                override fun getItemCount() = contacts.size
            }
        }
    }

    private fun startNewChatWithIA() {
        lifecycleScope.launch {
            val newSession = ChatSessionEntity(
                usuario_id = userId,
                titulo = "Consulta IA " + SimpleDateFormat("dd/MM HH:mm", Locale.getDefault()).format(Date()),
                is_ai = true
            )
            val sessionId = db.assetDao().insertChatSession(newSession)
            
            val intent = Intent(this@ContactsActivity, ChatbotActivity::class.java)
            intent.putExtra("SESSION_ID", sessionId.toInt())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
            finish()
        }
    }

    private fun startNewChatWithContact(contact: UsuarioEntity) {
        lifecycleScope.launch {
            val newSession = ChatSessionEntity(
                usuario_id = userId,
                titulo = "${contact.nombre} ${contact.apellidos}",
                is_ai = false,
                contact_id = contact.id
            )
            val sessionId = db.assetDao().insertChatSession(newSession)
            
            val intent = Intent(this@ContactsActivity, ChatbotActivity::class.java)
            intent.putExtra("SESSION_ID", sessionId.toInt())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
            finish()
        }
    }

    inner class ContactVH(view: android.view.View) : RecyclerView.ViewHolder(view)
}
