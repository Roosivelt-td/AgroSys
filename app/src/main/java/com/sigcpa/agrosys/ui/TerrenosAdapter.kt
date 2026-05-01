package com.sigcpa.agrosys.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.TerrenoConStats
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ItemTerrenoBinding

class TerrenosAdapter(
    private var terrenos: List<TerrenoConStats>,
    private val onItemClick: (TerrenoEntity) -> Unit
) : RecyclerView.Adapter<TerrenosAdapter.TerrenoViewHolder>() {

    class TerrenoViewHolder(val binding: ItemTerrenoBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): TerrenoViewHolder {
        val binding = ItemTerrenoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return TerrenoViewHolder(binding)
    }

    override fun onBindViewHolder(holder: TerrenoViewHolder, position: Int) {
        val stat = terrenos[position]
        val terreno = stat.terreno
        with(holder.binding) {
            tvNombre.text = terreno.nombre
            tvArea.text = "${terreno.area_hectareas} ha"
            tvUbicacion.text = terreno.direccion_referencia ?: "Sin ubicación"
            
            val isPropio = terreno.tipo_tenencia.lowercase() == "propio"
            tvTenencia.text = if (isPropio) "🏠 Propio" else "📄 Alquilado"
            
            tvCultivosCount.text = "${stat.cultivosCount} cultivos"
            
            // Lógica de Ocupación
            val areaTotal = terreno.area_hectareas
            val areaOcupada = stat.areaOcupada
            val porcentaje = if (areaTotal > 0) ((areaOcupada / areaTotal) * 100).toInt() else 0
            
            tvPorcentajeOcupado.text = "$porcentaje%"
            pbOcupacion.progress = porcentaje
            
            // Color de la barra según saturación
            if (porcentaje >= 90) {
                pbOcupacion.progressTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.RED)
            } else {
                pbOcupacion.progressTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.parseColor("#15803D"))
            }
            
            terreno.foto_path?.let { path ->
                ivTerrenoPhoto.clearColorFilter()
                ivTerrenoPhoto.setPadding(0, 0, 0, 0)
                Glide.with(root.context)
                    .load(path)
                    .placeholder(R.drawable.uploap)
                    .centerCrop()
                    .into(ivTerrenoPhoto)
            } ?: run {
                ivTerrenoPhoto.setImageResource(R.drawable.uploap)
                ivTerrenoPhoto.setPadding(16, 16, 16, 16)
                ivTerrenoPhoto.setColorFilter(root.context.getColor(android.R.color.darker_gray))
            }

            root.setOnClickListener { onItemClick(terreno) }
        }
    }

    override fun getItemCount() = terrenos.size

    fun updateData(newTerrenos: List<TerrenoConStats>) {
        terrenos = newTerrenos
        notifyDataSetChanged()
    }
}
