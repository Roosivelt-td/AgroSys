package com.sigcpa.agrosys.ui.adapters

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import com.sigcpa.agrosys.databinding.ItemCultivoCursoBinding
import java.text.SimpleDateFormat
import java.util.*

class CultivoCursoAdapter(
    private var list: List<Triple<CultivoEntity, TerrenoEntity?, CatalogoCultivoEntity?>>
) : RecyclerView.Adapter<CultivoCursoAdapter.ViewHolder>() {

    class ViewHolder(val binding: ItemCultivoCursoBinding) : RecyclerView.ViewHolder(binding.root)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemCultivoCursoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val (cultivo, terreno, catalogo) = list[position]
        val sdf = SimpleDateFormat("dd MMM", Locale.getDefault())

        holder.binding.tvNombreCultivo.text = catalogo?.nombre ?: "Cultivo"
        holder.binding.tvTerrenoNombre.text = terreno?.nombre ?: "Sin terreno"
        val dateLong = cultivo.fecha_siembra ?: cultivo.fecha_planificada ?: 0L
        val prefix = if (cultivo.fecha_siembra != null) "Siembra" else "Planificado"
        holder.binding.tvFechaSiembra.text = "$prefix: ${sdf.format(Date(dateLong * 1000))}"
        holder.binding.tvArea.text = "${cultivo.area_destinada ?: 0.0} ha"
    }

    override fun getItemCount(): Int = list.size

    fun updateData(newList: List<Triple<CultivoEntity, TerrenoEntity?, CatalogoCultivoEntity?>>) {
        list = newList
        notifyDataSetChanged()
    }
}
