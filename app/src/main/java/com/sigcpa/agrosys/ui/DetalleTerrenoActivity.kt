package com.sigcpa.agrosys.ui

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ActivityDetalleTerrenoBinding
import com.sigcpa.agrosys.databinding.ItemCultivoBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class DetalleTerrenoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetalleTerrenoBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentTerrenoId: Int = -1
    private var currentTerreno: TerrenoEntity? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetalleTerrenoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        currentTerrenoId = intent.getIntExtra("TERRENO_ID", -1)
        if (currentTerrenoId == -1) {
            finish()
            return
        }

        setupUI()
        
        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        androidx.core.view.WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        // SOLUCIÓN: Ajustar el menú inferior para que no lo tapen los botones del sistema
        val initialBottomPadding = binding.bottomNav.paddingBottom
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.bottomNav) { v, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, initialBottomPadding + systemBars.bottom)
            insets
        }

        // También ajustamos el Header para que respete la barra de estado superior
        val initialHeaderTopPadding = binding.headerContainer.paddingTop
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                initialHeaderTopPadding + systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            insets
        }

        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        
        binding.rvCultivosTerreno.layoutManager = LinearLayoutManager(this)
        
        binding.btnNuevoCultivo.setOnClickListener {
            val intent = Intent(this, RegisterCultivoActivity::class.java)
            intent.putExtra("TERRENO_ID", currentTerrenoId)
            startActivity(intent)
        }

        binding.btnEliminarTerreno.setOnClickListener { mostrarDialogoEliminar() }

        // Navigation
        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
        }
        binding.navCultivos.setOnClickListener {
            startActivity(Intent(this, CultivosListActivity::class.java))
            finish()
        }
    }

    private fun loadData() {
        lifecycleScope.launch {
            val terreno = db.assetDao().getTerrenoById(currentTerrenoId)
            if (terreno == null) {
                finish()
                return@launch
            }
            currentTerreno = terreno
            val cultivos = db.assetDao().getCultivosByTerreno(currentTerrenoId)

            updateGeneralUI(terreno, cultivos)
            binding.rvCultivosTerreno.adapter = CultivosAdapter(cultivos, terreno.nombre)
        }
    }

    private fun updateGeneralUI(terreno: TerrenoEntity, cultivos: List<CultivoEntity>) {
        binding.tvTerrenoNombreHeader.text = terreno.nombre
        binding.tvTerrenoNombreCard.text = terreno.nombre
        
        val totalArea = terreno.area_hectareas
        val areaOcupada = cultivos.filter { it.estado != "cosechado" && it.estado != "perdido" }.sumOf { it.area_destinada ?: 0.0 }
        val areaDisponible = totalArea - areaOcupada

        val areaStr = if (totalArea % 1.0 == 0.0) totalArea.toInt().toString() else totalArea.toString()
        val disponibleStr = String.format("%.2f", areaDisponible)
        val tipoTenencia = if (terreno.tipo_tenencia == "propio") "Propio" else "Alquilado"
        
        // Mostrar área disponible en el subheader y detalle
        binding.tvTerrenoSubheader.text = "$areaStr ha total · Disponible: $disponibleStr ha"
        binding.tvTerrenoResumenCard.text = "$disponibleStr ha disponible de $areaStr ha"
        
        binding.tvUbicacion.text = terreno.direccion_referencia ?: "No especificada"
        binding.tvAreaInfo.text = "$disponibleStr ha (Disponible)"
        binding.tvCostoAlquiler.text = if (terreno.tipo_tenencia == "alquilado") "S/ ${terreno.costo_alquiler_anual} anual" else "Terreno propio"
        binding.tvCalidadSuelo.text = terreno.calidad_suelo ?: "No especificada"
        binding.tvFuenteAgua.text = terreno.fuente_agua ?: "No especificada"
        
        binding.tvCultivosTitulo.text = "Cultivos (${cultivos.size})"
        
        // Si no hay área disponible, deshabilitar botón de nuevo cultivo
        if (areaDisponible <= 0.01) {
            binding.btnNuevoCultivo.alpha = 0.5f
            binding.btnNuevoCultivo.isEnabled = false
            Toast.makeText(this, "⚠️ Terreno sin área disponible para nuevos cultivos", Toast.LENGTH_SHORT).show()
        } else {
            binding.btnNuevoCultivo.alpha = 1.0f
            binding.btnNuevoCultivo.isEnabled = true
        }
    }

    private fun mostrarDialogoEliminar() {
        AlertDialog.Builder(this)
            .setTitle("¿Eliminar terreno?")
            .setMessage("Se perderán todos los cultivos y datos asociados de forma permanente.")
            .setPositiveButton("Eliminar") { _, _ ->
                eliminarTerreno()
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun eliminarTerreno() {
        val terreno = currentTerreno ?: return
        lifecycleScope.launch {
            db.assetDao().deleteTerreno(terreno)
            Toast.makeText(this@DetalleTerrenoActivity, "Terreno eliminado", Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    override fun onResume() {
        super.onResume()
        loadData()
    }

    inner class CultivosAdapter(private val lista: List<CultivoEntity>, private val terrenoNombre: String) : RecyclerView.Adapter<CultivosAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val b = ItemCultivoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
            return ViewHolder(b)
        }
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            holder.binding.tvNombreLote.text = item.nombre_lote ?: "Sin nombre"
            holder.binding.tvTerreno.text = terrenoNombre
            holder.binding.tvArea.text = "${item.area_destinada ?: 0.0} ha"
            
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            
            val fechaSiembraStr = sdf.format(Date(item.fecha_siembra * 1000))
            holder.binding.tvFecha.text = "S: $fechaSiembraStr"

            if (item.fecha_planificada != null) {
                val fechaPlanificadaStr = sdf.format(Date(item.fecha_planificada * 1000))
                holder.binding.tvFechaPlanificada.text = "P: $fechaPlanificadaStr"
                holder.binding.tvFechaPlanificada.visibility = android.view.View.VISIBLE
            } else {
                holder.binding.tvFechaPlanificada.visibility = android.view.View.GONE
            }
            
            holder.binding.tvEstado.text = when(item.estado) {
                "activo" -> "🌱 Activo"
                "planificado" -> "📋 Planificado"
                "cosechado" -> "🍂 Cosechado"
                "perdido" -> "⚠️ Perdido"
                else -> item.estado
            }

            holder.binding.root.setOnClickListener {
                val intent = Intent(this@DetalleTerrenoActivity, DetalleCultivoActivity::class.java)
                intent.putExtra("CULTIVO_ID", item.id)
                startActivity(intent)
            }
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemCultivoBinding) : RecyclerView.ViewHolder(binding.root)
    }
}
