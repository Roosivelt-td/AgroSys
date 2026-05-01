package com.sigcpa.agrosys.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.R
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
        val areaTotal = terreno.area_hectareas
        val areaDisponible = maxOf(0.0, areaTotal - areaOcupada)
        val estaLleno = areaDisponible <= 0.0
        val porcentajeOcupado = if (areaTotal > 0) (areaOcupada / areaTotal).toFloat() else 0f

        holder.binding.apply {
            tvNombreTerreno.text = terreno.nombre
            tvUbicacionTerreno.text = terreno.direccion_referencia ?: terreno.ubicacion_geo ?: "Sin ubicación"
            
            val df = java.text.DecimalFormat("#.##")
            val areaTexto = "${df.format(areaDisponible)} ha libres de ${df.format(areaTotal)} ha"
            tvAreaTerreno.text = areaTexto

            // Remove Progress Background logic as per new UI
            // viewProgressBg.visibility = android.view.View.GONE
            
            tvStatusOverlay.visibility = android.view.View.VISIBLE
            if (estaLleno) {
                tvStatusOverlay.text = root.context.getString(R.string.label_terreno_lleno)
                tvStatusOverlay.background = androidx.appcompat.content.res.AppCompatResources.getDrawable(root.context, R.drawable.bg_status_badge)
                (tvStatusOverlay.background as android.graphics.drawable.GradientDrawable).setColor(android.graphics.Color.parseColor("#FEE2E2")) // Rojo muy claro
                tvStatusOverlay.setTextColor(android.graphics.Color.parseColor("#DC2626")) // Rojo
                
                tvAreaTerreno.setTextColor(android.graphics.Color.parseColor("#DC2626")) // Rojo
                cardTerrenoSelector.setStrokeColor(android.graphics.Color.parseColor("#FEE2E2"))
                cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.parseColor("#FFF1F2")) // Fondo rojizo suave
                root.alpha = 1.0f
            } else {
                tvStatusOverlay.text = root.context.getString(R.string.label_terreno_disponible)
                tvStatusOverlay.background = androidx.appcompat.content.res.AppCompatResources.getDrawable(root.context, R.drawable.bg_status_badge)
                (tvStatusOverlay.background as android.graphics.drawable.GradientDrawable).setColor(android.graphics.Color.parseColor("#DCFCE7")) // Verde muy claro
                tvStatusOverlay.setTextColor(android.graphics.Color.parseColor("#15803D")) // Verde
                
                tvAreaTerreno.setTextColor(android.graphics.Color.parseColor("#15803D")) // Verde
                cardTerrenoSelector.setStrokeColor(android.graphics.Color.parseColor("#F1F5F9"))
                cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.WHITE)
                root.alpha = 1.0f
            }
            
            tvTenenciaTerreno.text = terreno.tipo_tenencia.replaceFirstChar { it.uppercase() }
            
            terreno.foto_path?.let { path ->
                val imageSource = if (path.startsWith("content://") || path.startsWith("file://")) {
                    android.net.Uri.parse(path)
                } else {
                    java.io.File(path)
                }

                Glide.with(root.context)
                    .load(imageSource)
                    .centerCrop()
                    .placeholder(R.drawable.ic_ubicacion_terreno)
                    .error(R.drawable.ic_ubicacion_terreno)
                    .into(ivTerrenoPhotoSelector)
                ivTerrenoPhotoSelector.clearColorFilter()
            } ?: run {
                ivTerrenoPhotoSelector.setImageResource(R.drawable.ic_ubicacion_terreno)
                ivTerrenoPhotoSelector.setColorFilter(android.graphics.Color.parseColor("#15803D"))
                ivTerrenoPhotoSelector.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            }

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