package com.sigcpa.agrosys.ui

import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.tabs.TabLayout
import com.sigcpa.agrosys.database.entities.SolicitudUnionEntity
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import com.sigcpa.agrosys.database.entities.MiembroOrganizacionEntity
import com.sigcpa.agrosys.databinding.ActivityManageMembersBinding
import com.sigcpa.agrosys.repository.AuthRepository
import com.sigcpa.agrosys.ui.adapters.MemberApprovalAdapter
import kotlinx.coroutines.launch

class ManageMembersActivity : AppCompatActivity() {

    private lateinit var binding: ActivityManageMembersBinding
    private lateinit var authRepository: AuthRepository
    private lateinit var adapter: MemberApprovalAdapter
    private var currentTab = 0
    private var orgId = 1
    private var adminId = 1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityManageMembersBinding.inflate(layoutInflater)
        setContentView(binding.root)

        authRepository = AuthRepository(this)
        
        orgId = intent.getIntExtra("ORG_ID", 1)
        adminId = intent.getIntExtra("USER_ID", 1)

        setupUI()
        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }

        adapter = MemberApprovalAdapter(
            onApprove = { item -> handleApproval(item, true) },
            onReject = { item -> handleApproval(item, false) }
        )

        binding.rvMembers.layoutManager = LinearLayoutManager(this)
        binding.rvMembers.adapter = adapter

        binding.tabLayout.addOnTabSelectedListener(object : TabLayout.OnTabSelectedListener {
            override fun onTabSelected(tab: TabLayout.Tab?) {
                currentTab = tab?.position ?: 0
                loadData()
            }
            override fun onTabUnselected(tab: TabLayout.Tab?) {}
            override fun onTabReselected(tab: TabLayout.Tab?) {}
        })
    }

    private fun loadData() {
        lifecycleScope.launch {
            when (currentTab) {
                0 -> {
                    // Solicitudes de unión pendientes
                    val pending = authRepository.getSolicitudesUnionPendientes(orgId)
                    adapter.setList(pending)
                }
                1 -> {
                    // Miembros actuales con roles (UsuarioConRol)
                    val miembros = authRepository.getMiembrosByOrganizacion(orgId)
                    adapter.setList(miembros)
                }
                2 -> {
                    // Todos los usuarios relacionados
                    val all = authRepository.getTodosUsuarios(orgId)
                    adapter.setList(all)
                }
            }
        }
    }

    private fun handleApproval(item: Any, approved: Boolean) {
        showAdminAuthDialog {
            lifecycleScope.launch {
                when (item) {
                    is SolicitudUnionEntity -> {
                        if (approved) {
                            authRepository.aprobarSolicitudUnion(item.id, adminId)
                            Toast.makeText(this@ManageMembersActivity, "Solicitud aprobada", Toast.LENGTH_SHORT).show()
                        } else {
                            authRepository.rechazarSolicitudUnion(item.id)
                            Toast.makeText(this@ManageMembersActivity, "Solicitud rechazada", Toast.LENGTH_SHORT).show()
                        }
                    }
                    is com.sigcpa.agrosys.database.dao.UsuarioConRol -> {
                        if (approved) {
                            // En esta vista, aprobar un "miembro" significa ascenderlo de rol
                            authRepository.ascenderMiembro(item.usuario.id, orgId)
                            Toast.makeText(this@ManageMembersActivity, "Rol ascendido", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
                loadData()
            }
        }
    }

    private fun showAdminAuthDialog(onSuccess: () -> Unit) {
        val dialogBinding = com.sigcpa.agrosys.databinding.DialogAdminAuthBinding.inflate(layoutInflater)
        val dialog = androidx.appcompat.app.AlertDialog.Builder(this)
            .setView(dialogBinding.root)
            .setCancelable(false)
            .create()

        dialog.window?.setBackgroundDrawableResource(android.R.color.transparent)

        dialogBinding.btnCancel.setOnClickListener { dialog.dismiss() }
        dialogBinding.btnConfirm.setOnClickListener {
            val password = dialogBinding.etAdminPassword.text.toString()
            if (password.isNotEmpty()) {
                // Validación simplificada: En producción debería validarse con el repositorio
                onSuccess()
                dialog.dismiss()
            } else {
                Toast.makeText(this, "Ingrese la contraseña", Toast.LENGTH_SHORT).show()
            }
        }
        dialog.show()
    }
}
