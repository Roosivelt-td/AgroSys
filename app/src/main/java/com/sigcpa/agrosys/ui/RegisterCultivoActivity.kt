package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.os.Bundle
import android.view.View
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
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
    private val calendar = Calendar.getInstance()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterCultivoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupUI()
        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        binding.etFechaSiembra.setOnClickListener { showDatePicker() }
        
        binding.btnStatusPlanificado.setOnClickListener { updateStatus("planificado") }
        binding.btnStatusActivo.setOnClickListener { updateStatus("activo") }
        updateStatus("activo")

        binding.btnSaveCultivo.setOnClickListener { saveCultivo() }

        // Configurar clics en sugerencias rápidas
        val sugerencias = mapOf(
            binding.btnSugMaiz to "Maíz",
            binding.btnSugPapa to "Papa",
            binding.btnSugPalta to "Palta",
            binding.btnSugCafe to "Café",
            binding.btnSugTomate to "Tomate",
            binding.btnSugLimon to "Limón",
            binding.btnSugCacao to "Cacao",
            binding.btnSugArroz to "Arroz"
        )

        sugerencias.forEach { (btn, nombre) ->
            btn.setOnClickListener {
                binding.autoCompleteCultivo.setText(nombre, false)
                seleccionarCultivoPorNombre(nombre)
            }
        }
    }

    private fun seleccionarCultivoPorNombre(nombre: String) {
        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoCultivos()
            selectedCatalogo = catalogo.find { it.nombre.equals(nombre, ignoreCase = true) }
            selectedCatalogo?.let {
                binding.tvCicloInfo.visibility = View.VISIBLE
                val ciclo = if (it.tipo_ciclo == "perenne") "Perenne" else "${it.dias_a_cosecha_promedio ?: "--"} días"
                binding.tvCicloInfo.text = "Ciclo productivo estimado: $ciclo"
            }
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
                    selectedTerreno = terreno
                    binding.terrenoInfoContainer.visibility = View.VISIBLE
                    binding.selectorTerrenoContainer.visibility = View.GONE
                    
                    val cultivosActivos = db.assetDao().getCultivosActivosByTerreno(terreno.id)
                    val areaOcupada = cultivosActivos.sumOf { it.area_destinada ?: 0.0 }
                    val areaDisponible = terreno.area_hectareas - areaOcupada

                    binding.tvSelectedTerrenoName.text = terreno.nombre
                    binding.tvSelectedTerrenoDetail.text = "Área Total: ${terreno.area_hectareas} ha · Disponible: ${String.format("%.2f", areaDisponible)} ha"
                }
            } else {
                val terrenos = db.assetDao().getTerrenosByAgricultor(agricultor.id)
                if (terrenos.isEmpty()) {
                    binding.btnSaveCultivo.isEnabled = false
                    binding.btnSaveCultivo.alpha = 0.5f
                    binding.tvSelectedTerrenoName.text = "Sin terrenos registrados"
                    binding.tvSelectedTerrenoDetail.text = "Debes registrar un terreno primero."
                } else {
                    binding.terrenoInfoContainer.visibility = View.GONE
                    binding.selectorTerrenoContainer.visibility = View.VISIBLE
                    
                    val adapter = ArrayAdapter(this@RegisterCultivoActivity, android.R.layout.simple_spinner_item, terrenos.map { it.nombre })
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    binding.spinnerTerrenos.adapter = adapter
                    
                    binding.spinnerTerrenos.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
                        override fun onItemSelected(p0: AdapterView<*>?, p1: View?, pos: Int, p3: Long) {
                            selectedTerreno = terrenos[pos]
                        }
                        override fun onNothingSelected(p0: AdapterView<*>?) {}
                    }
                }
            }

            val catalogo = db.assetDao().getCatalogoCultivos()
            val nombresCultivos = catalogo.map { it.nombre }
            val adapterCultivo = ArrayAdapter(this@RegisterCultivoActivity, android.R.layout.simple_dropdown_item_1line, nombresCultivos)
            binding.autoCompleteCultivo.setAdapter(adapterCultivo)
            
            binding.autoCompleteCultivo.setOnItemClickListener { _, _, position, _ ->
                val nombreSeleccionado = binding.autoCompleteCultivo.adapter.getItem(position) as String
                seleccionarCultivoPorNombre(nombreSeleccionado)
            }
        }
    }

    private fun showDatePicker() {
        DatePickerDialog(this, { _, year, month, day ->
            calendar.set(year, month, day)
            val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            binding.etFechaSiembra.setText(format.format(calendar.time))
        }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH)).show()
    }

    private fun updateStatus(status: String) {
        selectedStatus = status
        binding.btnStatusPlanificado.alpha = if (status == "planificado") 1.0f else 0.5f
        binding.btnStatusActivo.alpha = if (status == "activo") 1.0f else 0.5f
    }

    private fun saveCultivo() {
        val variedad = binding.etVariedad.text.toString().trim()
        val areaStr = binding.etAreaDestinada.text.toString().trim()
        val fechaStr = binding.etFechaSiembra.text.toString().trim()
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
        if (fechaStr.isEmpty()) {
            Toast.makeText(this, "⚠️ La fecha es obligatoria", Toast.LENGTH_SHORT).show()
            return
        }
        if (areaStr.isEmpty()) {
            Toast.makeText(this, "⚠️ El área es obligatoria", Toast.LENGTH_SHORT).show()
            return
        }
        if (observaciones.isEmpty()) {
            Toast.makeText(this, "⚠️ Las observaciones son obligatorias", Toast.LENGTH_SHORT).show()
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

            val nuevoCultivo = CultivoEntity(
                terreno_id = terreno.id,
                catalogo_cultivo_id = selectedCatalogo!!.id,
                nombre_lote = "${selectedCatalogo?.nombre} - $variedad",
                fecha_siembra = calendar.timeInMillis / 1000,
                fecha_finalizacion = null,
                estado = selectedStatus,
                area_destinada = areaNueva,
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
