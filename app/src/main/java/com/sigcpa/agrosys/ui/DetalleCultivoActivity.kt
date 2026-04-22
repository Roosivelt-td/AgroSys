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
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.databinding.ActivityDetalleCultivoBinding
import com.sigcpa.agrosys.databinding.ItemCosechaDetalleBinding
import com.sigcpa.agrosys.databinding.ItemLaborGroupBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class DetalleCultivoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetalleCultivoBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentCultivoId: Int = -1
    private var currentCultivo: CultivoEntity? = null
    private var currentTerreno: TerrenoEntity? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        window.statusBarColor = Color.parseColor("#15803D")
        androidx.core.view.WindowInsetsControllerCompat(window, window.decorView).apply {
            isAppearanceLightStatusBars = false
            window.navigationBarColor = Color.WHITE
            isAppearanceLightNavigationBars = true
        }

        binding = ActivityDetalleCultivoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(binding.headerContainer.paddingLeft, systemBars.top, binding.headerContainer.paddingRight, binding.headerContainer.paddingBottom)
            
            // Ajuste para el menú inferior
            binding.bottomNav.setPadding(binding.bottomNav.paddingLeft, binding.bottomNav.paddingTop, binding.bottomNav.paddingRight, systemBars.bottom)

            insets
        }

        currentCultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        if (currentCultivoId == -1) {
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
            currentCultivo?.let { cultivo ->
                intent.putExtra("TERRENO_NOMBRE", currentTerreno?.nombre ?: "")
                intent.putExtra("CULTIVO_DETALLE", "${cultivo.nombre_lote} (${cultivo.variedad})")
            }
            startActivity(intent)
        }

        binding.btnAgregarCosecha.setOnClickListener { checkLaboresYProceder() }

        binding.navHome.setOnClickListener { startActivity(Intent(this, DashboardActivity::class.java).apply { flags = Intent.FLAG_ACTIVITY_CLEAR_TOP }) }
        binding.navTerrenos.setOnClickListener { startActivity(Intent(this, TerrenosListActivity::class.java)) }
        binding.navLabores.setOnClickListener { startActivity(Intent(this, LaboresListActivity::class.java)) }
        binding.navReportes.setOnClickListener { startActivity(Intent(this, LaboresListActivity::class.java)) }
    }

    private fun loadData() {
        lifecycleScope.launch {
            val cultivo = db.assetDao().getCultivoById(currentCultivoId) ?: return@launch
            currentCultivo = cultivo
            currentTerreno = db.assetDao().getTerrenoById(cultivo.terreno_id)
            val catCultivo = db.assetDao().getCatalogoCultivoById(cultivo.catalogo_cultivo_id)
            val labores = db.assetDao().getLaboresByCultivo(currentCultivoId)
            val insumos = db.assetDao().getInsumosByCultivo(currentCultivoId)
            val cosechas = db.assetDao().getCosechasByCultivo(currentCultivoId)
            val ventas = db.assetDao().getVentasByCultivo(currentCultivoId)

            // Puntos 5 y 6: Nombre y Variedad
            binding.tvNombreLote.text = cultivo.nombre_lote
            binding.tvVariedad.text = cultivo.variedad
            binding.tvHeaderTitle.text = cultivo.nombre_lote
            binding.tvHeaderVariedad.text = cultivo.variedad
            binding.tvArea.text = "${cultivo.area_destinada ?: 0.0} ha"
            binding.tvAreaTotalTerreno.text = "${currentTerreno?.area_hectareas ?: 0.0} ha"
            binding.tvFechaSiembra.text = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(cultivo.fecha_siembra * 1000))

            // Punto 2: Total Labores
            binding.tvCantLabores.text = labores.size.toString()

            // Punto 1: Estado Badge
            updateStatusUI(cultivo.estado)

            // Punto 7: Progreso (Día actual vs Ciclo estimado)
            updateProgress(cultivo, catCultivo)

            // Punto 3: Inversión (Suma de labores e insumos)
            updateFinanzas(labores, insumos, cosechas, ventas)
            
            updateListas(labores, cosechas, ventas)
        }
    }

    private fun updateProgress(cultivo: CultivoEntity, catCultivo: CatalogoCultivoEntity?) {
        val diasEstimados = catCultivo?.dias_a_cosecha_promedio ?: 0
        if (diasEstimados > 0 && cultivo.fecha_siembra > 0) {
            val fechaActualMs = System.currentTimeMillis()
            val fechaSiembraMs = cultivo.fecha_siembra * 1000
            val diasTranscurridos = ((fechaActualMs - fechaSiembraMs) / (1000 * 60 * 60 * 24)).toInt().coerceAtLeast(0)
            
            binding.tvDiaActual.text = getString(R.string.label_dia_count, diasTranscurridos)
            
            val progreso = (diasTranscurridos * 100 / diasEstimados).coerceIn(0, 100)
            binding.progressCampania.progress = progreso
            
            val diasRestantes = (diasEstimados - diasTranscurridos).coerceAtLeast(0)
            binding.tvDiasRestantes.text = getString(R.string.label_dias_restantes, diasRestantes)
        } else {
            binding.tvDiaActual.text = getString(R.string.label_dia_count, 0)
            binding.tvDiasRestantes.text = getString(R.string.label_dias_restantes, 0)
            binding.progressCampania.progress = 0
        }
    }

    private fun updateStatusUI(estado: String) {
        binding.tvEstadoBadge.text = estado
        val color = when(estado) {
            "activo" -> "#dcfce7" to "#15803d"
            "planificado" -> "#fef9c3" to "#a16207"
            "cosechado" -> "#dbeafe" to "#1d4ed8"
            "perdido" -> "#fee2e2" to "#b91c1c"
            else -> "#f3f4f6" to "#6b7280"
        }
        binding.tvEstadoBadge.backgroundTintList = ColorStateList.valueOf(Color.parseColor(color.first))
        binding.tvEstadoBadge.setTextColor(Color.parseColor(color.second))
        
        updateStatusButtons(estado)
    }

    private fun updateStatusButtons(estado: String) {
        val btns = mapOf("planificado" to binding.btnStatusPlanificado, "activo" to binding.btnStatusActivo, "cosechado" to binding.btnStatusCosechado, "perdido" to binding.btnStatusPerdido)
        btns.forEach { (type, btn) ->
            if (type == estado) {
                btn.backgroundTintList = ColorStateList.valueOf(Color.parseColor("#15803d"))
                btn.setTextColor(Color.WHITE)
            } else {
                btn.backgroundTintList = ColorStateList.valueOf(Color.parseColor("#f3f4f6"))
                btn.setTextColor(Color.parseColor("#6b7280"))
            }
        }
    }

    private fun updateFinanzas(labores: List<LaborRealizadaEntity>, insumos: List<InsumoUsadoEntity>, cosechas: List<CosechaEntity>, ventas: List<VentaEntity>) {
        var costoPrep = 0.0
        var costoSiembra = 0.0
        var costoMant = 0.0

        labores.forEach { labor ->
            val totalLabor = labor.costo_mano_obra_total + labor.costo_maquinaria_total + 
                             insumos.filter { it.labor_id == labor.id }.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
            when (labor.catalogo_labor_id) {
                1 -> costoPrep += totalLabor
                2 -> costoSiembra += totalLabor
                else -> costoMant += totalLabor
            }
        }

        val costoCosecha = cosechas.sumOf { it.costo_operativo_cosecha } + ventas.sumOf { it.costo_flete + it.impuestos }
        val costoTotal = costoPrep + costoSiembra + costoMant + costoCosecha
        val ingresosTotal = ventas.sumOf { it.cantidad_vendida_kg * it.precio_por_kg }
        val ganancia = ingresosTotal - costoTotal

        binding.tvCostosTotales.text = "S/ ${String.format("%.2f", costoTotal)}"
        binding.tvCostosTotalesCard.text = "S/ ${String.format("%.2f", costoTotal)}" // Punto 3
        binding.tvIngresosTotales.text = "S/ ${String.format("%.2f", ingresosTotal)}"
        
        binding.rowPrep.apply { tvLabel.text = "Preparación"; tvValue.text = "S/ ${String.format("%.2f", costoPrep)}" }
        binding.rowSiembra.apply { tvLabel.text = "Siembra"; tvValue.text = "S/ ${String.format("%.2f", costoSiembra)}" }
        binding.rowMant.apply { tvLabel.text = "Mantenimiento"; tvValue.text = "S/ ${String.format("%.2f", costoMant)}" }
        binding.rowCosecha.apply { tvLabel.text = "Cosecha y Ventas"; tvValue.text = "S/ ${String.format("%.2f", costoCosecha)}" }

        binding.tvGanancia.text = "S/ ${String.format("%.2f", ganancia)}"
        binding.tvGanancia.setTextColor(if (ganancia >= 0) Color.parseColor("#15803d") else Color.parseColor("#ef4444"))
    }

    private fun updateListas(labores: List<LaborRealizadaEntity>, cosechas: List<CosechaEntity>, ventas: List<VentaEntity>) {
        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoLabores()
            val groupedLabores = labores.groupBy { it.catalogo_labor_id }.map { (id, items) -> LaborGroup(id, catalogo.find { it.id == id }?.nombre ?: "Labor", items) }
            binding.rvLabores.adapter = LaboresGroupAdapter(groupedLabores)
            binding.tvLaboresTitulo.text = "Labores Realizadas (${labores.size})"
        }
        binding.tvCosechasTitulo.text = "Cosechas (${cosechas.size})"
        binding.rvCosechas.adapter = CosechasAdapter(cosechas, ventas)
    }

    // --- Otros métodos auxiliares (Eliminar, CheckCosecha, Adapters) permanecen igual ---
    private fun updateStatus(nuevoEstado: String) {
        lifecycleScope.launch {
            currentCultivo?.let {
                val updated = it.copy(estado = nuevoEstado, updated_at = System.currentTimeMillis() / 1000)
                db.assetDao().updateCultivo(updated)
                loadData()
            }
        }
    }

    private fun checkLaboresYProceder() {
        lifecycleScope.launch {
            val labores = db.assetDao().getLaboresByCultivo(currentCultivoId).map { it.catalogo_labor_id }
            if (!labores.contains(1) || !labores.contains(2)) {
                AlertDialog.Builder(this@DetalleCultivoActivity).setTitle("Atención").setMessage("Debe registrar Preparación y Siembra antes de cosechar.").setPositiveButton("Ok", null).show()
            } else {
                startActivity(Intent(this@DetalleCultivoActivity, RegisterCosechaActivity::class.java).putExtra("CULTIVO_ID", currentCultivoId))
            }
        }
    }

    private fun mostrarDialogoEliminar() {
        AlertDialog.Builder(this).setTitle("Eliminar").setMessage("¿Desea eliminar este cultivo?").setPositiveButton("Eliminar") { _, _ ->
            lifecycleScope.launch { db.assetDao().deleteCultivo(currentCultivo!!); finish() }
        }.setNegativeButton("Cancelar", null).show()
    }

    data class LaborGroup(val id: Int, val nombre: String, val items: List<LaborRealizadaEntity>)
    inner class LaboresGroupAdapter(private val groups: List<LaborGroup>) : RecyclerView.Adapter<LaboresGroupAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(com.sigcpa.agrosys.databinding.ItemLaborGroupBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val group = groups[position]
            holder.binding.tvNombreCultivo.text = group.nombre
            holder.binding.tvResumenLabores.text = "${group.items.size} registros"
            holder.binding.root.setOnClickListener {
                startActivity(Intent(this@DetalleCultivoActivity, LaborTypeDetailActivity::class.java).apply {
                    putExtra("CULTIVO_ID", currentCultivoId); putExtra("LABOR_ID", group.id); putExtra("LABOR_NOMBRE", group.nombre)
                })
            }
        }
        override fun getItemCount() = groups.size
        inner class ViewHolder(val binding: com.sigcpa.agrosys.databinding.ItemLaborGroupBinding) : RecyclerView.ViewHolder(binding.root)
    }

    inner class CosechasAdapter(private val lista: List<CosechaEntity>, private val ventas: List<VentaEntity>) : RecyclerView.Adapter<CosechasAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(ItemCosechaDetalleBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            val total = ventas.filter { it.cosecha_id == item.id }.sumOf { it.cantidad_vendida_kg * it.precio_por_kg }
            holder.binding.tvCosechaInfo.text = "${item.cantidad_kg} kg · ${item.calidad ?: "Estándar"}"
            holder.binding.tvCosechaIngreso.text = "S/ ${String.format("%.2f", total)}"
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemCosechaDetalleBinding) : RecyclerView.ViewHolder(binding.root)
    }

    override fun onResume() { super.onResume(); loadData() }
}
