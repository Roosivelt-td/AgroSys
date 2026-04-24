package com.sigcpa.agrosys.ui

import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.databinding.ActivityRegisterStep2Binding
import com.sigcpa.agrosys.repository.AuthRepository
import kotlinx.coroutines.launch

class RegisterStep2Activity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterStep2Binding
    private lateinit var authRepository: AuthRepository

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterStep2Binding.inflate(layoutInflater)
        setContentView(binding.root)

        // Insets & Branding
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        ViewCompat.setOnApplyWindowInsetsListener(binding.headerContainer) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, systemBars.top, v.paddingRight, v.paddingBottom)
            insets
        }

        authRepository = AuthRepository(this)
        setupUI()
    }

    private var selectedOrgId: Int? = null

    private fun setupUI() {
        // Setup Education Spinner
        val educationLevels = arrayOf("Primaria", "Secundaria", "Técnico", "Universitario", "Ninguno")
        val adapterEdu = ArrayAdapter(this, android.R.layout.simple_dropdown_item_1line, educationLevels)
        binding.spinnerEducation.setAdapter(adapterEdu)

        // Setup Organizations Spinner (Búsqueda)
        lifecycleScope.launch {
            val orgs = authRepository.getOrganizaciones("")
            if (orgs.isEmpty()) {
                binding.layoutRegOrg.visibility = View.GONE // Si no hay orgs, el admin las creará luego
            } else {
                val orgNames = orgs.map { it.nombre }
                val adapterOrg = ArrayAdapter(this@RegisterStep2Activity, android.R.layout.simple_dropdown_item_1line, orgNames)
                binding.etRegOrg.setAdapter(adapterOrg)
                binding.etRegOrg.setOnItemClickListener { _, _, position, _ ->
                    selectedOrgId = orgs[position].id
                }
            }
        }

        // Role Selection Logic
        binding.toggleRoleGroup.addOnButtonCheckedListener { _, checkedId, isChecked ->
            if (isChecked) {
                when (checkedId) {
                    binding.btnRoleAgri.id -> {
                        binding.etRegExp.visibility = View.VISIBLE
                        binding.spinnerEducation.visibility = View.VISIBLE
                        binding.layoutRegOrg.visibility = View.VISIBLE
                    }
                    else -> {
                        binding.etRegExp.visibility = View.GONE
                        binding.spinnerEducation.visibility = View.GONE
                        binding.layoutRegOrg.visibility = View.VISIBLE
                    }
                }
            }
        }

        binding.btnBackStep.setOnClickListener {
            finish()
        }

        binding.btnBack.setOnClickListener {
            finish()
        }

        binding.btnFinalizeRegister.setOnClickListener {
            finalizeRegistration()
        }
    }

    private fun finalizeRegistration() {
        val role = when (binding.toggleRoleGroup.checkedButtonId) {
            binding.btnRoleAgri.id -> "agricultor"
            binding.btnRoleSuper.id -> "supervisor"
            else -> "admin"
        }

        val dni = binding.etRegDni.text.toString().trim()
        if (dni.isEmpty()) {
            Toast.makeText(this, getString(R.string.msg_error_dni_obligatorio), Toast.LENGTH_SHORT).show()
            return
        }

        val userData = mapOf(
            "nombre" to (intent.getStringExtra("REG_NAME") ?: ""),
            "apellidos" to (intent.getStringExtra("REG_LASTNAME") ?: ""),
            "email" to (intent.getStringExtra("REG_EMAIL") ?: ""),
            "password" to (intent.getStringExtra("REG_PASSWORD") ?: ""),
            "telefono" to (intent.getStringExtra("REG_PHONE") ?: ""),
            "dni" to dni
        )

        val extraData = mapOf(
            "experience" to binding.etRegExp.text.toString().trim(),
            "education" to binding.spinnerEducation.text.toString()
        )

        lifecycleScope.launch {
            val userId = authRepository.registerFullUser(userData, role, selectedOrgId, extraData)
            if (userId != -1) {
                Toast.makeText(this@RegisterStep2Activity, getString(R.string.msg_registro_exitoso), Toast.LENGTH_SHORT).show()
                
                // Save session
                val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
                sharedPref.edit().putInt("USER_ID", userId).apply()
                
                val intent = Intent(this@RegisterStep2Activity, DashboardActivity::class.java).apply {
                    putExtra("USER_NAME", userData["name"])
                    putExtra("USER_ROLE", role)
                }
                startActivity(intent)
                finishAffinity()
            } else {
                Toast.makeText(this@RegisterStep2Activity, getString(R.string.msg_error_guardar_datos), Toast.LENGTH_SHORT).show()
            }
        }
    }
}
