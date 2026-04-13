package com.sigcpa.agrosys.ui

import android.content.Intent
import android.content.res.ColorStateList
import android.graphics.Color
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
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.databinding.ActivityDetalleCultivoBinding
import com.sigcpa.agrosys.databinding.ItemCosechaDetalleBinding
import com.sigcpa.agrosys.databinding.ItemLaborDetalleBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class DetalleCultivoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetalleCultivoBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentCultivoId: Int = -1
    private var currentCultivo: CultivoEntity? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetalleCultivoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        currentCultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        if (currentCultivoId == -1) {
            Toast.makeText(this, "Error: No se encontró el cultivo", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupUI()
        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }

        binding.btnStatusPlanificado.setOnClickListener { updateStatus("planificado") }
        binding.btnStatusActivo.setOnClickListener { updateStatus("activo") }
        binding.btnStatusCosechado.setOnClickListener { updateStatus("cosechado") }
        binding.btnStatusPerdido.setOnClickListener { updateStatus("perdido") }

        binding.btnEliminarCultivo.setOnClickListener { mostrarDialogoEliminar() }

        binding.rvLabores.layoutManager = LinearLayoutManager(this)
        binding.rvCosechas.layoutManager = LinearLayoutManager(this)
        
        binding.btnAgregarCosecha.setOnClickListener {
            val intent = Intent(this, RegisterCosechaActivity::class.java)
            intent.putExtra("CULTIVO_ID", currentCultivoId)
            startActivity(intent)
        }

        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
        }
    }

    private fun loadData() {
        lifecycleScope.launch {
            val cultivo = db.assetDao().getCultivoById(currentCultivoId)
            if (cultivo == null) {
                finish()
                return@launch
            }
            currentCultivo = cultivo
            val terreno = db.assetDao().getTerrenoById(cultivo.terreno_id)
            val labores = db.assetDao().getLaboresByCultivo(currentCultivoId)
            val cosechas = db.assetDao().getCosechasByCultivo(currentCultivoId)
            val ventas = db.assetDao().getVentasByCultivo(currentCultivoId)
            val insumos = db.assetDao().getInsumosByCultivo(currentCultivoId)

            updateGeneralUI(cultivo, terreno)
            updateStatusButtons(cultivo.estado)
            updateFinanzas(labores, insumos, cosechas, ventas)
            updateListas(labores, cosechas, ventas)
        }
    }

    private fun updateGeneralUI(cultivo: CultivoEntity, terreno: TerrenoEntity?) {
        binding.tvCultivoNombreHeader.text = "${cultivo.nombre_lote} · ${terreno?.nombre ?: "Sin terreno"}"
        binding.tvNombreLote.text = cultivo.nombre_lote
        binding.tvTerrenoNombre.text = terreno?.nombre ?: "Sin terreno"
        binding.tvArea.text = "${cultivo.area_destinada ?: 0.0} ha"
        
        binding.tvEstado.text = when(cultivo.estado) {
            "activo" -> "🌱 Activo"
            "planificado" -> "📋 Planificado"
            "cosechado" -> "🍂 Cosechado"
            "perdido" -> "⚠️ Perdido"
            else -> cultivo.estado
        }
    }

    private fun updateStatusButtons(estado: String) {
        val activeColor = ColorStateList.valueOf(Color.parseColor("#15803d"))
        val inactiveColor = ColorStateList.valueOf(Color.parseColor("#f3f4f6"))
        
        val btns = mapOf(
            "planificado" to binding.btnStatusPlanificado,
            "activo" to binding.btnStatusActivo,
            "cosechado" to binding.btnStatusCosechado,
            "perdido" to binding.btnStatusPerdido
        )

        btns.forEach { (type, btn) ->
            if (type == estado) {
                btn.backgroundTintList = activeColor
                btn.setTextColor(Color.WHITE)
                btn.strokeColor = activeColor
            } else {
                btn.backgroundTintList = inactiveColor
                btn.setTextColor(Color.parseColor("#6b7280"))
                btn.strokeColor = ColorStateList.valueOf(Color.parseColor("#e5e7eb"))
            }
        }
    }

    private fun updateFinanzas(labores: List<LaborRealizadaEntity>, insumos: List<InsumoUsadoEntity>, cosechas: List<CosechaEntity>, ventas: List<VentaEntity>) {
        val costoLabores = labores.sumOf { it.costo_mano_obra_total + it.costo_maquinaria_total }
        val costoInsumos = insumos.sumOf { it.cantidad * it.costo_unitario }
        val costoCosecha = cosechas.sumOf { it.costo_operativo_cosecha } // Nuevo: Costo del schema
        
        val costoTotal = costoLabores + costoInsumos + costoCosecha
        val ingresosTotal = ventas.sumOf { it.cantidad_vendida_kg * it.precio_por_kg }
        val gananciaNeta = ingresosTotal - costoTotal

        binding.tvCostosTotales.text = "S/ ${String.format("%.2f", costoTotal)}"
        binding.tvIngresosTotales.text = "S/ ${String.format("%.2f", ingresosTotal)}"
        binding.tvGananciaNeta.text = "S/ ${String.format("%.2f", gananciaNeta)}"
        binding.tvGanancia.text = "S/ ${String.format("%.2f", gananciaNeta)}"
        
        val color = if (gananciaNeta >= 0) Color.parseColor("#15803d") else Color.parseColor("#b91c1c")
        binding.tvGananciaNeta.setTextColor(color)
        binding.tvGanancia.setTextColor(color)
    }

    private fun updateListas(labores: List<LaborRealizadaEntity>, cosechas: List<CosechaEntity>, ventas: List<VentaEntity>) {
        binding.tvLaboresTitulo.text = "Labores (${labores.size})"
        binding.rvLabores.adapter = LaboresAdapter(labores)

        binding.tvCosechasTitulo.text = "Cosechas (${cosechas.size})"
        binding.rvCosechas.adapter = CosechasAdapter(cosechas, ventas)
    }

    private fun updateStatus(nuevoEstado: String) {
        val cultivo = currentCultivo ?: return
        lifecycleScope.launch {
            val updated = cultivo.copy(estado = nuevoEstado, updated_at = System.currentTimeMillis() / 1000)
            db.assetDao().updateCultivo(updated)
            currentCultivo = updated
            loadData()
        }
    }

    private fun mostrarDialogoEliminar() {
        AlertDialog.Builder(this)
            .setTitle("¿Eliminar cultivo?")
            .setMessage("Se perderán todos los datos asociados.")
            .setPositiveButton("Eliminar") { _, _ ->
                lifecycleScope.launch {
                    db.assetDao().deleteCultivo(currentCultivo!!)
                    finish()
                }
            }
            .setNegativeButton("Cancelar", null).show()
    }

    override fun onResume() {
        super.onResume()
        loadData()
    }

    // Adapters... (se mantienen igual pero con lógica de costos de cosecha)
    inner class LaboresAdapter(private val lista: List<LaborRealizadaEntity>) : RecyclerView.Adapter<LaboresAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(ItemLaborDetalleBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            lifecycleScope.launch {
                val cat = db.assetDao().getCatalogoLaborById(item.catalogo_labor_id)
                holder.binding.tvLaborNombre.text = "🔧 ${cat?.nombre ?: "Labor"}"
            }
            holder.binding.tvLaborFecha.text = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(item.fecha_realizacion * 1000))
            holder.binding.tvLaborCosto.text = "-S/ ${String.format("%.2f", item.costo_mano_obra_total + item.costo_maquinaria_total)}"
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemLaborDetalleBinding) : RecyclerView.ViewHolder(binding.root)
    }

    inner class CosechasAdapter(private val lista: List<CosechaEntity>, private val ventas: List<VentaEntity>) : RecyclerView.Adapter<CosechasAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(ItemCosechaDetalleBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            val totalVenta = ventas.filter { it.cosecha_id == item.id }.sumOf { it.cantidad_vendida_kg * it.precio_por_kg }
            holder.binding.tvCosechaInfo.text = "📦 ${item.cantidad_kg} kg · ${item.calidad ?: "Estándar"}"
            holder.binding.tvCosechaFecha.text = "Lote: ${item.lote_codigo}"
            holder.binding.tvCosechaIngreso.text = "+S/ ${String.format("%.2f", totalVenta)}"
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemCosechaDetalleBinding) : RecyclerView.ViewHolder(binding.root)
    }


}
