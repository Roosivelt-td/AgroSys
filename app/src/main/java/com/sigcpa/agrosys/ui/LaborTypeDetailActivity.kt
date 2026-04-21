package com.sigcpa.agrosys.ui

import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.LaborRealizadaEntity
import com.sigcpa.agrosys.databinding.ActivityLaborTypeDetailBinding
import com.sigcpa.agrosys.databinding.ItemLaborDetalleBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class LaborTypeDetailActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLaborTypeDetailBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var cultivoId: Int = -1
    private var laborId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLaborTypeDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Branding e Insets
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        val initialHeaderPaddingTop = binding.headerContainer.paddingTop
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                initialHeaderPaddingTop + systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            binding.rvLabores.setPadding(
                binding.rvLabores.paddingLeft,
                binding.rvLabores.paddingTop,
                binding.rvLabores.paddingRight,
                systemBars.bottom + (16 * resources.displayMetrics.density).toInt()
            )
            insets
        }

        cultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        laborId = intent.getIntExtra("LABOR_ID", -1)
        binding.tvHeaderTitle.text = intent.getStringExtra("LABOR_NOMBRE") ?: "Detalle de Labor"

        binding.btnBack.setOnClickListener { finish() }
        binding.rvLabores.layoutManager = LinearLayoutManager(this)

        loadData()
    }

    private fun loadData() {
        lifecycleScope.launch {
            val labores = db.assetDao().getLaboresByCultivo(cultivoId)
                .filter { it.catalogo_labor_id == laborId }
                .sortedByDescending { it.fecha_realizacion }
            
            var granTotal = 0.0
            labores.forEach { labor ->
                val insumos = db.assetDao().getInsumosByLaborId(labor.id)
                granTotal += labor.costo_mano_obra_total + labor.costo_maquinaria_total + 
                             insumos.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
            }

            // Actualizar resumen en la UI
            binding.tvHeaderTitle.text = "${intent.getStringExtra("LABOR_NOMBRE")} (${labores.size})\nGasto Total: S/ ${String.format("%.2f", granTotal)}"
            
            binding.rvLabores.adapter = LaboresAdapter(labores)
        }
    }

    inner class LaboresAdapter(private val lista: List<LaborRealizadaEntity>) : RecyclerView.Adapter<LaboresAdapter.ViewHolder>() {
        
        private val expandedPositions = mutableSetOf<Int>()

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(ItemLaborDetalleBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            val isExpanded = expandedPositions.contains(position)
            
            holder.binding.tvLaborNombre.text = "Registro #${lista.size - position}"
            holder.binding.tvLaborFecha.text = SimpleDateFormat("EEEE d 'de' MMMM, yyyy", Locale.getDefault()).format(Date(item.fecha_realizacion * 1000)).replaceFirstChar { it.uppercase() }
            
            lifecycleScope.launch {
                val insumos = db.assetDao().getInsumosByLaborId(item.id)
                val costoInsumos = insumos.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
                val totalSesion = item.costo_mano_obra_total + item.costo_maquinaria_total + costoInsumos
                
                holder.binding.tvLaborCostos.text = "MO/Maq: S/ ${String.format("%.2f", item.costo_mano_obra_total + item.costo_maquinaria_total)}"
                holder.binding.tvLaborInsumosCount.text = "${insumos.size} insumos utilizados"
                holder.binding.tvLaborTotal.text = "Total Sesión: S/ ${String.format("%.2f", totalSesion)}"
                
                holder.binding.llInsumosDetalle.removeAllViews()
                holder.binding.llInsumosDetalle.visibility = if (isExpanded) View.VISIBLE else View.GONE
                
                if (isExpanded) {
                    // Nota/Descripción
                    if (!item.observaciones.isNullOrBlank()) {
                        val noteView = TextView(holder.itemView.context).apply {
                            text = "Nota: ${item.observaciones}"
                            textSize = 13f
                            setPadding(16, 8, 16, 8)
                            setTextColor(android.graphics.Color.parseColor("#374151"))
                            setBackgroundColor(android.graphics.Color.parseColor("#FEF3C7"))
                        }
                        holder.binding.llInsumosDetalle.addView(noteView)
                    }

                    // Diseño de sección: Mano de Obra
                    if (item.costo_mano_obra_total > 0) {
                        val moTitle = TextView(holder.itemView.context).apply {
                            text = "Mano de Obra"
                            textSize = 12f
                            setPadding(16, 8, 16, 4)
                            setTypeface(null, android.graphics.Typeface.BOLD)
                            setTextColor(android.graphics.Color.GRAY)
                        }
                        holder.binding.llInsumosDetalle.addView(moTitle)
                        
                        val moList = db.assetDao().getManoObraByLabor(item.id)
                        moList.forEach { mo ->
                            val moRow = TextView(holder.itemView.context).apply {
                                val tipo = if (mo.tipo_id == 1) "Varones" else "Mujeres"
                                text = "  • $tipo: ${mo.cantidad_trabajadores} pers. x ${mo.dias_trabajados} d. → S/ ${String.format("%.2f", mo.subtotal)}"
                                textSize = 13f
                                setPadding(24, 4, 16, 4)
                            }
                            holder.binding.llInsumosDetalle.addView(moRow)
                        }
                    }

                    // Diseño de sección: Maquinaria
                    if (item.costo_maquinaria_total > 0) {
                        val maqRow = TextView(holder.itemView.context).apply {
                            text = "Maquinaria / Otros: S/ ${String.format("%.2f", item.costo_maquinaria_total)}"
                            textSize = 13f
                            setPadding(16, 8, 16, 8)
                            setTypeface(null, android.graphics.Typeface.ITALIC)
                        }
                        holder.binding.llInsumosDetalle.addView(maqRow)
                    }

                    // Diseño de sección: Insumos
                    if (insumos.isNotEmpty()) {
                        val insTitle = TextView(holder.itemView.context).apply {
                            text = "Insumos Detallados"
                            textSize = 12f
                            setPadding(16, 8, 16, 4)
                            setTypeface(null, android.graphics.Typeface.BOLD)
                            setTextColor(android.graphics.Color.GRAY)
                        }
                        holder.binding.llInsumosDetalle.addView(insTitle)

                        insumos.forEach { ins ->
                            val insRow = com.sigcpa.agrosys.databinding.ItemInsumoRowBinding.inflate(LayoutInflater.from(holder.itemView.context), holder.binding.llInsumosDetalle, false)
                            val cat = db.assetDao().getCatalogoInsumoById(ins.catalogo_insumo_id)
                            insRow.tvInsumoNombre.text = cat?.nombre ?: "Insumo"
                            
                            val unidad = cat?.unidad_medida ?: ""
                            val cantStr = if (ins.cantidad % 1.0 == 0.0) ins.cantidad.toInt().toString() else ins.cantidad.toString()
                            insRow.tvInsumoCant.text = "$cantStr $unidad"
                            insRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.costo_unitario) + ins.costo_flete)}"
                            
                            insRow.btnEditInsumo.visibility = View.GONE
                            insRow.btnRemoveInsumo.visibility = View.GONE
                            holder.binding.llInsumosDetalle.addView(insRow.root)
                        }
                    }
                }
            }

            holder.binding.btnAddInsumoItem.visibility = View.GONE
            holder.binding.btnRemoveLabor.visibility = View.GONE
            holder.binding.btnEditLabor.visibility = View.GONE

            holder.itemView.setOnClickListener {
                if (expandedPositions.contains(position)) {
                    expandedPositions.remove(position)
                } else {
                    expandedPositions.add(position)
                }
                notifyItemChanged(position)
            }
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemLaborDetalleBinding) : RecyclerView.ViewHolder(binding.root)
    }
}
