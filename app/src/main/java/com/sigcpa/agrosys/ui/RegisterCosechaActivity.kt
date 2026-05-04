package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.core.content.FileProvider
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CosechaEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterCosechaBinding
import com.sigcpa.agrosys.util.FileUtils
import kotlinx.coroutines.launch
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class RegisterCosechaActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterCosechaBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var currentCultivoId: Int = -1
    private var laborId: Int = -1
    private var cosechaId: Int = -1
    private var currentCultivo: CultivoEntity? = null
    private var selectedCalidad: String? = null
    private var selectedUnidad: String = "kg"
    private val calendar = Calendar.getInstance()

    private var selectedImageUri: Uri? = null
    private var cameraImageUri: Uri? = null

    private val pickImageLauncher = registerForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri?.let {
            selectedImageUri = it
            showSelectedImage(it)
        }
    }

    private val takePhotoLauncher = registerForActivityResult(ActivityResultContracts.TakePicture()) { success ->
        if (success) {
            cameraImageUri?.let {
                selectedImageUri = it
                showSelectedImage(it)
            }
        }
    }

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
        laborId = intent.getIntExtra("LABOR_ID", -1)
        cosechaId = intent.getIntExtra("COSECHA_ID", -1)
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
        setupUnidadButtons()
        setupPhotoLogic()

        binding.btnSaveCosecha.setOnClickListener { saveCosecha() }
    }

    private fun setupPhotoLogic() {
        binding.cardFotoCosecha.setOnClickListener {
            val options = arrayOf("Tomar Foto", "Elegir de Galería")
            AlertDialog.Builder(this)
                .setTitle("Evidencia de Cosecha")
                .setItems(options) { _, which ->
                    when (which) {
                        0 -> {
                            val photoFile = File(getExternalFilesDir(android.os.Environment.DIRECTORY_PICTURES), "COSECHA_${System.currentTimeMillis()}.jpg")
                            cameraImageUri = FileProvider.getUriForFile(this, "${packageName}.fileprovider", photoFile)
                            takePhotoLauncher.launch(cameraImageUri!!)
                        }
                        1 -> pickImageLauncher.launch("image/*")
                    }
                }
                .show()
        }

        binding.btnRemoveFoto.setOnClickListener {
            selectedImageUri = null
            binding.ivFotoCosecha.setImageResource(R.drawable.uploap)
            binding.ivFotoCosecha.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            val padding = (64 * resources.displayMetrics.density).toInt()
            binding.ivFotoCosecha.setPadding(padding, padding, padding, padding)
            binding.btnRemoveFoto.visibility = View.GONE
        }
    }

    private fun showSelectedImage(uri: Uri) {
        binding.ivFotoCosecha.scaleType = android.widget.ImageView.ScaleType.CENTER_CROP
        Glide.with(this)
            .load(uri)
            .placeholder(R.drawable.uploap)
            .into(binding.ivFotoCosecha)
        binding.ivFotoCosecha.setPadding(0, 0, 0, 0)
        binding.btnRemoveFoto.visibility = View.VISIBLE
    }

    private fun generateDefaultLote() {
        val dateStr = SimpleDateFormat("yyyyMMdd", Locale.getDefault()).format(Date())
        binding.etLoteCodigo.setText("LOTE-$currentCultivoId-$dateStr")
    }

    private fun setupUnidadButtons() {
        val btns = mapOf(
            "kg" to binding.btnUnitKg,
            "sacos" to binding.btnUnitSacos,
            "unidades" to binding.btnUnitUnidades,
            "cajas" to binding.btnUnitCajas,
            "jabas" to binding.btnUnitJabas,
            "otros" to binding.btnUnitOtros
        )

        btns.forEach { (unidad, btn) ->
            btn.setOnClickListener {
                selectedUnidad = unidad
                binding.tvUnitLabel.text = unidad
                btns.values.forEach { 
                    it.backgroundTintList = getColorStateList(android.R.color.transparent)
                    it.setTextColor(getColor(android.R.color.darker_gray))
                    it.strokeColor = getColorStateList(com.sigcpa.agrosys.R.color.green_100)
                }
                btn.backgroundTintList = getColorStateList(com.sigcpa.agrosys.R.color.green_primary)
                btn.setTextColor(getColor(android.R.color.white))
                btn.strokeColor = getColorStateList(android.R.color.transparent)
            }
        }
        
        // Seleccionar kg por defecto
        binding.btnUnitKg.performClick()
    }

    private fun setupCalidadButtons() {
        val btns = mapOf(
            "Extra" to binding.btnCalidadExtra,
            "Primera" to binding.btnCalidadPrimera,
            "Segunda" to binding.btnCalidadSegunda,
            "Tercera" to binding.btnCalidadTercera
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

            // Si ya existe una cosecha para esta labor, cargarla para editar
            val cosechaExistente = when {
                laborId != -1 -> db.assetDao().getCosechasByCultivo(currentCultivoId).find { it.labor_id == laborId }
                cosechaId != -1 -> db.assetDao().getCosechaById(cosechaId)
                else -> null
            }

            cosechaExistente?.let { cos ->
                cosechaId = cos.id
                binding.etCantidadKg.setText(cos.cantidad_kg.toString())
                binding.etLoteCodigo.setText(cos.lote_codigo)
                binding.etCostoOperativo.setText(cos.costo_operativo_cosecha.toString())
                binding.etObservaciones.setText(cos.observaciones)
                selectedCalidad = cos.calidad
                selectedUnidad = cos.unidad_medida ?: "kg"
                setupCalidadButtons() // Refrescar visualmente
                setupUnidadButtons() // Refrescar visualmente
                
                if (cos.foto_path != null) {
                    selectedImageUri = Uri.parse(cos.foto_path)
                    showSelectedImage(selectedImageUri!!)
                }
                
                // Si estamos editando una cosecha existente, actualizar el calendario
                calendar.timeInMillis = cos.fecha_cosecha * 1000
                val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
                binding.etFechaCosecha.setText(sdf.format(calendar.time))
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
            AlertDialog.Builder(this)
                .setTitle("⚠️ Campo Obligatorio")
                .setMessage("No puedes guardar el registro sin ingresar una cantidad cosechada. Por favor, ingresa el peso o cantidad obtenida.")
                .setPositiveButton("Entendido", null)
                .show()
            return
        }

        val cantidad = cantidadStr.toDoubleOrNull() ?: 0.0
        val costoOp = costoOpStr.toDoubleOrNull() ?: 0.0

        lifecycleScope.launch {
            var finalFotoPath: String? = selectedImageUri?.toString()
            val sharedPref = getSharedPreferences("agrosys_prefs", android.content.Context.MODE_PRIVATE)
            val userId = sharedPref.getInt("USER_ID", -1)
            val userName = sharedPref.getString("USER_NAME", "user") ?: "user"

            selectedImageUri?.let { uri ->
                if (uri.scheme == "content" || uri.scheme == "file") {
                    val savedPath = FileUtils.saveImageLocally(
                        this@RegisterCosechaActivity,
                        uri,
                        userId,
                        userName,
                        "cosecha",
                        "cosecha_${System.currentTimeMillis()}.jpg"
                    )
                    finalFotoPath = savedPath
                }
            }

            val nuevaCosecha = CosechaEntity(
                cultivo_id = currentCultivoId,
                labor_id = if (laborId != -1) laborId else null,
                fecha_cosecha = calendar.timeInMillis / 1000,
                cantidad_kg = cantidad,
                unidad_medida = selectedUnidad,
                calidad = selectedCalidad,
                lote_codigo = loteCodigo,
                costo_operativo_cosecha = costoOp,
                observaciones = observaciones,
                foto_path = finalFotoPath,
                created_at = System.currentTimeMillis() / 1000,
                sincronizado = 0
            )

            val cosechaExistenteId = when {
                laborId != -1 -> db.assetDao().getCultivoById(currentCultivoId)?.let { db.assetDao().getCosechasByCultivo(currentCultivoId).find { it.labor_id == laborId }?.id }
                cosechaId != -1 -> cosechaId
                else -> null
            }
            
            val paraInsertar = if (cosechaExistenteId != null) {
                nuevaCosecha.copy(id = cosechaExistenteId)
            } else {
                nuevaCosecha
            }

            val id = db.assetDao().insertCosecha(paraInsertar)
            if (id > 0) {
                if (marcarCosechado && currentCultivo != null) {
                    val updated = currentCultivo!!.copy(estado = "cosechado", updated_at = System.currentTimeMillis() / 1000)
                    db.assetDao().updateCultivo(updated)
                    
                    // Actualizar fecha de vencimiento si el terreno es alquilado por campaña
                    val terreno = db.assetDao().getTerrenoById(currentCultivo!!.terreno_id)
                    if (terreno != null && terreno.tipo_tenencia == "alquilado" && terreno.alquiler_periodo == "campania") {
                        val updatedTerreno = terreno.copy(fecha_vencimiento_alquiler = nuevaCosecha.fecha_cosecha)
                        db.assetDao().updateTerreno(updatedTerreno)
                    }
                }
                Toast.makeText(this@RegisterCosechaActivity, "Cosecha registrada con éxito", Toast.LENGTH_LONG).show()
                finish()
            } else {
                Toast.makeText(this@RegisterCosechaActivity, "Error al registrar", Toast.LENGTH_SHORT).show()
            }
        }
    }
}
