package com.sigcpa.agrosys.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ItemCultivoBinding
import java.text.SimpleDateFormat
import java.util.*

class CultivosAdapter(
    private var cultivos: List<Pair<CultivoEntity, String>>, // Cultivo and Terreno Name
    private val onItemClick: (CultivoEntity) -> Unit
) : RecyclerView.Adapter<CultivosAdapter.CultivoViewHolder>() {

    fun updateData(newData: List<Pair<CultivoEntity, String>>) {
        this.cultivos = newData
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CultivoViewHolder {
        val binding = ItemCultivoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return CultivoViewHolder(binding)
    }

    override fun onBindViewHolder(holder: CultivoViewHolder, position: Int) {
        holder.bind(cultivos[position])
    }

    override fun getItemCount(): Int = cultivos.size

    inner class CultivoViewHolder(private val binding: ItemCultivoBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: Pair<CultivoEntity, String>) {
            val cultivo = item.first
            val terrenoNombre = item.second
            
            binding.tvNombreLote.text = cultivo.nombre_lote ?: "Sin nombre"
            binding.tvTerreno.text = terrenoNombre
            binding.tvArea.text = "${cultivo.area_destinada ?: 0.0} ha"
            
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            binding.tvFecha.text = sdf.format(Date(cultivo.fecha_siembra * 1000))
            
            binding.tvEstado.text = when(cultivo.estado) {
                "activo" -> "🌱 Activo"
                "planificado" -> "📋 Planificado"
                "cosechado" -> "🍂 Cosechado"
                "perdido" -> "⚠️ Perdido"
                else -> cultivo.estado
            }
            
            // Finanzas - simplistic for now as per HTML mock
            binding.tvFinanzas.text = "S/ 0.00" 

            binding.root.setOnClickListener { onItemClick(cultivo) }
        }
    }
}
