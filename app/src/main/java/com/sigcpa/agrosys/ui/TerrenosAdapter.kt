package com.sigcpa.agrosys.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ItemTerrenoBinding

class TerrenosAdapter(
    private var terrenos: List<TerrenoEntity>,
    private val onItemClick: (TerrenoEntity) -> Unit
) : RecyclerView.Adapter<TerrenosAdapter.TerrenoViewHolder>() {

    class TerrenoViewHolder(val binding: ItemTerrenoBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TerrenoViewHolder {
        val binding = ItemTerrenoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return TerrenoViewHolder(binding)
    }

    override fun onBindViewHolder(holder: TerrenoViewHolder, position: Int) {
        val terreno = terrenos[position]
        with(holder.binding) {
            tvNombre.text = terreno.nombre
            tvArea.text = "${terreno.area_hectareas} ha"
            tvUbicacion.text = terreno.direccion_referencia ?: "Sin ubicación"
            
            val isPropio = terreno.tipo_tenencia.lowercase() == "propio"
            tvTenencia.text = if (isPropio) "🏠 Propio" else "📄 Alquilado"
            // Note: The count of crops would ideally come from a JOIN or another query, 
            // but for now we display a placeholder or 0 if not provided in the entity.
            tvCultivosCount.text = "Consultando cultivos..." // This can be updated later with a more complex data model
            
            root.setOnClickListener { onItemClick(terreno) }
        }
    }

    override fun getItemCount() = terrenos.size

    fun updateData(newTerrenos: List<TerrenoEntity>) {
        terrenos = newTerrenos
        notifyDataSetChanged()
    }
}
