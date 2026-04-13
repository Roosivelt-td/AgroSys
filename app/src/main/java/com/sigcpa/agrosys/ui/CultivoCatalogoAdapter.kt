package com.sigcpa.agrosys.ui

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import com.sigcpa.agrosys.databinding.ItemCatalogoGridBinding

class CultivoCatalogoAdapter(
    private val items: List<CatalogoCultivoEntity>,
    private val onItemSelected: (CatalogoCultivoEntity) -> Unit
) : RecyclerView.Adapter<CultivoCatalogoAdapter.ViewHolder>() {

    private var selectedPosition = -1

    class ViewHolder(val binding: ItemCatalogoGridBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemCatalogoGridBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = items[position]
        holder.binding.tvNombreCultivo.text = item.nombre
        
        val isSelected = selectedPosition == position
        
        holder.binding.cardCultivo.apply {
            strokeColor = if (isSelected) {
                ContextCompat.getColor(context, com.google.android.material.R.color.design_default_color_primary)
            } else {
                ContextCompat.getColor(context, android.R.color.transparent)
            }
            strokeWidth = if (isSelected) 4 else 0
            setCardBackgroundColor(if (isSelected) 0xFFE8F5E9.toInt() else 0xFFF9FAFB.toInt())
        }

        holder.binding.root.setOnClickListener {
            val previousSelected = selectedPosition
            selectedPosition = holder.adapterPosition
            notifyItemChanged(previousSelected)
            notifyItemChanged(selectedPosition)
            onItemSelected(item)
        }
    }

    override fun getItemCount() = items.size
}
