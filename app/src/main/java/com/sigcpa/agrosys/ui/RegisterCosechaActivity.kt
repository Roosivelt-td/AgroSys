package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CosechaEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterCosechaBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class RegisterCosechaActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterCosechaBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var currentCultivoId: Int = -1
    private var currentCultivo: CultivoEntity? = null
    private var selectedCalidad: String? = null
    private val calendar = Calendar.getInstance()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        androidx.core.view.WindowInsetsControllerCompat(window, window.decorView).apply {
            isAppearanceLightStatusBars = false
            // Consistencia en barra de navegación
            window.navigationBarColor = android.graphics.Color.WHITE
            isAppearanceLightNavigationBars = true
        }

        binding = ActivityRegisterCosechaBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Manejo dinámico de Insets
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.root) { _, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            
            // Ajuste para el Header
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )

            // Ajuste para que el contenido no quede tras la barra de navegación
            binding.scrollContainer.setPadding(0, 0, 0, systemBars.bottom)

            insets
        }

        currentCultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        if (currentCultivoId == -1) {
            Toast.makeText(this, "Error: Cultivo no especificado", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        setupUI()
        loadData()
        generateDefaultLote()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        
        binding.etFechaCosecha.setOnClickListener { showDatePicker() }
        val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        binding.etFechaCosecha.setText(sdf.format(calendar.time))

        setupCalidadButtons()

        binding.btnSaveCosecha.setOnClickListener { saveCosecha() }
    }

    private fun generateDefaultLote() {
        val dateStr = SimpleDateFormat("yyyyMMdd", Locale.getDefault()).format(Date())
        binding.etLoteCodigo.setText("LOTE-$currentCultivoId-$dateStr")
    }

    private fun setupCalidadButtons() {
        val btns = mapOf(
            "Extra" to binding.btnCalidadExtra,
            "Primera" to binding.btnCalidadPrimera,
            "Segunda" to binding.btnCalidadSegunda,
            "Tercera" to binding.btnCalidadTercera,
            "Descarte" to binding.btnCalidadDescarte
        )

        btns.forEach { (calidad, btn) ->
            btn.setOnClickListener {
                selectedCalidad = calidad
                btns.values.forEach { 
                    it.backgroundTintList = getColorStateList(android.R.color.transparent)
                    it.setTextColor(getColor(android.R.color.darker_gray))
                }
                btn.backgroundTintList = getColorStateList(com.sigcpa.agrosys.R.color.black) // O color primario
                btn.setTextColor(getColor(android.R.color.white))
            }
        }
    }

    private fun loadData() {
        lifecycleScope.launch {
            val cultivo = db.assetDao().getCultivoById(currentCultivoId)
            if (cultivo != null) {
                currentCultivo = cultivo
                val terreno = db.assetDao().getTerrenoById(cultivo.terreno_id)
                binding.tvCultivoNombre.text = "${cultivo.nombre_lote} (${terreno?.nombre ?: "Sin terreno"})"
            }
        }
    }

    private fun showDatePicker() {
        DatePickerDialog(this, { _, year, month, day ->
            calendar.set(year, month, day)
            val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            binding.etFechaCosecha.setText(format.format(calendar.time))
        }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH)).show()
    }

    private fun saveCosecha() {
        val cantidadStr = binding.etCantidadKg.text.toString()
        val costoOpStr = binding.etCostoOperativo.text.toString()
        val loteCodigo = binding.etLoteCodigo.text.toString()
        val observaciones = binding.etObservaciones.text.toString()
        val marcarCosechado = binding.cbMarcarCosechado.isChecked

        if (cantidadStr.isEmpty()) {
            Toast.makeText(this, "Ingresa la cantidad cosechada", Toast.LENGTH_SHORT).show()
            return
        }

        val cantidad = cantidadStr.toDoubleOrNull() ?: 0.0
        val costoOp = costoOpStr.toDoubleOrNull() ?: 0.0

        val nuevaCosecha = CosechaEntity(
            cultivo_id = currentCultivoId,
            fecha_cosecha = calendar.timeInMillis / 1000,
            cantidad_kg = cantidad,
            calidad = selectedCalidad,
            lote_codigo = loteCodigo,
            costo_operativo_cosecha = costoOp,
            observaciones = observaciones,
            created_at = System.currentTimeMillis() / 1000,
            sincronizado = 0
        )

        lifecycleScope.launch {
            val id = db.assetDao().insertCosecha(nuevaCosecha)
            if (id > 0) {
                if (marcarCosechado && currentCultivo != null) {
                    val updated = currentCultivo!!.copy(estado = "cosechado", updated_at = System.currentTimeMillis() / 1000)
                    db.assetDao().updateCultivo(updated)
                }
                Toast.makeText(this@RegisterCosechaActivity, "Cosecha registrada con éxito", Toast.LENGTH_LONG).show()
                finish()
            } else {
                Toast.makeText(this@RegisterCosechaActivity, "Error al registrar", Toast.LENGTH_SHORT).show()
            }
        }
    }
}
