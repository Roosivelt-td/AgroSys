package com.sigcpa.agrosys.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ItemTerrenoSelectorBinding

class TerrenoSelectorAdapter(
    private val terrenos: List<Pair<TerrenoEntity, Double>>,
    private val onTerrenoSelected: (TerrenoEntity) -> Unit
) : RecyclerView.Adapter<TerrenoSelectorAdapter.ViewHolder>() {

    private var selectedPosition = -1

    inner class ViewHolder(val binding: ItemTerrenoSelectorBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemTerrenoSelectorBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val (terreno, areaOcupada) = terrenos[position]
        val areaDisponible = maxOf(0.0, terreno.area_hectareas - areaOcupada)
        val estaLleno = areaDisponible <= 0.0

        holder.binding.apply {
            tvNombreTerreno.text = terreno.nombre
            tvUbicacionTerreno.text = terreno.direccion_referencia ?: terreno.ubicacion_geo ?: "Sin ubicación"
            
            val areaTexto = if (estaLleno) {
                "SIN ÁREA DISPONIBLE"
            } else {
                "${String.format("%.2f", areaDisponible)} ha libres de ${terreno.area_hectareas} ha"
            }
            tvAreaTerreno.text = areaTexto
            
            if (estaLleno) {
                tvAreaTerreno.setTextColor(android.graphics.Color.parseColor("#DC2626")) // Rojo
                cardTerrenoSelector.setStrokeColor(android.graphics.Color.parseColor("#FEE2E2"))
                cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.parseColor("#FEF2F2"))
                root.alpha = 0.7f
            } else {
                tvAreaTerreno.setTextColor(android.graphics.Color.parseColor("#15803D")) // Verde
                cardTerrenoSelector.setStrokeColor(android.graphics.Color.parseColor("#E5E7EB"))
                cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.WHITE)
                root.alpha = 1.0f
            }
            
            tvTenenciaTerreno.text = terreno.tipo_tenencia.replaceFirstChar { it.uppercase() }
            
            rbSelected.isChecked = position == selectedPosition
            rbSelected.isEnabled = !estaLleno

            root.setOnClickListener {
                if (!estaLleno) {
                    val previousPosition = selectedPosition
                    selectedPosition = holder.adapterPosition
                    notifyItemChanged(previousPosition)
                    notifyItemChanged(selectedPosition)
                    onTerrenoSelected(terreno)
                }
            }
        }
    }

    override fun getItemCount() = terrenos.size
}