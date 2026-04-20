package com.sigcpa.agrosys.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ItemTerrenoSelectorBinding

class TerrenoSelectorAdapter(
    private val terrenos: List<TerrenoEntity>,
    private val onTerrenoSelected: (TerrenoEntity) -> Unit
) : RecyclerView.Adapter<TerrenoSelectorAdapter.ViewHolder>() {

    private var selectedPosition = -1

    inner class ViewHolder(val binding: ItemTerrenoSelectorBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemTerrenoSelectorBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val terreno = terrenos[position]
        holder.binding.apply {
            tvNombreTerreno.text = terreno.nombre
            tvUbicacionTerreno.text = terreno.direccion_referencia ?: terreno.ubicacion_geo ?: "Sin ubicación"
            tvAreaTerreno.text = "${terreno.area_hectareas} ha"
            tvTenenciaTerreno.text = terreno.tipo_tenencia.replaceFirstChar { it.uppercase() }
            
            rbSelected.isChecked = position == selectedPosition

            root.setOnClickListener {
                val previousPosition = selectedPosition
                selectedPosition = holder.adapterPosition
                notifyItemChanged(previousPosition)
                notifyItemChanged(selectedPosition)
                onTerrenoSelected(terreno)
            }
        }
    }

    override fun getItemCount() = terrenos.size
}