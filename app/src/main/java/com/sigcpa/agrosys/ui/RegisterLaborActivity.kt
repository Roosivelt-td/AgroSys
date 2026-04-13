package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.content.res.ColorStateList
import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.databinding.ActivityRegisterLaborBinding
import com.sigcpa.agrosys.databinding.ItemInsumoFormBinding
import com.google.android.material.card.MaterialCardView
import androidx.core.widget.doOnTextChanged
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

data class LaborTempRecord(
    val laborId: Int,
    val laborName: String,
    val fecha: Long,
    val costoMO: Double,
    val costoMaq: Double,
    val observaciones: String,
    val insumos: List<InsumoTempRecord> = emptyList()
)

data class InsumoTempRecord(
    val categoria: String,
    val nombre: String,
    val cantidad: Double,
    val unidad: String,
    val precioUnitario: Double,
    val proveedor: String
)

class RegisterLaborActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterLaborBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var selectedCultivoId: Int = -1
    private var selectedLaborId: Int = -1
    private var selectedLaborName: String = ""
    private val calendar = Calendar.getInstance()
    private val insumoViews = mutableListOf<ItemInsumoFormBinding>()
    
    private val categoriasInsumos = listOf("Semillas", "Fertilizantes", "Pesticidas", "Herbicidas", "Comida", "Herramientas", "Otros")
    private val laboresAgregadas = mutableListOf<LaborTempRecord>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterLaborBinding.inflate(layoutInflater)
        setContentView(binding.root)

        selectedCultivoId = intent.getIntExtra("CULTIVO_ID", -1)

        setupUI()
        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        
        binding.etFechaRealizacion.setOnClickListener { showDatePicker() }
        val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        binding.etFechaRealizacion.setText(sdf.format(calendar.time))

        binding.rgTipoJornal.setOnCheckedChangeListener { _, checkedId ->
            if (checkedId == R.id.rbUnificado) {
                binding.llManoObraUnificado.visibility = View.VISIBLE
                binding.llManoObraDiferenciado.visibility = View.GONE
            } else {
                binding.llManoObraUnificado.visibility = View.GONE
                binding.llManoObraDiferenciado.visibility = View.VISIBLE
            }
            calculateSessionTotal()
        }

        binding.btnAgregarInsumo.setOnClickListener { addInsumoRow() }
        
        binding.btnSaveLabor.setOnClickListener {
            if (validateCurrentLabor()) {
                addToTempList()
                saveAllToDatabase()
            }
        }

        val updateListener = { _: CharSequence?, _: Int, _: Int, _: Int -> calculateSessionTotal() }
        binding.etPeonesUnif.doOnTextChanged(updateListener)
        binding.etDiasUnif.doOnTextChanged(updateListener)
        binding.etCostoJornalUnif.doOnTextChanged(updateListener)
        binding.etHombresCant.doOnTextChanged(updateListener)
        binding.etHombresDias.doOnTextChanged(updateListener)
        binding.etMujeresCant.doOnTextChanged(updateListener)
        binding.etMujeresDias.doOnTextChanged(updateListener)
        binding.etJornalHombre.doOnTextChanged(updateListener)
        binding.etJornalMujer.doOnTextChanged(updateListener)
        binding.etCostoMaquinaria.doOnTextChanged(updateListener)
    }

    private fun validateCurrentLabor(): Boolean {
        if (selectedLaborId == -1) {
            Toast.makeText(this, "⚠️ Selecciona un tipo de labor", Toast.LENGTH_SHORT).show()
            return false
        }

        // Validación según tipo de labor
        when (selectedLaborId) {
            1 -> { // Preparación: Requiere costo maquinaria > 0
                val costMaq = binding.etCostoMaquinaria.text.toString().toDoubleOrNull() ?: 0.0
                if (costMaq <= 0) {
                    Toast.makeText(this, "⚠️ La preparación requiere ingresar un costo de maquinaria", Toast.LENGTH_SHORT).show()
                    binding.etCostoMaquinaria.requestFocus()
                    return false
                }
            }
            2 -> { // Siembra: Requiere insumos (Semillas)
                if (insumoViews.isEmpty()) {
                    Toast.makeText(this, "⚠️ La siembra requiere registrar insumos (Semillas)", Toast.LENGTH_SHORT).show()
                    return false
                }
                for (b in insumoViews) {
                    if (b.etNombreInsumo.text.isEmpty() || (b.etInsumoCantidad.text.toString().toDoubleOrNull() ?: 0.0) <= 0) {
                        Toast.makeText(this, "⚠️ Completa los datos de los insumos", Toast.LENGTH_SHORT).show()
                        return false
                    }
                }
            }
        }

        // Validación general de Mano de Obra para labores que la requieren (casi todas excepto preparación pura a veces)
        if (binding.containerManoObra.visibility == View.VISIBLE) {
            if (binding.rbUnificado.isChecked) {
                val peones = binding.etPeonesUnif.text.toString().toDoubleOrNull() ?: 0.0
                val dias = binding.etDiasUnif.text.toString().toDoubleOrNull() ?: 0.0
                if (peones <= 0 || dias <= 0) {
                    Toast.makeText(this, "⚠️ Ingresa cantidad de peones y días de trabajo", Toast.LENGTH_SHORT).show()
                    return false
                }
            }
        }

        return true
    }

    private fun setupLaboresClickListeners(yaPreparo: Boolean, yaSembro: Boolean) {
        val laboresMap = mapOf(
            binding.cardPreparacion to 1,
            binding.cardSiembra to 2,
            binding.cardRiego to 3,
            binding.cardFumigacion to 4,
            binding.cardAporque to 5,
            binding.cardDeshierbe to 6,
            binding.cardFertilizacion to 7,
            binding.cardCosecha to 8,
            binding.cardOtro to 9
        )

        laboresMap.forEach { (card, id) ->
            val isEnabled = when (id) {
                1, 9 -> true
                2 -> yaPreparo
                else -> yaSembro
            }

            card.isEnabled = isEnabled
            card.alpha = if (isEnabled) 1.0f else 0.3f

            card.setOnClickListener {
                if (!isEnabled) {
                    val msg = if (id == 2) "debes preparar el terreno" else "debes registrar la siembra"
                    Toast.makeText(this, "⚠️ Bloqueado: Primero $msg", Toast.LENGTH_SHORT).show()
                    return@setOnClickListener
                }

                if (selectedLaborId != -1 && selectedLaborId != id) {
                    if (validateCurrentLabor()) {
                        addToTempList()
                    } else {
                        return@setOnClickListener // No permite cambiar si la actual es inválida
                    }
                }

                selectedLaborId = id
                resetGridSelection()
                
                card.setStrokeColor(ColorStateList.valueOf(Color.parseColor("#16a34a")))
                card.setCardBackgroundColor(ColorStateList.valueOf(Color.parseColor("#E8F5E9")))
                
                selectedLaborName = (card.getChildAt(0) as? LinearLayout)?.let { 
                    (it.getChildAt(1) as? TextView)?.text.toString() 
                } ?: ""
                
                binding.tvHeaderSubtitle.text = "Configurando: $selectedLaborName"
                binding.formContainer.visibility = View.VISIBLE
                updateFormVisibility(id)
                resetCurrentForm()
            }
        }
    }

    private fun updateFormVisibility(id: Int) {
        val showMaq = id in listOf(1, 2, 8, 9)
        binding.containerMaquinaria.visibility = if (showMaq) View.VISIBLE else View.GONE
        val showMO = id != 1
        binding.containerManoObra.visibility = if (showMO) View.VISIBLE else View.GONE
        val showInsumos = id != 1
        binding.cardInsumos.visibility = if (showInsumos) View.VISIBLE else View.GONE
    }

    private fun resetCurrentForm() {
        binding.etObservaciones.setText("")
        binding.etCostoMaquinaria.setText("0")
        binding.etPeonesUnif.setText("0")
        binding.etDiasUnif.setText("0")
        binding.insumosContainer.removeAllViews()
        insumoViews.clear()
        binding.tvEmptyInsumos.visibility = View.VISIBLE
    }

    private fun addToTempList() {
        if (selectedLaborId == -1) return

        val costMO = calculateCurrentMOCost()
        val costMaq = if (binding.containerMaquinaria.visibility == View.VISIBLE) 
            binding.etCostoMaquinaria.text.toString().toDoubleOrNull() ?: 0.0 else 0.0
        
        val tempInsumos = insumoViews.map { b ->
            InsumoTempRecord(
                categoria = b.spinnerCategoriaInsumo.selectedItem.toString(),
                nombre = b.etNombreInsumo.text.toString(),
                cantidad = b.etInsumoCantidad.text.toString().toDoubleOrNull() ?: 0.0,
                unidad = b.etUnidadMedida.text.toString(),
                precioUnitario = b.etInsumoPrecio.text.toString().toDoubleOrNull() ?: 0.0,
                proveedor = if (b.cbTieneProveedor.isChecked) b.etNombreProveedor.text.toString() else "Anónimo"
            )
        }.filter { it.nombre.isNotEmpty() && it.cantidad > 0 }

        laboresAgregadas.add(LaborTempRecord(
            selectedLaborId,
            selectedLaborName,
            calendar.timeInMillis / 1000,
            costMO,
            costMaq,
            binding.etObservaciones.text.toString(),
            tempInsumos
        ))

        updateResumenUI()
    }

    private fun updateResumenUI() {
        val nombres = laboresAgregadas.joinToString(", ") { it.laborName }
        binding.tvLaboresResumen.text = if (laboresAgregadas.isNotEmpty()) {
            "Labores listas: $nombres"
        } else {
            "No hay labores listas"
        }
        calculateSessionTotal()
    }

    private fun calculateCurrentMOCost(): Double {
        if (binding.containerManoObra.visibility == View.GONE) return 0.0
        return if (binding.rbUnificado.isChecked) {
            val p = binding.etPeonesUnif.text.toString().toDoubleOrNull() ?: 0.0
            val d = binding.etDiasUnif.text.toString().toDoubleOrNull() ?: 0.0
            val j = binding.etCostoJornalUnif.text.toString().toDoubleOrNull() ?: 0.0
            p * d * j
        } else {
            val hc = binding.etHombresCant.text.toString().toDoubleOrNull() ?: 0.0
            val hd = binding.etHombresDias.text.toString().toDoubleOrNull() ?: 0.0
            val hj = binding.etJornalHombre.text.toString().toDoubleOrNull() ?: 0.0
            val mc = binding.etMujeresCant.text.toString().toDoubleOrNull() ?: 0.0
            val md = binding.etMujeresDias.text.toString().toDoubleOrNull() ?: 0.0
            val mj = binding.etJornalMujer.text.toString().toDoubleOrNull() ?: 0.0
            (hc * hd * hj) + (mc * md * mj)
        }
    }

    private fun calculateSessionTotal() {
        var total = laboresAgregadas.sumOf { it.costoMO + it.costoMaq + it.insumos.sumOf { ins -> ins.cantidad * ins.precioUnitario } }
        
        if (selectedLaborId != -1) {
            total += calculateCurrentMOCost()
            total += if (binding.containerMaquinaria.visibility == View.VISIBLE) 
                binding.etCostoMaquinaria.text.toString().toDoubleOrNull() ?: 0.0 else 0.0
            insumoViews.forEach { b ->
                val c = b.etInsumoCantidad.text.toString().toDoubleOrNull() ?: 0.0
                val p = b.etInsumoPrecio.text.toString().toDoubleOrNull() ?: 0.0
                total += (c * p)
            }
        }
        
        binding.tvCostoTotalPreview.text = "S/ ${String.format("%.2f", total)}"
    }

    private fun resetGridSelection() {
        val cards = listOf(
            binding.cardPreparacion, binding.cardSiembra, binding.cardRiego,
            binding.cardFumigacion, binding.cardAporque, binding.cardDeshierbe,
            binding.cardFertilizacion, binding.cardCosecha, binding.cardOtro
        )
        cards.forEach { card ->
            card.setStrokeColor(ColorStateList.valueOf(Color.parseColor("#E5E7EB")))
            card.setCardBackgroundColor(ColorStateList.valueOf(Color.parseColor("#FFFFFF")))
        }
    }

    private fun loadData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val agricultor = db.userDao().getAgricultorByUserId(userId) ?: return@launch
            val cultivos = db.assetDao().getCultivosByAgricultor(agricultor.id)
            
            if (cultivos.isEmpty()) {
                binding.btnSaveLabor.isEnabled = false
                binding.btnSaveLabor.alpha = 0.5f
                binding.btnSaveLabor.text = "Requiere Cultivo Activo"
                Toast.makeText(this@RegisterLaborActivity, "⚠️ Registra un cultivo primero", Toast.LENGTH_LONG).show()
                return@launch
            }

            val adapter = ArrayAdapter(this@RegisterLaborActivity, android.R.layout.simple_spinner_item, cultivos.map { it.nombre_lote ?: "Cultivo #${it.id}" })
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.spinnerCultivos.adapter = adapter
            
            binding.spinnerCultivos.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
                override fun onItemSelected(p0: AdapterView<*>?, p1: View?, pos: Int, p3: Long) {
                    selectedCultivoId = cultivos[pos].id
                    validateSequence(selectedCultivoId)
                }
                override fun onNothingSelected(p0: AdapterView<*>?) {}
            }
        }
    }

    private fun validateSequence(cultivoId: Int) {
        lifecycleScope.launch {
            val realizadas = db.assetDao().getLaboresByCultivo(cultivoId)
            val yaPreparo = realizadas.any { it.catalogo_labor_id == 1 }
            val yaSembro = realizadas.any { it.catalogo_labor_id == 2 }
            setupLaboresClickListeners(yaPreparo, yaSembro)
        }
    }

    private fun addInsumoRow() {
        binding.tvEmptyInsumos.visibility = View.GONE
        val insumoBinding = ItemInsumoFormBinding.inflate(LayoutInflater.from(this), binding.insumosContainer, false)
        val catAdapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, categoriasInsumos)
        catAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        insumoBinding.spinnerCategoriaInsumo.adapter = catAdapter
        insumoBinding.cbTieneProveedor.setOnCheckedChangeListener { _, isChecked ->
            insumoBinding.llProveedorContainer.visibility = if (isChecked) View.VISIBLE else View.GONE
        }
        insumoBinding.btnDeleteInsumo.setOnClickListener {
            binding.insumosContainer.removeView(insumoBinding.root)
            insumoViews.remove(insumoBinding)
            if (insumoViews.isEmpty()) binding.tvEmptyInsumos.visibility = View.VISIBLE
            calculateSessionTotal()
        }
        insumoBinding.etInsumoCantidad.doOnTextChanged { _, _, _, _ -> calculateSessionTotal() }
        insumoBinding.etInsumoPrecio.doOnTextChanged { _, _, _, _ -> calculateSessionTotal() }
        insumoViews.add(insumoBinding)
        binding.insumosContainer.addView(insumoBinding.root)
    }

    private fun showDatePicker() {
        DatePickerDialog(this, { _, y, m, d ->
            calendar.set(y, m, d)
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            binding.etFechaRealizacion.setText(sdf.format(calendar.time))
        }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH)).show()
    }

    private fun saveAllToDatabase() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            laboresAgregadas.forEach { record ->
                val labor = LaborRealizadaEntity(
                    cultivo_id = selectedCultivoId,
                    catalogo_labor_id = record.laborId,
                    fecha_realizacion = record.fecha,
                    costo_mano_obra_total = record.costoMO,
                    costo_maquinaria_total = record.costoMaq,
                    observaciones = record.observaciones,
                    creado_por_usuario_id = userId
                )

                val laborId = db.assetDao().insertLabor(labor).toInt()
                
                record.insumos.forEach { ins ->
                    val catInsumoId = 1 
                    db.assetDao().insertInsumoUsado(InsumoUsadoEntity(
                        labor_id = laborId,
                        catalogo_insumo_id = catInsumoId,
                        proveedor_id = null,
                        cantidad = ins.cantidad,
                        costo_unitario = ins.precioUnitario
                    ))
                }
            }

            Toast.makeText(this@RegisterLaborActivity, "✅ ${laboresAgregadas.size} labores registradas", Toast.LENGTH_LONG).show()
            finish()
        }
    }
}
