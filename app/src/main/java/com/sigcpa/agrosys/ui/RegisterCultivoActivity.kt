package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.os.Bundle
import android.view.View
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.graphics.toColorInt
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.sigcpa.agrosys.ui.adapters.TerrenoSelectorAdapter
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterCultivoBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class RegisterCultivoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterCultivoBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var selectedTerreno: TerrenoEntity? = null
    private var selectedCatalogo: CatalogoCultivoEntity? = null
    private var selectedStatus: String = "activo"
    private val calendarSiembra = Calendar.getInstance()
    private val calendarPlanificada = Calendar.getInstance()
    private var hasSelectedSiembra = false
    private var hasSelectedPlanificada = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterCultivoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupUI()
        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        binding.etFechaPlanificada.setOnClickListener { showDatePicker(true) }
        binding.etFechaSiembra.setOnClickListener { showDatePicker(false) }
        
        binding.btnStatusPlanificado.setOnClickListener { updateStatus("planificado") }
        binding.btnStatusActivo.setOnClickListener { updateStatus("activo") }
        updateStatus("activo")

        binding.btnSaveCultivo.setOnClickListener { saveCultivo() }
    }

    private fun setupSugerencias(cultivos: List<CatalogoCultivoEntity>) {
        binding.gridSugerencias.removeAllViews()
        cultivos.forEach { cultivo ->
            val button = com.google.android.material.button.MaterialButton(
                this,
                null,
                com.google.android.material.R.attr.materialButtonOutlinedStyle
            ).apply {
                layoutParams = android.widget.GridLayout.LayoutParams().apply {
                    width = 0
                    height = (40 * resources.displayMetrics.density).toInt()
                    columnSpec = android.widget.GridLayout.spec(android.widget.GridLayout.UNDEFINED, 1f)
                    setMargins(
                        (2 * resources.displayMetrics.density).toInt(),
                        (2 * resources.displayMetrics.density).toInt(),
                        (2 * resources.displayMetrics.density).toInt(),
                        (2 * resources.displayMetrics.density).toInt()
                    )
                }
                text = cultivo.nombre
                textSize = 10f
                isAllCaps = false
                setTextColor("#166534".toColorInt())
                backgroundTintList = android.content.res.ColorStateList.valueOf("#f0fdf4".toColorInt())
                strokeColor = android.content.res.ColorStateList.valueOf("#166534".toColorInt())
                strokeWidth = (0.5 * resources.displayMetrics.density).toInt()
                cornerRadius = (8 * resources.displayMetrics.density).toInt()
                
                setOnClickListener {
                    binding.autoCompleteCultivo.setText(cultivo.nombre, false)
                    selectedCatalogo = cultivo
                    updateCicloInfo(cultivo)
                }
            }
            binding.gridSugerencias.addView(button)
        }
    }

    private fun updateCicloInfo(it: CatalogoCultivoEntity) {
        binding.tvCicloInfo.visibility = View.VISIBLE
        val ciclo = if (it.tipo_ciclo == "perenne") "Perenne" else "${it.dias_a_cosecha_promedio ?: "--"} días"
        binding.tvCicloInfo.text = "Ciclo productivo estimado: $ciclo"
    }

    private fun seleccionarCultivoPorNombre(nombre: String) {
        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoCultivos()
            selectedCatalogo = catalogo.find { it.nombre.equals(nombre, ignoreCase = true) }
            selectedCatalogo?.let { updateCicloInfo(it) }
        }
    }

    private fun loadData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        val preSelectedTerrenoId = intent.getIntExtra("TERRENO_ID", -1)

        lifecycleScope.launch {
            val agricultor = db.userDao().getAgricultorByUserId(userId) ?: return@launch
            
            if (preSelectedTerrenoId != -1) {
                val terreno = db.assetDao().getTerrenoById(preSelectedTerrenoId)
                if (terreno != null) {
                    displaySelectedTerreno(terreno)
                }
            } else {
                showTerrenoSelectionDialog()
            }

            val catalogo = db.assetDao().getCatalogoCultivos()
            val sugerencias = db.assetDao().getMostUsedCatalogoCultivos()
            setupSugerencias(sugerencias)

            val nombresCultivos = catalogo.map { it.nombre }
            val adapterCultivo = ArrayAdapter(this@RegisterCultivoActivity, android.R.layout.simple_dropdown_item_1line, nombresCultivos)
            binding.autoCompleteCultivo.setAdapter(adapterCultivo)
            
            binding.autoCompleteCultivo.setOnItemClickListener { _, _, position, _ ->
                val nombreSeleccionado = binding.autoCompleteCultivo.adapter.getItem(position) as String
                seleccionarCultivoPorNombre(nombreSeleccionado)
            }
        }
    }

    private fun displaySelectedTerreno(terreno: TerrenoEntity) {
        selectedTerreno = terreno
        binding.terrenoInfoContainer.visibility = View.VISIBLE
        binding.selectorTerrenoContainer.visibility = View.GONE

        lifecycleScope.launch {
            val cultivosActivos = db.assetDao().getCultivosActivosByTerreno(terreno.id)
            val areaOcupada = cultivosActivos.sumOf { it.area_destinada ?: 0.0 }
            val areaDisponible = terreno.area_hectareas - areaOcupada

            binding.tvSelectedTerrenoName.text = terreno.nombre
            binding.tvSelectedTerrenoArea.text = "${String.format("%.2f", areaDisponible)} Hectáreas Libres"
            binding.tvSelectedTerrenoUbicacion.text = terreno.direccion_referencia ?: terreno.ubicacion_geo ?: "Ubicación no especificada"
            binding.badgeStatus.text = terreno.tipo_tenencia.replaceFirstChar { it.uppercase() }
        }
    }

    private fun showDatePicker(isPlanificada: Boolean) {
        val cal = if (isPlanificada) calendarPlanificada else calendarSiembra
        DatePickerDialog(this, { _, year, month, day ->
            cal.set(year, month, day)
            val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            if (isPlanificada) {
                binding.etFechaPlanificada.setText(format.format(cal.time))
                hasSelectedPlanificada = true
            } else {
                binding.etFechaSiembra.setText(format.format(cal.time))
                hasSelectedSiembra = true
            }
        }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH)).show()
    }

    private fun showTerrenoSelectionDialog() {
        val dialog = com.google.android.material.bottomsheet.BottomSheetDialog(this)
        val dialogBinding = com.sigcpa.agrosys.databinding.DialogSelectTerrenoBinding.inflate(layoutInflater)
        dialog.setContentView(dialogBinding.root)
        dialog.setCancelable(true) 

        dialog.setOnCancelListener {
            if (selectedTerreno == null) {
                finish() // Si cancela y no hay terreno, cerramos
            }
        }

        lifecycleScope.launch {
            val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
            val userId = sharedPref.getInt("USER_ID", -1)
            val agricultor = db.userDao().getAgricultorByUserId(userId) ?: return@launch
            val terrenos = db.assetDao().getTerrenosByAgricultor(agricultor.id)

            if (terrenos.isEmpty()) {
                Toast.makeText(this@RegisterCultivoActivity, "No tienes terrenos. Registra uno primero.", Toast.LENGTH_LONG).show()
                finish()
                return@launch
            }

            var tempSelected: TerrenoEntity? = null
            
            val adapter = TerrenoSelectorAdapter(terrenos) { terreno ->
                tempSelected = terreno
                dialogBinding.btnConfirmarSeleccion.isEnabled = true
            }
            
            dialogBinding.rvTerrenosSelector.layoutManager = LinearLayoutManager(this@RegisterCultivoActivity)
            dialogBinding.rvTerrenosSelector.adapter = adapter

            dialogBinding.btnConfirmarSeleccion.setOnClickListener {
                tempSelected?.let {
                    displaySelectedTerreno(it)
                    dialog.dismiss()
                }
            }

            dialogBinding.btnCloseDialog.setOnClickListener {
                dialog.cancel()
            }

            dialogBinding.btnCancelarSeleccion.setOnClickListener {
                dialog.cancel()
            }
        }

        dialog.show()
    }

    private fun updateStatus(status: String) {
        selectedStatus = status
        binding.btnStatusPlanificado.alpha = if (status == "planificado") 1.0f else 0.5f
        binding.btnStatusActivo.alpha = if (status == "activo") 1.0f else 0.5f
    }

    private fun saveCultivo() {
        val variedad = binding.etVariedad.text.toString().trim()
        val areaStr = binding.etAreaDestinada.text.toString().trim()
        val fechaSiembraStr = binding.etFechaSiembra.text.toString().trim()
        val fechaPlanificadaStr = binding.etFechaPlanificada.text.toString().trim()
        val observaciones = binding.etObservaciones.text.toString().trim()
        
        if (selectedTerreno == null) {
            Toast.makeText(this, "⚠️ El terreno es obligatorio", Toast.LENGTH_SHORT).show()
            return
        }
        if (selectedCatalogo == null) {
            Toast.makeText(this, "⚠️ Selecciona un tipo de cultivo", Toast.LENGTH_SHORT).show()
            return
        }
        if (variedad.isEmpty()) {
            Toast.makeText(this, "⚠️ La variedad es obligatoria", Toast.LENGTH_SHORT).show()
            return
        }
        if (fechaPlanificadaStr.isEmpty()) {
            Toast.makeText(this, "⚠️ La fecha planificada es obligatoria", Toast.LENGTH_SHORT).show()
            return
        }
        if (areaStr.isEmpty()) {
            Toast.makeText(this, "⚠️ El área es obligatoria", Toast.LENGTH_SHORT).show()
            return
        }

        val areaNueva = areaStr.toDoubleOrNull() ?: 0.0
        if (areaNueva <= 0) {
            Toast.makeText(this, "⚠️ El área debe ser mayor a 0", Toast.LENGTH_SHORT).show()
            return
        }

        lifecycleScope.launch {
            val terreno = selectedTerreno!!
            val cultivosActivos = db.assetDao().getCultivosActivosByTerreno(terreno.id)
            val areaOcupada = cultivosActivos.sumOf { it.area_destinada ?: 0.0 }
            val areaDisponible = terreno.area_hectareas - areaOcupada

            if (areaNueva > areaDisponible) {
                Toast.makeText(this@RegisterCultivoActivity, "❌ Área insuficiente. Disponible: ${String.format("%.2f", areaDisponible)} ha", Toast.LENGTH_LONG).show()
                return@launch
            }

            // Si no se seleccionó fecha de siembra real, usamos la planificada o el momento actual para evitar nulos si el esquema lo requiere
            // pero según el pedido, la de siembra es la que manda el orden.
            val finalFechaSiembra = if (hasSelectedSiembra) calendarSiembra.timeInMillis / 1000 else calendarPlanificada.timeInMillis / 1000

            val nuevoCultivo = CultivoEntity(
                terreno_id = terreno.id,
                catalogo_cultivo_id = selectedCatalogo!!.id,
                nombre_lote = "${selectedCatalogo?.nombre} - $variedad",
                fecha_planificada = calendarPlanificada.timeInMillis / 1000,
                fecha_siembra = finalFechaSiembra,
                fecha_finalizacion = null,
                estado = selectedStatus,
                area_destinada = areaNueva,
                variedad = variedad,
                plantas_estimadas = 0,
                observaciones = observaciones
            )

            val id = db.assetDao().insertCultivo(nuevoCultivo)
            if (id > 0) {
                Toast.makeText(this@RegisterCultivoActivity, "✅ Cultivo registrado", Toast.LENGTH_LONG).show()
                finish()
            }
        }
    }
}
