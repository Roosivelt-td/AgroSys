package com.sigcpa.agrosys.ui

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.NotificacionEntity
import com.sigcpa.agrosys.databinding.ActivityNotificacionesBinding
import com.sigcpa.agrosys.databinding.ItemNotificacionBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class NotificacionesActivity : AppCompatActivity() {
    private lateinit var binding: ActivityNotificacionesBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var userId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityNotificacionesBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        userId = sharedPref.getInt("USER_ID", -1)

        binding.btnBack.setOnClickListener { finish() }
        setupRecyclerView()
    }

    private fun setupRecyclerView() {
        binding.rvNotificaciones.layoutManager = LinearLayoutManager(this)
        loadNotificaciones()
    }

    private fun loadNotificaciones() {
        lifecycleScope.launch {
            val notifs = db.assetDao().getNotificacionesByUsuario(userId)
            if (notifs.isEmpty()) {
                binding.emptyState.visibility = View.VISIBLE
                binding.rvNotificaciones.visibility = View.GONE
            } else {
                binding.emptyState.visibility = View.GONE
                binding.rvNotificaciones.visibility = View.VISIBLE
                binding.rvNotificaciones.adapter = NotificacionesAdapter(notifs) { notif ->
                    handleNotifClick(notif)
                }
            }
        }
    }

    private fun handleNotifClick(notif: NotificacionEntity) {
        lifecycleScope.launch {
            db.assetDao().updateNotificacion(notif.copy(leido = 1))
            
            val message = notif.mensaje.lowercase()
            val title = notif.titulo.lowercase()
            
            when {
                message.contains("parcelas") || message.contains("terrenos") -> {
                    startActivity(Intent(this@NotificacionesActivity, RegisterTerrenoActivity::class.java))
                }
                message.contains("cultivo") && (message.contains("registrar") || message.contains("sugerimos")) -> {
                    startActivity(Intent(this@NotificacionesActivity, RegisterCultivoActivity::class.java))
                }
                message.contains("siembra") -> {
                    startActivity(Intent(this@NotificacionesActivity, CultivosListActivity::class.java))
                }
                message.contains("labores") -> {
                    startActivity(Intent(this@NotificacionesActivity, LaboresListActivity::class.java))
                }
                title.contains("clima") || title.contains("lluvia") || title.contains("riego") -> {
                    Toast.makeText(this@NotificacionesActivity, "Sugerencia: " + notif.mensaje, Toast.LENGTH_LONG).show()
                }
                else -> {
                    Toast.makeText(this@NotificacionesActivity, notif.mensaje, Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    inner class NotificacionesAdapter(
        private val list: List<NotificacionEntity>,
        private val onClick: (NotificacionEntity) -> Unit
    ) : RecyclerView.Adapter<NotificacionesAdapter.ViewHolder>() {

        inner class ViewHolder(val binding: ItemNotificacionBinding) : RecyclerView.ViewHolder(binding.root)

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            return ViewHolder(ItemNotificacionBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        }

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = list[position]
            holder.binding.tvNotifTitle.text = item.titulo
            holder.binding.tvNotifMessage.text = item.mensaje
            
            val sdf = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
            holder.binding.tvNotifTime.text = sdf.format(Date(item.created_at * 1000))
            
            holder.binding.unreadIndicator.visibility = if (item.leido == 1) View.GONE else View.VISIBLE
            
            holder.binding.root.setOnClickListener { onClick(item) }
        }

        override fun getItemCount() = list.size
    }
}