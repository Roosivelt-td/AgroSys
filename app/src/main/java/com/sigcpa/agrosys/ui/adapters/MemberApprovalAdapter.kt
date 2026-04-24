package com.sigcpa.agrosys.ui.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.database.entities.SolicitudUnionEntity
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import com.sigcpa.agrosys.database.dao.UsuarioConRol
import com.sigcpa.agrosys.databinding.ItemMemberApprovalBinding

class MemberApprovalAdapter(
    private val onApprove: (Any) -> Unit,
    private val onReject: (Any) -> Unit
) : RecyclerView.Adapter<MemberApprovalAdapter.ViewHolder>() {

    private var items = mutableListOf<Any>()

    fun setList(newList: List<Any>) {
        items.clear()
        items.addAll(newList)
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemMemberApprovalBinding.inflate(
            LayoutInflater.from(parent.context), parent, false
        )
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val item = items[position]
        holder.bind(item)
    }

    override fun getItemCount(): Int = items.size

    inner class ViewHolder(private val binding: ItemMemberApprovalBinding) :
        RecyclerView.ViewHolder(binding.root) {

        fun bind(item: Any) {
            binding.roleChangeContainer.visibility = View.GONE
            
            when (item) {
                is UsuarioConRol -> {
                    val u = item.usuario
                    binding.tvUserName.text = "${u.nombre} ${u.apellidos}"
                    binding.tvUserEmail.text = u.email
                    binding.tvUserRole.text = item.rol_nombre.uppercase()
                    
                    // En la lista de miembros, "Aprobar" puede significar "Ascender"
                    binding.btnApprove.setOnClickListener { onApprove(item) }
                    binding.btnReject.visibility = View.GONE // No ocultamos al miembro, solo opciones de gestión
                }
                is SolicitudUnionEntity -> {
                    binding.tvUserName.text = "Solicitud de Unión"
                    binding.tvUserEmail.text = "ID Solicitante: ${item.usuario_solicitante_id}"
                    binding.tvUserRole.text = "PENDIENTE"
                    
                    binding.roleChangeContainer.visibility = View.VISIBLE
                    binding.tvRequestedRole.text = item.mensaje ?: "Sin mensaje"
                    
                    binding.btnApprove.setOnClickListener { onApprove(item) }
                    binding.btnReject.setOnClickListener { onReject(item) }
                    binding.btnReject.visibility = View.VISIBLE
                }
                is UsuarioEntity -> {
                    binding.tvUserName.text = "${item.nombre} ${item.apellidos}"
                    binding.tvUserEmail.text = item.email
                    binding.tvUserRole.text = "USUARIO"
                    
                    binding.btnApprove.setOnClickListener { onApprove(item) }
                    binding.btnReject.setOnClickListener { onReject(item) }
                    binding.btnReject.visibility = View.VISIBLE
                }
            }
        }
    }
}
