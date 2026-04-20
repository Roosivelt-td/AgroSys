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
import com.sigcpa.agrosys.databinding.ItemLaborGroupBinding
import com.sigcpa.agrosys.databinding.ItemLaborMiniBinding
import com.sigcpa.agrosys.databinding.ItemInsumoRowBinding
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
        
        // Hacer la barra de estado verde con iconos blancos
        window.statusBarColor = Color.parseColor("#2d6a4f")
        window.decorView.systemUiVisibility = 0 
        
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
        
        binding.btnAgregarLabor.setOnClickListener {
            val intent = Intent(this, RegisterLaborActivity::class.java)
            intent.putExtra("CULTIVO_ID", currentCultivoId)
            startActivity(intent)
        }

        binding.btnAgregarCosecha.setOnClickListener {
            checkLaboresYProceder()
        }

        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
        }
        
        binding.navTerrenos.setOnClickListener {
            val intent = Intent(this, TerrenosListActivity::class.java)
            startActivity(intent)
        }
        
        binding.navLabores.setOnClickListener {
            val intent = Intent(this, LaboresListActivity::class.java)
            startActivity(intent)
        }
        
        binding.navReportes.setOnClickListener {
            // Podría ir a una actividad de reportes específica si existe, 
            // por ahora usamos LaboresListActivity como resumen
            val intent = Intent(this, LaboresListActivity::class.java)
            startActivity(intent)
        }
    }

    private fun checkLaboresYProceder() {
        lifecycleScope.launch {
            val cultivo = currentCultivo ?: return@launch
            val labores = db.assetDao().getLaboresByCultivo(currentCultivoId)
            val idsLabores = labores.map { it.catalogo_labor_id }
            
            // 1. Validar Labores Indispensables (Preparación = 1, Siembra = 2)
            val tienePreparacion = idsLabores.contains(1)
            val tieneSiembra = idsLabores.contains(2)

            if (!tienePreparacion || !tieneSiembra) {
                AlertDialog.Builder(this@DetalleCultivoActivity)
                    .setTitle("Acción Requerida")
                    .setMessage("Para registrar una cosecha, primero debe haber registrado la Preparación del terreno y la Siembra.")
                    .setPositiveButton("Entendido", null)
                    .show()
                return@launch
            }

            // 2. Validar Tiempo de Cosecha (Basado en fecha_siembra real)
            val catCultivo = db.assetDao().getCatalogoCultivoById(cultivo.catalogo_cultivo_id)
            val diasEstimados = catCultivo?.dias_a_cosecha_promedio ?: 0
            
            if (diasEstimados > 0) {
                val fechaActualMs = System.currentTimeMillis()
                val fechaSiembraMs = cultivo.fecha_siembra * 1000
                val diasTranscurridos = ((fechaActualMs - fechaSiembraMs) / (1000 * 60 * 60 * 24)).toInt()
                
                val limiteHabilitacion = (diasEstimados * 0.75).toInt()

                if (diasTranscurridos < limiteHabilitacion) {
                    val diasFaltantes = diasEstimados - diasTranscurridos
                    AlertDialog.Builder(this@DetalleCultivoActivity)
                        .setTitle("Advertencia de Cosecha")
                        .setMessage("Según el ciclo estimado (${diasEstimados} días), aún faltan aproximadamente ${diasFaltantes} días para la cosecha ideal.\n\n¿Está seguro que desea registrar la cosecha ahora?")
                        .setPositiveButton("Sí, Continuar") { _, _ ->
                            procederARegistroCosecha()
                        }
                        .setNegativeButton("Cancelar", null)
                        .show()
                } else {
                    procederARegistroCosecha()
                }
            } else {
                // Si no hay días estimados en el catálogo, permitimos el flujo
                procederARegistroCosecha()
            }
        }
    }

    private fun procederARegistroCosecha() {
        val intent = Intent(this, RegisterCosechaActivity::class.java)
        intent.putExtra("CULTIVO_ID", currentCultivoId)
        startActivity(intent)
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
        lifecycleScope.launch {
            val catalogoLabores = db.assetDao().getCatalogoLabores()
            val mapCategorias = catalogoLabores.associate { it.id to (it.nombre.lowercase()) }

            var costoPrep = 0.0
            var costoSiembra = 0.0
            var costoMant = 0.0

            labores.forEach { labor ->
                val costoLabor = labor.costo_mano_obra_total + labor.costo_maquinaria_total
                val costoInsumosLabor = insumos.filter { it.labor_id == labor.id }.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
                val totalLabor = costoLabor + costoInsumosLabor
                
                val nombre = mapCategorias[labor.catalogo_labor_id] ?: ""
                when {
                    nombre.contains("preparación") || nombre.contains("suelo") -> costoPrep += totalLabor
                    nombre.contains("siembra") -> costoSiembra += totalLabor
                    else -> costoMant += totalLabor
                }
            }

            val costoCosecha = cosechas.sumOf { it.costo_operativo_cosecha } + ventas.sumOf { it.costo_flete + it.impuestos }
            val costoTotal = costoPrep + costoSiembra + costoMant + costoCosecha
            val ingresosTotal = ventas.sumOf { it.cantidad_vendida_kg * it.precio_por_kg }
            val gananciaNeta = ingresosTotal - costoTotal

            binding.tvCostosTotales.text = "S/ ${String.format("%.2f", costoTotal)}"
            binding.tvIngresosTotales.text = "S/ ${String.format("%.2f", ingresosTotal)}"
            binding.tvCostoPreparacion.text = "S/ ${String.format("%.2f", costoPrep)}"
            binding.tvCostoSiembra.text = "S/ ${String.format("%.2f", costoSiembra)}"
            binding.tvCostoMantenimiento.text = "S/ ${String.format("%.2f", costoMant)}"
            binding.tvCostoCosecha.text = "S/ ${String.format("%.2f", costoCosecha)}"
            
            val prefix = if (gananciaNeta >= 0) "+" else ""
            binding.tvGananciaNeta.text = "$prefix S/ ${String.format("%.2f", gananciaNeta)}"
            binding.tvGanancia.text = "$prefix S/ ${String.format("%.2f", gananciaNeta)}"
            
            val color = if (gananciaNeta >= 0) Color.parseColor("#22c55e") else Color.parseColor("#ef4444")
            binding.tvGananciaNeta.setTextColor(color)
            binding.tvGanancia.setTextColor(color)
        }
    }

    private fun updateListas(labores: List<LaborRealizadaEntity>, cosechas: List<CosechaEntity>, ventas: List<VentaEntity>) {
        binding.tvLaboresTitulo.text = "Labores (${labores.size})"
        
        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoLabores()
            val groupedLabores = labores.groupBy { it.catalogo_labor_id }
                .map { (catId, items) -> 
                    val catLabor = catalogo.find { it.id == catId }
                    LaborGroup(catId, catLabor?.nombre ?: "Labor", items)
                }
            binding.rvLabores.adapter = LaboresGroupAdapter(groupedLabores)
        }

        binding.tvCosechasTitulo.text = "Cosechas (${cosechas.size})"
        binding.rvCosechas.adapter = CosechasAdapter(cosechas, ventas)
    }

    data class LaborGroup(val id: Int, val nombre: String, val items: List<LaborRealizadaEntity>)

    inner class LaboresGroupAdapter(private val groups: List<LaborGroup>) : RecyclerView.Adapter<LaboresGroupAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(ItemLaborGroupBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val group = groups[position]
            holder.binding.tvNombreCultivo.text = "${group.nombre} (${group.items.size})"

            lifecycleScope.launch {
                var totalGastoGroup = 0.0
                group.items.forEach { labor ->
                    val insumos = db.assetDao().getInsumosByLaborId(labor.id)
                    totalGastoGroup += labor.costo_mano_obra_total + labor.costo_maquinaria_total + 
                                     insumos.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
                }
                holder.binding.tvResumenLabores.text = "Gasto acumulado: S/ ${String.format("%.2f", totalGastoGroup)}"
            }
            
            holder.binding.layoutExpandible.visibility = android.view.View.GONE
            holder.binding.ivExpand.rotation = 0f

            holder.binding.layoutHeader.setOnClickListener {
                val isExpanded = holder.binding.layoutExpandible.visibility == android.view.View.VISIBLE
                holder.binding.layoutExpandible.visibility = if (isExpanded) android.view.View.GONE else android.view.View.VISIBLE
                holder.binding.ivExpand.animate().rotation(if (isExpanded) 0f else 180f).start()
                
                if (!isExpanded && holder.binding.containerLaboresMini.childCount == 0) {
                    group.items.sortedByDescending { it.fecha_realizacion }.forEach { labor ->
                        val miniBinding = ItemLaborMiniBinding.inflate(LayoutInflater.from(holder.itemView.context), holder.binding.containerLaboresMini, false)
                        miniBinding.tvMiniNombre.text = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(labor.fecha_realizacion * 1000))
                        
                        lifecycleScope.launch {
                            val costoInsumos = db.assetDao().getCostoInsumosByLabor(labor.id) ?: 0.0
                            val subtotal = labor.costo_mano_obra_total + labor.costo_maquinaria_total + costoInsumos
                            miniBinding.tvMiniCosto.text = "S/ ${String.format("%.2f", subtotal)}"
                        }
                        
                        miniBinding.tvMiniFecha.text = if (labor.observaciones.isNullOrBlank()) "Sin observaciones" else labor.observaciones
                        holder.binding.containerLaboresMini.addView(miniBinding.root)
                    }
                }
            }

            holder.binding.btnVerDetalles.setOnClickListener {
                val intent = Intent(this@DetalleCultivoActivity, LaborTypeDetailActivity::class.java)
                intent.putExtra("CULTIVO_ID", currentCultivoId)
                intent.putExtra("LABOR_ID", group.id)
                intent.putExtra("LABOR_NOMBRE", group.nombre)
                startActivity(intent)
            }
        }
        override fun getItemCount() = groups.size
        inner class ViewHolder(val binding: ItemLaborGroupBinding) : RecyclerView.ViewHolder(binding.root)
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
