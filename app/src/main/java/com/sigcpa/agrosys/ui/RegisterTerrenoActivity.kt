package com.sigcpa.agrosys.ui

import android.content.Context
import android.graphics.Color
import android.os.Bundle
import android.view.View
import android.widget.LinearLayout
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterTerrenoBinding
import com.sigcpa.agrosys.repository.AssetRepository
import kotlinx.coroutines.launch

class RegisterTerrenoActivity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterTerrenoBinding
    private lateinit var assetRepository: AssetRepository
    private var agricultorId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterTerrenoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Branding
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        assetRepository = AssetRepository(this)
        
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            insets
        }
        
        // Get agricultor ID from session
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        
        lifecycleScope.launch {
            val id = assetRepository.getAgricultorIdByUserId(userId)
            if (id != null) {
                agricultorId = id
            } else {
                Toast.makeText(this@RegisterTerrenoActivity, getString(R.string.error_profile_not_found), Toast.LENGTH_SHORT).show()
                finish()
            }
        }

        setupUI()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }

        binding.toggleTenencia.addOnButtonCheckedListener { _, checkedId, isChecked ->
            if (isChecked) {
                binding.layoutCosto.visibility = if (checkedId == binding.btnAlquilado.id) View.VISIBLE else View.GONE
            }
        }

        binding.btnSave.setOnClickListener {
            saveTerreno()
        }
    }

    private fun saveTerreno() {
        val nombre = binding.etNombre.text.toString().trim()
        val ubicacion = binding.etUbicacion.text.toString().trim()
        val areaStr = binding.etArea.text.toString().trim()
        val quality = binding.etCalidadSuelo.text.toString().trim()
        val water = binding.etFuenteAgua.text.toString().trim()
        val costStr = binding.etCostoAlquiler.text.toString().trim()
        val isAlquilado = binding.btnAlquilado.isChecked

        // VALIDACIONES OBLIGATORIAS
        if (nombre.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_nombre_required), Toast.LENGTH_SHORT).show()
            binding.etNombre.requestFocus()
            return
        }

        if (ubicacion.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_ubicacion_required), Toast.LENGTH_SHORT).show()
            binding.etUbicacion.requestFocus()
            return
        }

        if (areaStr.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_area_required), Toast.LENGTH_SHORT).show()
            binding.etArea.requestFocus()
            return
        }

        val area = areaStr.toDoubleOrNull() ?: 0.0
        if (area <= 0) {
            Toast.makeText(this, getString(R.string.error_invalid_area), Toast.LENGTH_SHORT).show()
            binding.etArea.requestFocus()
            return
        }

        if (isAlquilado && costStr.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_costo_required), Toast.LENGTH_SHORT).show()
            binding.etCostoAlquiler.requestFocus()
            return
        }

        val cost = costStr.toDoubleOrNull() ?: 0.0

        val terreno = TerrenoEntity(
            agricultor_id = agricultorId,
            nombre = nombre,
            ubicacion_geo = null,
            direccion_referencia = ubicacion,
            area_hectareas = area,
            tipo_tenencia = if (isAlquilado) "alquilado" else "propio",
            costo_alquiler_anual = cost,
            calidad_suelo = quality,
            fuente_agua = water
        )

        lifecycleScope.launch {
            val success = assetRepository.saveTerreno(terreno)
            if (success) {
                Toast.makeText(this@RegisterTerrenoActivity, getString(R.string.msg_terreno_saved), Toast.LENGTH_SHORT).show()
                finish()
            } else {
                Toast.makeText(this@RegisterTerrenoActivity, getString(R.string.error_db_save), Toast.LENGTH_SHORT).show()
            }
        }
    }
}
