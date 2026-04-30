package com.sigcpa.agrosys.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.databinding.ItemAdminRecordBinding

data class CatalogItem(
    val id: Int,
    val name: String,
    val description: String,
    val rawObject: Any
)

class AdminCatalogAdapter(
    private var items: List<CatalogItem>,
    private val onEdit: (CatalogItem) -> Unit
) : RecyclerView.Adapter<AdminCatalogAdapter.ViewHolder>() {

    class ViewHolder(val binding: ItemAdminRecordBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemAdminRecordBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = items[position]
        holder.binding.apply {
            tvRecordName.text = item.name
            tvRecordDesc.text = item.description
            btnEditRecord.setOnClickListener { onEdit(item) }
        }
    }

    override fun getItemCount() = items.size

    fun updateData(newItems: List<CatalogItem>) {
        items = newItems
        notifyDataSetChanged()
    }
}
