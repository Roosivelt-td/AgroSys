package com.sigcpa.agrosys.ui

import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.ArrayAdapter
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.OrganizacionEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterStep2Binding
import com.sigcpa.agrosys.repository.AuthRepository
import kotlinx.coroutines.launch

class RegisterStep2Activity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterStep2Binding
    private lateinit var authRepository: AuthRepository
    private var selectedOrgId: Int? = null
    private var organizaciones: List<OrganizacionEntity> = emptyList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterStep2Binding.inflate(layoutInflater)
        setContentView(binding.root)

        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        ViewCompat.setOnApplyWindowInsetsListener(binding.mainContent) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, systemBars.top, v.paddingRight, v.paddingBottom)
            insets
        }

        authRepository = AuthRepository(this)
        setupUI()
        loadOrganizaciones()
    }

    private fun setupUI() {
        val educationLevels = arrayOf("Primaria", "Secundaria", "Técnico", "Universitario", "Ninguno")
        val adapter = ArrayAdapter(this, android.R.layout.simple_dropdown_item_1line, educationLevels)
        binding.spinnerEducation.setAdapter(adapter)

        // Lógica de Organización
        binding.cbJoinOrg.setOnCheckedChangeListener { _, isChecked ->
            binding.tilSearchOrg.visibility = if (isChecked) View.VISIBLE else View.GONE
            binding.cbRequestSupervisor.visibility = if (isChecked) View.VISIBLE else View.GONE
            if (!isChecked) {
                binding.cbRequestSupervisor.isChecked = false
                selectedOrgId = null
                binding.etSearchOrg.setText("")
            }
        }

        binding.etSearchOrg.setOnItemClickListener { parent, _, position, _ ->
            val selection = parent.getItemAtPosition(position) as String
            val org = organizaciones.find { it.nombre == selection }
            if (org != null) {
                showOrgDetailDialog(org)
            }
        }

        binding.btnBackStep.setOnClickListener { finish() }
        binding.btnBack.setOnClickListener { finish() }

        binding.btnFinalizeRegister.setOnClickListener {
            finalizeRegistration()
        }
    }

    private fun showOrgDetailDialog(org: OrganizacionEntity) {
        val dialogView = LayoutInflater.from(this).inflate(R.layout.dialog_org_detail, null)
        val dialog = AlertDialog.Builder(this)
            .setView(dialogView)
            .create()

        dialogView.findViewById<TextView>(R.id.tvDetailOrgName).text = org.nombre
        dialogView.findViewById<TextView>(R.id.tvDetailOrgRuc).text = "RUC: ${org.ruc ?: "No especificado"}"
        dialogView.findViewById<TextView>(R.id.tvDetailOrgType).text = "Tipo: ${org.tipo ?: "No especificado"}"
        dialogView.findViewById<TextView>(R.id.tvDetailOrgRubro).text = "Rubro: ${org.rubro ?: "No especificado"}"
        dialogView.findViewById<TextView>(R.id.tvDetailOrgAddress).text = "Dirección: ${org.direccion ?: "No especificado"}"
        dialogView.findViewById<TextView>(R.id.tvDetailOrgPhone).text = "Teléfono: ${org.telefono_contacto ?: "No especificado"}"
        dialogView.findViewById<TextView>(R.id.tvDetailOrgDescription).text = org.descripcion ?: "Sin descripción adicional."

        dialogView.findViewById<View>(R.id.btnConfirmJoin).setOnClickListener {
            selectedOrgId = org.id
            binding.etSearchOrg.setText(org.nombre)
            dialog.dismiss()
            Toast.makeText(this, "Organización seleccionada: ${org.nombre}", Toast.LENGTH_SHORT).show()
        }

        dialog.show()
    }

    private fun loadOrganizaciones() {
        lifecycleScope.launch {
            organizaciones = authRepository.getAllOrganizaciones()
            val names = organizaciones.map { it.nombre }
            val adapter = ArrayAdapter(this@RegisterStep2Activity, android.R.layout.simple_dropdown_item_1line, names)
            binding.etSearchOrg.setAdapter(adapter)
        }
    }

    private fun finalizeRegistration() {
        val dni = binding.etRegDni.text.toString().trim()
        if (dni.isEmpty()) {
            Toast.makeText(this, "El DNI es obligatorio", Toast.LENGTH_SHORT).show()
            return
        }

        if (binding.cbJoinOrg.isChecked && selectedOrgId == null) {
            Toast.makeText(this, "Selecciona una organización válida de la lista", Toast.LENGTH_SHORT).show()
            return
        }

        val userData = mutableMapOf(
            "name" to (intent.getStringExtra("REG_NAME") ?: ""),
            "lastName" to (intent.getStringExtra("REG_LASTNAME") ?: ""),
            "email" to (intent.getStringExtra("REG_EMAIL") ?: ""),
            "password" to (intent.getStringExtra("REG_PASSWORD") ?: ""),
            "phone" to (intent.getStringExtra("REG_PHONE") ?: ""),
            "dni" to dni,
            "experience" to binding.etRegExp.text.toString().trim(),
            "education" to binding.spinnerEducation.text.toString()
        )

        lifecycleScope.launch {
            val userId = authRepository.registerUser(
                userData = userData,
                orgId = if (binding.cbJoinOrg.isChecked) selectedOrgId else null,
                requestSupervisor = binding.cbRequestSupervisor.isChecked
            )

            if (userId != -1) {
                Toast.makeText(this@RegisterStep2Activity, "¡Registro exitoso!", Toast.LENGTH_SHORT).show()
                val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
                sharedPref.edit().putInt("USER_ID", userId).apply()
                
                val intent = Intent(this@RegisterStep2Activity, DashboardActivity::class.java).apply {
                    putExtra("USER_NAME", userData["name"])
                    putExtra("USER_ROLE", "usuario")
                }
                startActivity(intent)
                finishAffinity()
            } else {
                Toast.makeText(this@RegisterStep2Activity, "Error al registrar usuario", Toast.LENGTH_SHORT).show()
            }
        }
    }
}
