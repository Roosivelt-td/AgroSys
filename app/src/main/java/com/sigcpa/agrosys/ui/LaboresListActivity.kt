package com.sigcpa.agrosys.ui

import android.content.Intent
import android.content.res.ColorStateList
import android.graphics.Color
import android.os.Bundle
import android.transition.AutoTransition
import android.transition.TransitionManager
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.LaborRealizadaEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ActivityLaboresListBinding
import com.sigcpa.agrosys.databinding.ItemLaborGroupBinding
import com.sigcpa.agrosys.databinding.ItemLaborMiniBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class LaboresListActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLaboresListBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var hasCultivos = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLaboresListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupRecyclerView()
        setupListeners()
        loadLabores()
    }

    private fun setupRecyclerView() {
        binding.rvLabores.layoutManager = LinearLayoutManager(this)
    }

    private fun setupListeners() {
        val goToRegister = {
            if (hasCultivos) {
                startActivity(Intent(this, RegisterLaborActivity::class.java))
            } else {
                Toast.makeText(this, "⚠️ Registra un cultivo primero", Toast.LENGTH_SHORT).show()
            }
        }

        binding.btnNuevaLabor.setOnClickListener { goToRegister() }
        binding.btnEmptyAdd.setOnClickListener { goToRegister() }

        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
            finish()
        }
        
        binding.navTerrenos.setOnClickListener {
            startActivity(Intent(this, TerrenosListActivity::class.java))
            finish()
        }
        
        binding.navCultivos.setOnClickListener {
            startActivity(Intent(this, CultivosListActivity::class.java))
            finish()
        }
    }

    private fun loadLabores() {
        val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val agricultor = db.userDao().getAgricultorByUserId(userId)
            if (agricultor != null) {
                val cultivos = db.assetDao().getCultivosByAgricultor(agricultor.id)
                hasCultivos = cultivos.isNotEmpty()
                
                // Aplicar bloqueos visuales
                binding.btnNuevaLabor.alpha = if (hasCultivos) 1.0f else 0.4f
                binding.navReportes.alpha = if (hasCultivos) 1.0f else 0.4f

                val labores = db.assetDao().getLaboresByAgricultor(agricultor.id)
                
                // AGRUPACIÓN POR CULTIVO
                val laboresAgrupadas = labores.groupBy { it.cultivo_id }
                
                // Mapear a una lista de pares (Cultivo, Lista de Labores)
                val data = laboresAgrupadas.mapNotNull { (cultivoId, listaLabores) ->
                    val cultivo = cultivos.find { it.id == cultivoId }
                    if (cultivo != null) cultivo to listaLabores else null
                }

                updateUI(data)
            } else {
                updateUI(emptyList())
            }
        }
    }

    private fun updateUI(data: List<Pair<CultivoEntity, List<LaborRealizadaEntity>>>) {
        if (data.isEmpty()) {
            binding.emptyState.visibility = View.VISIBLE
            binding.rvLabores.visibility = View.GONE
        } else {
            binding.emptyState.visibility = View.GONE
            binding.rvLabores.visibility = View.VISIBLE
            binding.rvLabores.adapter = LaboresGroupAdapter(data)
        }
    }

    override fun onResume() {
        super.onResume()
        loadLabores()
    }

    inner class LaboresGroupAdapter(private val lista: List<Pair<CultivoEntity, List<LaborRealizadaEntity>>>) 
        : RecyclerView.Adapter<LaboresGroupAdapter.ViewHolder>() {

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = 
            ViewHolder(ItemLaborGroupBinding.inflate(LayoutInflater.from(parent.context), parent, false))

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val (cultivo, labores) = lista[position]
            
            holder.binding.tvNombreCultivo.text = cultivo.nombre_lote ?: "Cultivo #${cultivo.id}"
            holder.binding.tvResumenLabores.text = "${labores.size} labores realizadas"

            // Configurar contenedor expandible
            holder.binding.layoutHeader.setOnClickListener {
                val isVisible = holder.binding.layoutExpandible.visibility == View.VISIBLE
                
                TransitionManager.beginDelayedTransition(holder.binding.root as ViewGroup, AutoTransition())
                holder.binding.layoutExpandible.visibility = if (isVisible) View.GONE else View.VISIBLE
                holder.binding.ivExpand.rotation = if (isVisible) 0f else 180f
            }

            // Cargar las primeras 3 labores
            holder.binding.containerLaboresMini.removeAllViews()
            labores.take(3).forEach { labor ->
                addMiniLaborView(holder.binding.containerLaboresMini, labor)
            }

            holder.binding.btnVerDetalles.setOnClickListener {
                // Ir a la actividad de detalle de labores de este cultivo
                val intent = Intent(this@LaboresListActivity, DetalleCultivoActivity::class.java)
                intent.putExtra("CULTIVO_ID", cultivo.id)
                startActivity(intent)
            }
        }

        private fun addMiniLaborView(container: LinearLayout, labor: LaborRealizadaEntity) {
            val miniBinding = ItemLaborMiniBinding.inflate(LayoutInflater.from(container.context), container, false)
            
            lifecycleScope.launch {
                val cat = db.assetDao().getCatalogoLaborById(labor.catalogo_labor_id)
                miniBinding.tvMiniNombre.text = cat?.nombre ?: "Labor"
                
                val icono = when {
                    cat?.nombre?.contains("Preparación", true) == true -> "🚜"
                    cat?.nombre?.contains("Siembra", true) == true -> "🌱"
                    cat?.nombre?.contains("Riego", true) == true -> "💧"
                    cat?.nombre?.contains("Cosecha", true) == true -> "🍂"
                    else -> "🔧"
                }
                miniBinding.tvMiniIcono.text = icono
            }

            val sdf = SimpleDateFormat("dd/MM", Locale.getDefault())
            miniBinding.tvMiniFecha.text = sdf.format(Date(labor.fecha_realizacion * 1000))
            miniBinding.tvMiniCosto.text = "S/ ${String.format("%.2f", labor.costo_mano_obra_total + labor.costo_maquinaria_total)}"
            
            container.addView(miniBinding.root)
        }

        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemLaborGroupBinding) : RecyclerView.ViewHolder(binding.root)
    }
}
