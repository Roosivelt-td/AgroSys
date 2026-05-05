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
import android.net.Uri
import com.bumptech.glide.Glide
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

        // El botón de mas opciones ahora puede usarse para otra cosa o mantenerse como decorativo/menú secundario
        binding.btnMasOpciones.setOnClickListener {
            // Podrías abrir un menú de reportes o simplemente dejarlo para info
            Toast.makeText(this, "Opciones de cultivo", Toast.LENGTH_SHORT).show()
        }

        binding.btnEditCultivo.setOnClickListener {
            currentCultivo?.let { cultivo ->
                AlertDialog.Builder(this)
                    .setTitle(getString(R.string.btn_editar))
                    .setMessage("¿Desea modificar los datos de este cultivo?")
                    .setPositiveButton(getString(R.string.btn_continuar)) { _, _ ->
                        val intent = Intent(this, RegisterCultivoActivity::class.java)
                        intent.putExtra("CULTIVO_ID", cultivo.id)
                        intent.putExtra("TERRENO_ID", cultivo.terreno_id)
                        startActivity(intent)
                    }
                    .setNegativeButton(getString(R.string.btn_cancelar), null)
                    .show()
            }
        }

        binding.btnDeleteCultivoMini.setOnClickListener { mostrarDialogoEliminar() }

        binding.btnStatusPlanificado.setOnClickListener { updateStatus("planificado") }
        binding.btnStatusActivo.setOnClickListener { updateStatus("activo") }
        binding.btnStatusCosechado.setOnClickListener { updateStatus("cosechado") }
        binding.btnStatusPerdido.setOnClickListener { updateStatus("perdido") }

        binding.rvLabores.layoutManager = LinearLayoutManager(this)
        binding.rvCosechas.layoutManager = LinearLayoutManager(this)
        
        binding.btnAgregarLabor.setOnClickListener {
            AlertDialog.Builder(this)
                .setMessage(getString(R.string.dialog_confirm_nueva_labor))
                .setPositiveButton(getString(R.string.btn_continuar)) { _, _ ->
                    val intent = Intent(this, RegisterLaborActivity::class.java)
                    intent.putExtra("CULTIVO_ID", currentCultivoId)
                    currentCultivo?.let { cultivo ->
                        intent.putExtra("TERRENO_NOMBRE", currentTerreno?.nombre ?: "")
                        intent.putExtra("CULTIVO_DETALLE", "${cultivo.nombre_lote} (${cultivo.variedad})")
                    }
                    startActivity(intent)
                }
                .setNegativeButton(getString(R.string.btn_cancelar), null)
                .show()
        }

        binding.btnAgregarCosecha.setOnClickListener {
            AlertDialog.Builder(this)
                .setMessage(getString(R.string.dialog_confirm_nueva_cosecha))
                .setPositiveButton(getString(R.string.btn_continuar)) { _, _ ->
                    checkLaboresYProceder()
                }
                .setNegativeButton(getString(R.string.btn_cancelar), null)
                .show()
        }

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
            binding.tvNombreTerrenoDetalle.text = currentTerreno?.nombre ?: ""
            binding.tvNombreLote.text = cultivo.nombre_lote
            binding.tvVariedad.text = cultivo.variedad
            binding.tvHeaderTitle.text = cultivo.nombre_lote
            binding.tvHeaderVariedad.text = cultivo.variedad
            binding.tvArea.text = "${cultivo.area_destinada ?: 0.0} ha"
            binding.tvAreaTotalTerreno.text = "${currentTerreno?.area_hectareas ?: 0.0} ha"
            val fechaMostrar = (cultivo.fecha_siembra ?: cultivo.fecha_planificada ?: 0L) * 1000
            binding.tvFechaSiembra.text = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(fechaMostrar))

            // Tenencia y Foto
            binding.tvTenenciaBadge.text = currentTerreno?.tipo_tenencia?.uppercase() ?: "PROPIO"
            
            val fotoPath = cultivo.foto_path ?: currentTerreno?.foto_path
            fotoPath?.let { path ->
                val file = java.io.File(path)
                val uri = if (file.exists()) Uri.fromFile(file) else Uri.parse(path)
                Glide.with(this@DetalleCultivoActivity)
                    .load(uri)
                    .placeholder(R.drawable.uploap)
                    .centerCrop()
                    .into(binding.ivCultivoFoto)
            }

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
        val fechaSiembra = cultivo.fecha_siembra
        if (diasEstimados > 0 && fechaSiembra != null && fechaSiembra > 0) {
            val fechaActualMs = System.currentTimeMillis()
            val fechaSiembraMs = fechaSiembra * 1000
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
        var costoAlquiler = 0.0
        var costoOtros = 0.0

        labores.forEach { labor ->
            val totalLabor = labor.costo_mano_obra_total + labor.costo_maquinaria_total + 
                             insumos.filter { it.labor_id == labor.id }.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
            when (labor.catalogo_labor_id) {
                1 -> costoPrep += totalLabor
                2 -> costoSiembra += totalLabor
                10 -> costoAlquiler += totalLabor
                8 -> costoOtros += totalLabor // ID 8 corresponde a 'Otros'
                else -> costoMant += totalLabor
            }
        }

        val costoCosecha = cosechas.sumOf { it.costo_operativo_cosecha } + ventas.sumOf { it.costo_flete + it.impuestos }
        val costoTotal = costoPrep + costoSiembra + costoMant + costoCosecha + costoAlquiler + costoOtros
        val ingresosTotal = ventas.sumOf { it.cantidad_vendida_kg * it.precio_por_kg }
        val ganancia = ingresosTotal - costoTotal

        binding.tvCostosTotales.text = "S/ ${String.format("%.2f", costoTotal)}"
        binding.tvCostosTotalesCard.text = "S/ ${String.format("%.2f", costoTotal)}" // Punto 3
        binding.tvIngresosTotales.text = "S/ ${String.format("%.2f", ingresosTotal)}"
        
        binding.rowAlquiler.apply { 
            root.visibility = if (costoAlquiler > 0) android.view.View.VISIBLE else android.view.View.GONE
            tvLabel.text = "Alquiler de Terreno"
            tvValue.text = "S/ ${String.format("%.2f", costoAlquiler)}"
        }
        binding.rowPrep.apply { tvLabel.text = "Preparación"; tvValue.text = "S/ ${String.format("%.2f", costoPrep)}" }
        binding.rowSiembra.apply { tvLabel.text = "Siembra"; tvValue.text = "S/ ${String.format("%.2f", costoSiembra)}" }
        binding.rowMant.apply { tvLabel.text = "Mantenimiento"; tvValue.text = "S/ ${String.format("%.2f", costoMant)}" }
        binding.rowCosecha.apply { tvLabel.text = "Cosecha"; tvValue.text = "S/ ${String.format("%.2f", costoCosecha)}" }

        binding.rowOtros.apply {
            root.visibility = android.view.View.VISIBLE
            tvLabel.text = "Otros"
            tvValue.text = "S/ ${String.format("%.2f", costoOtros)}"
        }

        binding.tvGanancia.text = "S/ ${String.format("%.2f", ganancia)}"
        binding.tvGanancia.setTextColor(if (ganancia >= 0) Color.parseColor("#15803d") else Color.parseColor("#ef4444"))
    }

    private fun updateListas(labores: List<LaborRealizadaEntity>, cosechas: List<CosechaEntity>, ventas: List<VentaEntity>) {
        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoLabores()
            val insumos = db.assetDao().getInsumosByCultivo(currentCultivoId)
            
            val groupedLabores = labores.groupBy { it.catalogo_labor_id }.map { (id, items) ->
                val totalCost = items.sumOf { labor ->
                    labor.costo_mano_obra_total + labor.costo_maquinaria_total +
                            insumos.filter { it.labor_id == labor.id }.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
                }
                val lastDate = items.maxOfOrNull { it.fecha_realizacion } ?: 0L
                LaborGroup(id, catalogo.find { it.id == id }?.nombre ?: "Labor", items, totalCost, lastDate)
            }
            binding.rvLabores.adapter = LaboresGroupAdapter(groupedLabores)
            binding.tvLaboresTitulo.text = "Historial de Labores (${groupedLabores.size})"
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
            val todasLasLabores = db.assetDao().getLaboresByCultivo(currentCultivoId)
            val laboresCosecha = todasLasLabores.filter { it.catalogo_labor_id == 9 }
            val cosechasExistentes = db.assetDao().getCosechasByCultivo(currentCultivoId)
            
            // Obtener IDs de labores que ya tienen un registro de producción vinculado
            val laborIdsConProduccion = cosechasExistentes.mapNotNull { it.labor_id }
            
            // Encontrar labores de cosecha que NO tienen producción registrada
            val laboresPendientes = laboresCosecha.filter { !laborIdsConProduccion.contains(it.id) }

            when {
                laboresPendientes.isNotEmpty() -> {
                    // Si hay labores de cosecha sin producción, procedemos a registrar la producción para la primera pendiente
                    val laborParaVincular = laboresPendientes.first()
                    val intent = Intent(this@DetalleCultivoActivity, RegisterCosechaActivity::class.java)
                    intent.putExtra("CULTIVO_ID", currentCultivoId)
                    intent.putExtra("LABOR_ID", laborParaVincular.id)
                    startActivity(intent)
                }
                else -> {
                    // Si no hay labores de cosecha previas o todas ya tienen producción, 
                    // primero debemos registrar el trabajo (mano de obra/insumos) de la nueva cosecha.
                    val intent = Intent(this@DetalleCultivoActivity, RegisterLaborActivity::class.java)
                    intent.putExtra("CULTIVO_ID", currentCultivoId)
                    intent.putExtra("PRE_SELECT_LABOR_ID", 9)
                    intent.putExtra("LABOR_NOMBRE", "Cosecha")
                    currentCultivo?.let { cultivo ->
                        intent.putExtra("TERRENO_NOMBRE", currentTerreno?.nombre ?: "")
                        intent.putExtra("CULTIVO_DETALLE", "${cultivo.nombre_lote} (${cultivo.variedad})")
                    }
                    startActivity(intent)
                    Toast.makeText(this@DetalleCultivoActivity, "Primero registre los gastos de la nueva cosecha", Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    private fun mostrarDialogoEliminar() {
        AlertDialog.Builder(this).setTitle("Eliminar").setMessage("¿Desea eliminar este cultivo?").setPositiveButton("Eliminar") { _, _ ->
            lifecycleScope.launch { db.assetDao().deleteCultivo(currentCultivo!!); finish() }
        }.setNegativeButton("Cancelar", null).show()
    }

    data class LaborGroup(val id: Int, val nombre: String, val items: List<LaborRealizadaEntity>, val totalCost: Double, val lastDate: Long)
    inner class LaboresGroupAdapter(private val groups: List<LaborGroup>) : RecyclerView.Adapter<LaboresGroupAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(com.sigcpa.agrosys.databinding.ItemLaborGroupBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val group = groups[position]
            holder.binding.tvNombreCultivo.text = group.nombre
            
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            val fechaStr = if (group.lastDate > 0) sdf.format(Date(group.lastDate * 1000)) else "--/--/----"
            
            holder.binding.tvFecha.text = fechaStr
            holder.binding.tvCostoTotal.text = "S/ ${String.format("%.2f", group.totalCost)}"
            holder.binding.tvResumenLabores.text = "${group.items.size} registros • Último: $fechaStr\nVer más detalles"

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
            
            val unidad = item.unidad_medida ?: "kg"
            holder.binding.tvCosechaInfo.text = "${item.cantidad_kg} $unidad · ${item.calidad ?: "Estándar"}"
            holder.binding.tvCosechaIngreso.text = "S/ ${String.format("%.2f", total)}"

            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            holder.binding.tvCosechaFecha.text = sdf.format(Date(item.fecha_cosecha * 1000))

            holder.itemView.setOnClickListener {
                if (item.labor_id != null) {
                    val intent = Intent(this@DetalleCultivoActivity, RegisterCosechaActivity::class.java)
                    intent.putExtra("CULTIVO_ID", currentCultivoId)
                    intent.putExtra("LABOR_ID", item.labor_id)
                    startActivity(intent)
                } else {
                    // Si por alguna razón no tiene labor_id, permitimos editar normalmente
                    val intent = Intent(this@DetalleCultivoActivity, RegisterCosechaActivity::class.java)
                    intent.putExtra("CULTIVO_ID", currentCultivoId)
                    intent.putExtra("COSECHA_ID", item.id) // Necesitaría manejar COSECHA_ID en RegisterCosechaActivity
                    startActivity(intent)
                }
            }
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemCosechaDetalleBinding) : RecyclerView.ViewHolder(binding.root)
    }

    override fun onResume() { super.onResume(); loadData() }
}
