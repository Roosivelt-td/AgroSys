package com.sigcpa.agrosys.ui

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ItemCultivoBinding
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class CultivosAdapter(
    private var cultivos: List<Triple<CultivoEntity, String, String>>, // Cultivo, Terreno Name, Tenencia
    private val onItemClick: (CultivoEntity) -> Unit
) : RecyclerView.Adapter<CultivosAdapter.CultivoViewHolder>() {

    fun updateData(newData: List<Triple<CultivoEntity, String, String>>) {
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
        fun bind(item: Triple<CultivoEntity, String, String>) {
            val cultivo = item.first
            val terrenoNombre = item.second
            val tenencia = item.third
            
            binding.tvNombreLote.text = cultivo.nombre_lote ?: "Sin nombre"
            binding.tvTerreno.text = terrenoNombre
            binding.tvTenencia.text = tenencia.uppercase()
            
            // Color para tenencia
            if (tenencia.lowercase() == "alquilado") {
                binding.tvTenencia.setTextColor(binding.root.context.getColor(R.color.blue_700))
                binding.tvTenencia.backgroundTintList = binding.root.context.getColorStateList(R.color.blue_100)
            } else {
                binding.tvTenencia.setTextColor(binding.root.context.getColor(R.color.green_700))
                binding.tvTenencia.backgroundTintList = binding.root.context.getColorStateList(R.color.green_100)
            }

            binding.tvArea.text = "${cultivo.area_destinada ?: 0.0} ha"
            
            val sdf = SimpleDateFormat("dd/MM", Locale.getDefault())
            val sdfFull = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            
            if (cultivo.fecha_planificada != null) {
                binding.tvFechaPlanificada.text = "P: ${sdf.format(Date(cultivo.fecha_planificada * 1000))}"
                binding.tvFechaPlanificada.visibility = View.VISIBLE
            } else {
                binding.tvFechaPlanificada.visibility = View.GONE
            }

            if (cultivo.fecha_siembra != null) {
                binding.tvFecha.text = "S: ${sdfFull.format(Date(cultivo.fecha_siembra * 1000))}"
            } else if (cultivo.fecha_planificada != null) {
                binding.tvFecha.text = "P: ${sdfFull.format(Date(cultivo.fecha_planificada * 1000))}"
            } else {
                binding.tvFecha.text = "S: --/--/--"
            }
            
            val context = binding.root.context
            when(cultivo.estado.lowercase()) {
                "activo" -> {
                    binding.tvEstado.text = "ACTIVO"
                    binding.tvEstado.setTextColor(context.getColor(R.color.green_700))
                    binding.tvEstado.backgroundTintList = context.getColorStateList(R.color.green_100)
                }
                "planificado" -> {
                    binding.tvEstado.text = "PLANIFICADO"
                    binding.tvEstado.setTextColor(context.getColor(R.color.blue_700))
                    binding.tvEstado.backgroundTintList = context.getColorStateList(R.color.blue_100)
                }
                "en_cosecha" -> {
                    binding.tvEstado.text = "EN COSECHA"
                    binding.tvEstado.setTextColor(context.getColor(R.color.yellow_700))
                    binding.tvEstado.backgroundTintList = context.getColorStateList(R.color.yellow_100)
                }
                "cosechado" -> {
                    binding.tvEstado.text = "COSECHADO"
                    binding.tvEstado.setTextColor(context.getColor(R.color.gray_700))
                    binding.tvEstado.backgroundTintList = context.getColorStateList(R.color.gray_100)
                }
                "perdido" -> {
                    binding.tvEstado.text = "PERDIDO"
                    binding.tvEstado.setTextColor(context.getColor(R.color.red_700))
                    binding.tvEstado.backgroundTintList = context.getColorStateList(R.color.red_100)
                }
                else -> {
                    binding.tvEstado.text = cultivo.estado.uppercase()
                    binding.tvEstado.setTextColor(context.getColor(R.color.gray_700))
                    binding.tvEstado.backgroundTintList = context.getColorStateList(R.color.gray_100)
                }
            }
            
            // Cargar imagen
            if (!cultivo.foto_path.isNullOrEmpty()) {
                val imgFile = File(cultivo.foto_path)
                if (imgFile.exists()) {
                    Glide.with(context)
                        .load(imgFile)
                        .placeholder(R.drawable.agro_cosecha)
                        .error(R.drawable.agro_cosecha)
                        .into(binding.ivCultivo)
                } else {
                    binding.ivCultivo.setImageResource(R.drawable.agro_cosecha)
                }
            } else {
                binding.ivCultivo.setImageResource(R.drawable.agro_cosecha)
            }

            // Finanzas - simplistic for now as per HTML mock
            binding.tvFinanzas.text = "S/ 0.00" 

            binding.root.setOnClickListener { onItemClick(cultivo) }
        }
    }
}
