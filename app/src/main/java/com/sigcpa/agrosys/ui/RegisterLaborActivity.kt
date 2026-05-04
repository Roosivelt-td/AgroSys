package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.content.Intent
import android.graphics.Color
import android.net.Uri
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.google.android.material.bottomsheet.BottomSheetDialog
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.databinding.*
import com.canhub.cropper.CropImageContract
import com.canhub.cropper.CropImageContractOptions
import com.canhub.cropper.CropImageOptions
import com.canhub.cropper.CropImageView
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class RegisterLaborActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterLaborBinding
    private var cultivoRelacionado: CultivoEntity? = null
    private var terrenoRelacionado: TerrenoEntity? = null
    private var catalogoCultivo: CatalogoCultivoEntity? = null
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var selectedCultivoId: Int = -1
    private var currentLabor: LaborSessionItem? = null
    private val calendar = Calendar.getInstance()
    private var laborsHistory: List<LaborRealizadaEntity> = emptyList()
    private var referenceDateFormatted: String = "--/--/----"

    // Manejo de Fotos
    private var selectedImageUri: Uri? = null
    private var cameraImageUri: Uri? = null
    private var currentDialogBinding: DialogAddLaborBinding? = null

    private val cropImage = registerForActivityResult(CropImageContract()) { result ->
        if (result.isSuccessful) {
            selectedImageUri = result.uriContent
            refreshDialogFotoUI()
        } else {
            val exception = result.error
            Toast.makeText(this, "Error al recortar imagen: ${exception?.message}", Toast.LENGTH_SHORT).show()
        }
    }

    private val pickImageLauncher = registerForActivityResult(androidx.activity.result.contract.ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri?.let { startCrop(it) }
    }

    private val takePhotoLauncher = registerForActivityResult(androidx.activity.result.contract.ActivityResultContracts.TakePicture()) { success ->
        if (success) {
            cameraImageUri?.let { startCrop(it) }
        } else {
            selectedImageUri = null
        }
    }

    private fun startCrop(uri: Uri) {
        cropImage.launch(
            CropImageContractOptions(
                uri = uri,
                cropImageOptions = CropImageOptions(
                    guidelines = CropImageView.Guidelines.ON,
                    aspectRatioX = 16,
                    aspectRatioY = 9,
                    fixAspectRatio = true,
                    cropShape = CropImageView.CropShape.RECTANGLE,
                    outputCompressFormat = android.graphics.Bitmap.CompressFormat.JPEG,
                    outputCompressQuality = 80
                )
            )
        )
    }

    private fun refreshDialogFotoUI() {
        val dBinding = currentDialogBinding ?: return
        if (selectedImageUri != null) {
            dBinding.ivFotoLabor.imageTintList = null // Eliminar el tinte gris para ver la foto real
            com.bumptech.glide.Glide.with(this)
                .load(selectedImageUri)
                .placeholder(R.drawable.uploap)
                .centerCrop()
                .into(dBinding.ivFotoLabor)
            dBinding.ivFotoLabor.setPadding(0, 0, 0, 0)
            dBinding.btnRemoveFoto.visibility = View.VISIBLE
        } else {
            dBinding.ivFotoLabor.imageTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.parseColor("#D1D5DB"))
            dBinding.ivFotoLabor.setImageResource(R.drawable.uploap)
            dBinding.ivFotoLabor.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            dBinding.ivFotoLabor.setPadding(48, 48, 48, 48)
            dBinding.btnRemoveFoto.visibility = View.GONE
        }
    }

    // Estado de la secuencia
    private var yaPreparo = false
    private var yaSembro = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterLaborBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Branding y Insets
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).apply {
            isAppearanceLightStatusBars = false
            window.navigationBarColor = Color.WHITE
            isAppearanceLightNavigationBars = true
        }

        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                systemBars.top,
                binding.headerContainer.paddingRight,
                (24 * resources.displayMetrics.density).toInt()
            )

            // Ajuste para el contenedor de scroll
            binding.scrollContainer.setPadding(0, 0, 0, systemBars.bottom)
            
            insets
        }

        selectedCultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        binding.tvHeaderTitle.text = intent.getStringExtra("TERRENO_NOMBRE") ?: "Terreno"
        binding.tvHeaderSubtitle.text = intent.getStringExtra("CULTIVO_DETALLE") ?: "Detalle del cultivo"

        setupListeners()
        loadInitialState()
    }

    private fun setupListeners() {
        binding.btnBack.setOnClickListener { finish() }
        
        binding.btnViewDetalle.setOnClickListener {
            val intent = Intent(this, DetalleCultivoActivity::class.java)
            intent.putExtra("CULTIVO_ID", selectedCultivoId)
            startActivity(intent)
        }
        
        // Listeners para las tarjetas de labor
        binding.cardPreparacion.setOnClickListener { handleLaborClick(1, "Preparación") }
        binding.cardSiembra.setOnClickListener { handleLaborClick(2, "Siembra") }
        binding.cardRiego.setOnClickListener { handleLaborClick(3, "Riego") }
        binding.cardFumigacion.setOnClickListener { handleLaborClick(4, "Fumigación") }
        binding.cardAporque.setOnClickListener { handleLaborClick(5, "Aporque") }
        binding.cardDeshierbe.setOnClickListener { handleLaborClick(6, "Deshierbe") }
        binding.cardFertilizacion.setOnClickListener { handleLaborClick(7, "Abonado / Fertilización") }
        binding.cardPoda.setOnClickListener { handleLaborClick(8, "Poda") }
        binding.cardCosecha.setOnClickListener { handleLaborClick(9, "Cosecha") }

        binding.btnFinalizarRegistro.setOnClickListener { showConfirmationDialog() }
    }

    private fun loadInitialState() {
        refreshScreenState()
        // Manejar pre-selección automática si viene de otra pantalla
        val preSelectId = intent.getIntExtra("PRE_SELECT_LABOR_ID", -1)
        val preSelectNombre = intent.getStringExtra("LABOR_NOMBRE")
        if (preSelectId != -1 && preSelectNombre != null) {
            // Si viene de un resumen, abrimos directamente el formulario
            openLaborDialog(preSelectId, preSelectNombre)
        }
    }

    private fun refreshScreenState() {
        lifecycleScope.launch {
            // 1. Refrescar datos para asegurar que mostramos lo último y sincronizado
            val cultivo = db.assetDao().getCultivoById(selectedCultivoId) ?: return@launch
            cultivoRelacionado = cultivo
            terrenoRelacionado = db.assetDao().getTerrenoById(cultivo.terreno_id)
            catalogoCultivo = db.assetDao().getCatalogoCultivoById(cultivo.catalogo_cultivo_id)
            laborsHistory = db.assetDao().getLaboresByCultivo(selectedCultivoId)

            // 2. Cabecera (Sowing Date Priority: Real > Crop Entity > Planned)
            binding.tvHeaderLabel.text = "Registro de labores del cultivo,"
            binding.tvHeaderTitle.text = terrenoRelacionado?.nombre ?: "Sin terreno"
            binding.tvHeaderSubtitle.text = "${cultivoRelacionado?.nombre_lote} (${cultivoRelacionado?.variedad})"

            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            
            // Prioridad: Primera labor de Siembra (ID 2)
            val firstSowingLabor = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
            val fechaReferencia = firstSowingLabor?.fecha_realizacion 
                                  ?: cultivoRelacionado?.fecha_siembra 
                                  ?: cultivoRelacionado?.fecha_planificada

            if (fechaReferencia != null) {
                referenceDateFormatted = sdf.format(Date(fechaReferencia * 1000))
                binding.tvInfoFecha.text = "F. Siembra: $referenceDateFormatted"
            } else {
                // Si no hay siembra ni planificación, mostramos la fecha de alquiler del terreno
                val fechaBase = terrenoRelacionado?.fecha_alquiler
                referenceDateFormatted = if (fechaBase != null) sdf.format(Date(fechaBase * 1000)) else "--/--/----"
                binding.tvInfoFecha.text = "Inicia: $referenceDateFormatted"
            }

            // 3. Estado de Tenencia
            val tenencia = terrenoRelacionado?.tipo_tenencia?.uppercase() ?: ""
            binding.tvInfoTenencia.text = tenencia
            if (tenencia == "PROPIO") {
                binding.tvInfoTenencia.setTextColor(android.graphics.Color.parseColor("#15803D"))
            } else {
                binding.tvInfoTenencia.setTextColor(android.graphics.Color.parseColor("#B45309"))
            }

            // 4. Contadores de Labores
            val counts = laborsHistory.groupBy { it.catalogo_labor_id }.mapValues { it.value.size }
            binding.tvCountPreparacion.text = (counts[1] ?: 0).toString()
            binding.tvCountSiembra.text = (counts[2] ?: 0).toString()
            binding.tvCountRiego.text = (counts[3] ?: 0).toString()
            binding.tvCountFumigacion.text = (counts[4] ?: 0).toString()
            binding.tvCountAporque.text = (counts[5] ?: 0).toString()
            binding.tvCountDeshierbe.text = (counts[6] ?: 0).toString()
            binding.tvCountFertilizacion.text = (counts[7] ?: 0).toString()
            binding.tvCountOtros.text = (counts[8] ?: 0).toString()
            binding.tvCountCosecha.text = (counts[9] ?: 0).toString()

            // 5. Estado de Secuencia (Basado ÚNICAMENTE en historial guardado en DB)
            yaPreparo = laborsHistory.any { it.catalogo_labor_id == 1 }
            yaSembro = laborsHistory.any { it.catalogo_labor_id == 2 }
            
            updateLaborGridState()
        }
    }

    private fun handleLaborClick(laborId: Int, laborName: String) {
        // Validación de seguridad adicional por si el click pasa el filtro de isClickable
        if (laborId == 2 && !yaPreparo) {
            Toast.makeText(this, "⚠️ Debe registrar al menos una labor de Preparación antes de Sembrar", Toast.LENGTH_SHORT).show()
            return
        }
        if (laborId >= 3 && !yaSembro) {
            Toast.makeText(this, "⚠️ Debe registrar la Siembra antes de habilitar otras labores", Toast.LENGTH_SHORT).show()
            return
        }

        val history = laborsHistory.filter { it.catalogo_labor_id == laborId }
        if (history.isNotEmpty()) {
            showHistoryModal(laborId, laborName, history)
        } else {
            openLaborDialog(laborId, laborName)
        }
    }

    private fun showHistoryModal(laborId: Int, laborName: String, history: List<LaborRealizadaEntity>) {
        val last = history.maxByOrNull { it.fecha_realizacion } ?: return
        val dialog = BottomSheetDialog(this)
        val hBinding = DialogLaborHistoryBriefBinding.inflate(layoutInflater)
        dialog.setContentView(hBinding.root)

        hBinding.tvTitle.text = "$laborName (${history.size})"
        val sdf = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
        val formattedDate = sdf.format(Date(last.fecha_realizacion * 1000))
        hBinding.tvLastDate.text = getString(R.string.label_last_time, formattedDate)
        
        lifecycleScope.launch {
            val mo = db.assetDao().getManoObraByLabor(last.id)
            val totalMO = mo.sumOf { it.subtotal }
            val cantMO = mo.sumOf { it.cantidad_trabajadores }
            val insumosCosto = db.assetDao().getCostoInsumosByLabor(last.id) ?: 0.0
            
            val totalGasto = totalMO + last.costo_maquinaria_total + insumosCosto
            hBinding.tvSummary.text = "${getString(R.string.label_gasto, totalGasto)}\n${getString(R.string.label_mo_peones, cantMO)}"
        }

        hBinding.btnVerDetalles.setOnClickListener {
            val intent = Intent(this, LaborTypeDetailActivity::class.java)
            intent.putExtra("CULTIVO_ID", selectedCultivoId)
            intent.putExtra("LABOR_ID", laborId)
            intent.putExtra("LABOR_NOMBRE", laborName)
            startActivity(intent)
            dialog.dismiss()
        }

        hBinding.btnNuevaLabor.setOnClickListener {
            openLaborDialog(laborId, laborName)
            dialog.dismiss()
        }

        dialog.show()
    }

    private fun updateLaborGridState() {
        // Bloqueos visuales según la secuencia agrícola estricta
        
        // 1. Siembra (2) requiere al menos una Preparación (1)
        val siembraEnabled = yaPreparo
        binding.cardSiembra.alpha = if (siembraEnabled) 1.0f else 0.4f
        binding.cardSiembra.isClickable = siembraEnabled
        binding.cardSiembra.isFocusable = siembraEnabled

        // 2. Labores de mantenimiento (3-8) y Cosecha (9) requieren al menos una Siembra (2)
        val maintenanceEnabled = yaSembro
        val maintenanceCards = listOf(
            binding.cardRiego, binding.cardFumigacion, binding.cardAporque,
            binding.cardDeshierbe, binding.cardFertilizacion, binding.cardPoda, binding.cardCosecha
        )
        
        maintenanceCards.forEach { card ->
            card.alpha = if (maintenanceEnabled) 1.0f else 0.4f
            card.isClickable = maintenanceEnabled
            card.isFocusable = maintenanceEnabled
        }
        
        // Preparación (1) siempre habilitada al inicio
        binding.cardPreparacion.alpha = 1.0f
        binding.cardPreparacion.isClickable = true
    }

    private fun openLaborDialog(laborId: Int, laborName: String) {
        if (currentLabor != null) {
            androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("Labor en curso")
                .setMessage("¿Estás seguro de registrar otra labor sin antes guardar la actual?")
                .setPositiveButton("Sí, descartar y nueva") { _, _ ->
                    currentLabor = null
                    // Actualizar flags de secuencia síncronamente antes de abrir el diálogo
                    yaPreparo = laborsHistory.any { it.catalogo_labor_id == 1 }
                    yaSembro = laborsHistory.any { it.catalogo_labor_id == 2 }
                    refreshScreenState() // Async update of grid visuals
                    showLaborDialog(laborId, laborName)
                }
                .setNegativeButton("No", null)
                .show()
        } else {
            showLaborDialog(laborId, laborName)
        }
    }

    private fun showLaborDialog(laborId: Int, laborName: String, existingLabor: LaborSessionItem? = null) {
        if (existingLabor == null) {
            // Validación de secuencia según requerimientos:
            // 2. Labores de mantenimiento (3-8) y Cosecha (9) requieren siembra previa registrada
            if (laborId >= 3 && !yaSembro) {
                Toast.makeText(this, getString(R.string.error_require_siembra), Toast.LENGTH_SHORT).show()
                return
            }
            // Preparación (1) y Siembra (2) no tienen restricciones de cantidad para permitir re-siembras o preparaciones continuas
        }

        val dialog = BottomSheetDialog(this)
        val dBinding = DialogAddLaborBinding.inflate(layoutInflater)
        currentDialogBinding = dBinding
        dialog.setContentView(dBinding.root)

        dialog.setOnDismissListener { currentDialogBinding = null }

        // Configurar Encabezado del Diálogo (Solo Título de la Labor en grande)
        dBinding.tvFormLabel.text = "DETALLES: $laborName".uppercase()
        
        // Reset selectedImageUri para nueva labor o cargar existente
        selectedImageUri = existingLabor?.fotoPath?.let { Uri.parse(it) }

        refreshDialogFotoUI()

        dBinding.cardFotoLabor.setOnClickListener {
            val options = arrayOf("Tomar Foto", "Elegir de Galería")
            androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("Seleccionar Imagen")
                .setItems(options) { _, which ->
                    when (which) {
                        0 -> {
                            try {
                                val photoFile = java.io.File(getExternalFilesDir(android.os.Environment.DIRECTORY_PICTURES), "LABOR_${System.currentTimeMillis()}.jpg")
                                cameraImageUri = androidx.core.content.FileProvider.getUriForFile(this, "${packageName}.fileprovider", photoFile)
                                selectedImageUri = cameraImageUri
                                takePhotoLauncher.launch(cameraImageUri!!)
                            } catch (e: Exception) {
                                Toast.makeText(this@RegisterLaborActivity, "Error al abrir la cámara", Toast.LENGTH_SHORT).show()
                            }
                        }
                        1 -> pickImageLauncher.launch("image/*")
                    }
                }
                .show()
        }

        dBinding.btnRemoveFoto.setOnClickListener {
            selectedImageUri = null
            refreshDialogFotoUI()
        }
        
        // Si estamos editando, cargar datos previos
        existingLabor?.let { labor ->
            calendar.timeInMillis = labor.timestamp * 1000
            dBinding.etFecha.setText(SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(calendar.time))
            dBinding.etDescripcion.setText(labor.desc)
            dBinding.etCostoMaq.setText(labor.costoMaq.toString())
            
            labor.manoObra.forEach { mo ->
                if (mo.tipoId == 1) {
                    dBinding.etVaronCant.setText(mo.cantidad.toString())
                    dBinding.etVaronCosto.setText(mo.costoDia.toString())
                } else if (mo.tipoId == 2) {
                    dBinding.etMujerCant.setText(mo.cantidad.toString())
                    dBinding.etMujerCosto.setText(mo.costoDia.toString())
                }
            }
        }

        val updateSubtotal = {
            val varonSub = (dBinding.etVaronCant.text.toString().toDoubleOrNull() ?: 0.0) * 
                           (dBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0)
            
            val mujerSub = (dBinding.etMujerCant.text.toString().toDoubleOrNull() ?: 0.0) * 
                            (dBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0)

            val maq = dBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
            
            dBinding.tvLaborSubtotal.text = "S/ ${String.format("%.2f", varonSub + mujerSub + maq)}"
        }

        val textWatcher = object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) { updateSubtotal() }
            override fun afterTextChanged(s: android.text.Editable?) {}
        }
        
        dBinding.etVaronCant.addTextChangedListener(textWatcher)
        dBinding.etVaronCosto.addTextChangedListener(textWatcher)
        dBinding.etMujerCant.addTextChangedListener(textWatcher)
        dBinding.etMujerCosto.addTextChangedListener(textWatcher)
        dBinding.etCostoMaq.addTextChangedListener(textWatcher)

        // 1. Calcular Fecha Mínima Aceptable según la lógica de secuencia
        var minDateMillis: Long = 0
        terrenoRelacionado?.fecha_alquiler?.let { minDateMillis = it * 1000 }

        when {
            laborId == 1 -> { // Preparación
                // Solo le restringe el alquiler y preparaciones previas (no la siembra planificada)
                val lastPrep = laborsHistory.filter { it.catalogo_labor_id == 1 }.maxByOrNull { it.fecha_realizacion }
                lastPrep?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
            }
            laborId == 2 -> { // Siembra
                // Le restringe la última preparación
                val lastPrep = laborsHistory.filter { it.catalogo_labor_id == 1 }.maxByOrNull { it.fecha_realizacion }
                lastPrep?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }

                // ADEMÁS: Si ya hay una siembra, la resiembra no puede ser antes de la primera
                val primeraSiembra = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                primeraSiembra?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
            }
            else -> { // Labores de mantenimiento (3+) y Cosecha (9)
                // Restringidas por la fecha de Siembra (Real preferida, planificada como fallback)
                val sowingLabor = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                val sowingDate = sowingLabor?.fecha_realizacion 
                                 ?: cultivoRelacionado?.fecha_siembra 
                                 ?: cultivoRelacionado?.fecha_planificada
                
                sowingDate?.let { minDateMillis = maxOf(minDateMillis, it * 1000) }
                
                // Y por el orden de labores reales (excluyendo ID 10 de Alquiler)
                val ultimaLaborReal = laborsHistory.filter { it.catalogo_labor_id != 10 }.maxByOrNull { it.fecha_realizacion }
                if (ultimaLaborReal != null) {
                    minDateMillis = maxOf(minDateMillis, ultimaLaborReal.fecha_realizacion * 1000)
                }
            }
        }

        // 2. Establecer SUGERENCIA y RANGOS (Punto sobre Resiembras)
        if (existingLabor == null) {
            if (laborId == 2 && yaSembro) {
                // Si ya hubo una siembra, permitimos resiembras desde la 1ra hasta hoy
                val primeraSiembra = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                primeraSiembra?.let {
                    val fecha1ra = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(it.fecha_realizacion * 1000))
                    dBinding.tvLabelFecha.text = "Fecha de Resiembra (Desde: $fecha1ra)"
                    dBinding.tvLabelFecha.setTextColor(android.graphics.Color.parseColor("#15803D"))
                    calendar.timeInMillis = System.currentTimeMillis() // Sugerimos hoy para la resiembra
                }
            } else if (laborId >= 3) {
                // Sugerir la fecha de HOY por defecto para mantenimiento y cosecha, 
                // asegurándonos de que no sea menor al mínimo permitido (fecha de siembra).
                val today = System.currentTimeMillis()
                calendar.timeInMillis = maxOf(minDateMillis, today)
            } else {
                // Para Preparación y Siembra, sugerimos Hoy (si el alquiler/preparación previa lo permite)
                val today = System.currentTimeMillis()
                calendar.timeInMillis = maxOf(minDateMillis, today)
            }
        } else {
            calendar.timeInMillis = existingLabor.timestamp * 1000
        }

        val sdfFormat = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        dBinding.etFecha.setText(sdfFormat.format(calendar.time))
        if (minDateMillis > 0 && dBinding.etFecha.isEnabled) {
            dBinding.tvLabelFecha.text = "Fecha de realización (Mínimo: ${sdfFormat.format(Date(minDateMillis))})"
        }

        dBinding.etFecha.setOnClickListener {
            val datePickerDialog = DatePickerDialog(this, { _, y, m, d ->
                calendar.set(y, m, d)
                dBinding.etFecha.setText(sdfFormat.format(calendar.time))
            }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH))

            // 1. Mínimo permitido
            if (minDateMillis > 0) {
                datePickerDialog.datePicker.minDate = minDateMillis
            }
            
            // 2. Máximo permitido: HOY (Bloquea fechas futuras)
            datePickerDialog.datePicker.maxDate = System.currentTimeMillis()

            datePickerDialog.show()
        }

        dBinding.btnAccept.setOnClickListener {
            val fechaSeleccionada = calendar.timeInMillis / 1000
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            
            lifecycleScope.launch {
                // 1. Validación contra Alquiler de Terreno
                val fechaAlquiler = terrenoRelacionado?.fecha_alquiler
                if (fechaAlquiler != null && fechaSeleccionada < fechaAlquiler) {
                    Toast.makeText(this@RegisterLaborActivity, "⚠️ No se puede registrar labores antes de la fecha de alquiler del terreno (${sdf.format(Date(fechaAlquiler * 1000))})", Toast.LENGTH_LONG).show()
                    return@launch
                }

                // 2. Validación de Secuencia Global
                // Nota: Ignoramos la labor ID 10 (Alquiler automático) ya que es solo un registro de costo
                // y no debe bloquear la cronología real de las labores físicas.
                val laboresReales = laborsHistory.filter { it.catalogo_labor_id != 10 }
                val ultimaLabor = laboresReales.maxByOrNull { it.fecha_realizacion }
                
                if (ultimaLabor != null && fechaSeleccionada < ultimaLabor.fecha_realizacion) {
                    // Solo bloqueamos si es una labor de mantenimiento (3+) o cosecha (9)
                    // Las de Preparación (1) y Siembra (2) pueden registrarse para corregir la cronología inicial
                    if (laborId >= 3) {
                        Toast.makeText(this@RegisterLaborActivity, "⚠️ Esta labor no puede ser anterior a la registrada previamente (${sdf.format(Date(ultimaLabor.fecha_realizacion * 1000))})", Toast.LENGTH_LONG).show()
                        return@launch
                    }
                }

                // 3. Validación: Labores de mantenimiento >= Primera Siembra
                if (laborId in 3..8) {
                    val primeraSiembra = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                    if (primeraSiembra != null && fechaSeleccionada < primeraSiembra.fecha_realizacion) {
                        Toast.makeText(this@RegisterLaborActivity, "⚠️ Esta labor no puede ser anterior a la primera siembra (${sdf.format(Date(primeraSiembra.fecha_realizacion * 1000))})", Toast.LENGTH_LONG).show()
                        return@launch
                    }
                }

                // 4. Validación: Cronología del mismo tipo de labor
                val ultimaMismoTipo = laborsHistory
                    .filter { it.catalogo_labor_id == laborId }
                    .maxByOrNull { it.fecha_realizacion }
                
                if (ultimaMismoTipo != null && fechaSeleccionada < ultimaMismoTipo.fecha_realizacion) {
                    Toast.makeText(this@RegisterLaborActivity, "⚠️ Esta labor no puede ser anterior a la registrada previamente (${sdf.format(Date(ultimaMismoTipo.fecha_realizacion * 1000))})", Toast.LENGTH_LONG).show()
                    return@launch
                }

                // 5. Validación: Cosecha (ID 9) vs Primera Siembra
                if (laborId == 9) {
                    val primeraSiembra = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                    val sowingDate = primeraSiembra?.fecha_realizacion ?: cultivoRelacionado?.fecha_siembra
                    val expectedDays = catalogoCultivo?.dias_a_cosecha_promedio

                    if (sowingDate != null && expectedDays != null && expectedDays > 0) {
                        val diffMillis = (fechaSeleccionada * 1000) - (sowingDate * 1000)
                        val daysPassed = diffMillis / (1000 * 60 * 60 * 24)
                        
                        if (daysPassed < (expectedDays * 0.75)) {
                            val progress = (daysPassed.toDouble() / expectedDays * 100).toInt()
                            androidx.appcompat.app.AlertDialog.Builder(this@RegisterLaborActivity)
                                .setTitle("⚠️ ¡Advertencia de Cosecha!")
                                .setMessage("Atención: Solo han pasado $daysPassed días desde la siembra (aproximadamente un $progress% del ciclo esperado de $expectedDays días).\n\n¿Estás seguro de que deseas registrar la cosecha?")
                                .setPositiveButton("Sí, continuar") { _, _ ->
                                    completeLaborRegistration(laborId, laborName, fechaSeleccionada, dBinding, existingLabor, dialog)
                                }
                                .setNegativeButton("Cancelar", null)
                                .show()
                            return@launch
                        }
                    }
                }

                completeLaborRegistration(laborId, laborName, fechaSeleccionada, dBinding, existingLabor, dialog)
            }
        }

        dBinding.btnCancel.setOnClickListener { dialog.dismiss() }
        dialog.show()
    }

    private fun completeLaborRegistration(
        laborId: Int, 
        laborName: String, 
        fechaSeleccionada: Long, 
        dBinding: DialogAddLaborBinding, 
        existingLabor: LaborSessionItem?,
        dialog: BottomSheetDialog
    ) {
        val maq = dBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
        val manoObraItems = mutableListOf<ManoObraSessionItem>()
        
        val vCant = dBinding.etVaronCant.text.toString().toIntOrNull() ?: 0
        val vCosto = dBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0
        if (vCant > 0) manoObraItems.add(ManoObraSessionItem(1, vCant, 1, vCosto))

        val mCant = dBinding.etMujerCant.text.toString().toIntOrNull() ?: 0
        val mCosto = dBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0
        if (mCant > 0) manoObraItems.add(ManoObraSessionItem(2, mCant, 1, mCosto))

        currentLabor = LaborSessionItem(
            laborId, laborName, fechaSeleccionada, 
            dBinding.etDescripcion.text.toString(), maq, manoObraItems, existingLabor?.insumos ?: emptyList(),
            selectedImageUri?.toString()
        )
        
        refreshLaboresList()
        refreshScreenState() // Actualiza grid (bloqueos) y flags con la nueva labor en sesión
        dialog.dismiss()
    }

    private fun openInsumoDialog(editingIndex: Int? = null, onSync: (List<InsumoSessionItem>) -> Unit) {
        val dialog = BottomSheetDialog(this)
        val iBinding = DialogAddInsumoBinding.inflate(layoutInflater)
        dialog.setContentView(iBinding.root)

        val tempInsumosList = currentLabor?.insumos?.toMutableList() ?: mutableListOf()
        var currentEditingIndex: Int? = editingIndex

        fun loadInsumoToEdit(index: Int) {
            val ins = tempInsumosList[index]
            iBinding.etNombreInsumo.setText(ins.nombre)
            iBinding.etCantidad.setText(ins.cantidad.toString())
            iBinding.etPrecio.setText(ins.precio.toString())
            iBinding.etFlete.setText(ins.flete.toString())
            iBinding.cbHasProveedor.isChecked = ins.proveedor != null
            iBinding.etProveedor.setText(ins.proveedor ?: "")
            iBinding.btnAcceptInsumo.text = "Actualizar Insumo"
        }

        if (currentEditingIndex != null) {
            loadInsumoToEdit(currentEditingIndex!!)
        }

        fun refreshInsumoListInDialog() {
            iBinding.llInsumosAgregados.removeAllViews()
            if (tempInsumosList.isEmpty()) {
                iBinding.tvInsumosAgregadosTitle.visibility = View.GONE
            } else {
                iBinding.tvInsumosAgregadosTitle.visibility = View.VISIBLE
                iBinding.tvInsumosTitle.text = "Insumos (${tempInsumosList.size})"
                
                tempInsumosList.forEachIndexed { index, ins ->
                    val row = ItemInsumoRowBinding.inflate(layoutInflater, iBinding.llInsumosAgregados, false)
                    row.tvInsumoNombre.text = ins.nombre
                    
                    lifecycleScope.launch {
                        val unidad = db.assetDao().getUnidadInsumo(ins.catId) ?: ""
                        row.tvInsumoCant.text = "${ins.cantidad} $unidad"
                    }
                    
                    row.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.precio) + ins.flete)}"
                    
                    row.root.setOnClickListener {
                        androidx.appcompat.app.AlertDialog.Builder(this)
                            .setTitle("Editar Insumo")
                            .setMessage("¿Desea editar o eliminar este insumo?")
                            .setPositiveButton("Editar") { _, _ ->
                                loadInsumoToEdit(index)
                                currentEditingIndex = index
                            }
                            .setNegativeButton("Eliminar") { _, _ ->
                                tempInsumosList.removeAt(index)
                                refreshInsumoListInDialog()
                                onSync(tempInsumosList)
                            }
                            .show()
                    }
                    iBinding.llInsumosAgregados.addView(row.root)
                }
            }
        }

        refreshInsumoListInDialog()

        iBinding.cbHasProveedor.setOnCheckedChangeListener { _, isChecked ->
            iBinding.llProveedor.visibility = if (isChecked) View.VISIBLE else View.GONE
        }

        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoInsumos()
            val adapter = ArrayAdapter(this@RegisterLaborActivity, android.R.layout.simple_dropdown_item_1line, catalogo.map { it.nombre })
            iBinding.etNombreInsumo.setAdapter(adapter)
        }

        iBinding.btnAcceptInsumo.setOnClickListener {
            val nombre = iBinding.etNombreInsumo.text.toString()
            val cant = iBinding.etCantidad.text.toString().toDoubleOrNull() ?: 0.0
            val precio = iBinding.etPrecio.text.toString().toDoubleOrNull() ?: 0.0
            val flete = iBinding.etFlete.text.toString().toDoubleOrNull() ?: 0.0
            val unidad = iBinding.etUnidad.text.toString()
            val proveedor = if (iBinding.cbHasProveedor.isChecked) iBinding.etProveedor.text.toString() else null

            if (nombre.isNotEmpty() && cant > 0) {
                lifecycleScope.launch {
                    val catalogo = db.assetDao().getCatalogoInsumos()
                    var catId = catalogo.find { it.nombre.lowercase() == nombre.lowercase() }?.id
                    
                    if (catId == null) {
                        catId = db.assetDao().insertCatalogoInsumo(CatalogoInsumoEntity(
                            nombre = nombre,
                            categoria = "Otros",
                            unidad_medida = unidad,
                            descripcion = ""
                        )).toInt()
                    }
                    
                    val insumo = InsumoSessionItem(catId, nombre, cant, precio, flete, proveedor)
                    
                    if (currentEditingIndex != null) {
                        tempInsumosList[currentEditingIndex!!] = insumo
                        currentEditingIndex = null
                        iBinding.btnAcceptInsumo.text = "Agregar Insumo"
                    } else {
                        tempInsumosList.add(insumo)
                    }

                    onSync(tempInsumosList)
                    refreshInsumoListInDialog()

                    // Limpiar campos
                    iBinding.etNombreInsumo.setText("")
                    iBinding.etCantidad.setText("")
                    iBinding.etPrecio.setText("")
                    iBinding.etFlete.setText("")
                    iBinding.etProveedor.setText("")

                    androidx.appcompat.app.AlertDialog.Builder(this@RegisterLaborActivity)
                        .setTitle("¿Agregar más?")
                        .setMessage("¿Deseas agregar otro insumo para esta labor?")
                        .setPositiveButton("Sí") { _, _ -> /* Se queda en el modal */ }
                        .setNegativeButton("No, listo") { _, _ -> dialog.dismiss() }
                        .show()
                }
            } else {
                Toast.makeText(this, "Completa los datos del insumo", Toast.LENGTH_SHORT).show()
            }
        }

        iBinding.btnCancelInsumo.setOnClickListener { dialog.dismiss() }
        dialog.show()
    }

    private fun refreshLaboresList() {
        binding.llLaboresList.removeAllViews()
        val labor = currentLabor
        
        if (labor == null) {
            binding.tvLaboresListTitle.text = getString(R.string.label_labor_sesion)
            binding.tvEmptyLabores.visibility = View.VISIBLE
            binding.tvEmptyLabores.text = getString(R.string.msg_no_labor_selected)
            binding.btnFinalizarRegistro.isEnabled = false
            binding.tvCostoTotalSession.text = "S/ 0.00"
        } else {
            binding.tvLaboresListTitle.text = getString(R.string.label_labor_to_register, labor.name)
            binding.tvEmptyLabores.visibility = View.GONE
            binding.btnFinalizarRegistro.isEnabled = true
            
            val itemBinding = ItemLaborDetalleBinding.inflate(layoutInflater, binding.llLaboresList, false)
            
            // Configuración visual de la tarjeta (Consistente con el nuevo diseño)
            itemBinding.markerContainer.visibility = View.GONE
            itemBinding.timelineLine.visibility = View.GONE
            itemBinding.llFooterSession.visibility = View.VISIBLE
            itemBinding.llInsumosDetalle.visibility = View.VISIBLE

            val cardParams = itemBinding.cardContainer.layoutParams as android.view.ViewGroup.MarginLayoutParams
            cardParams.leftMargin = 0
            cardParams.marginStart = 0
            itemBinding.cardContainer.layoutParams = cardParams

            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            itemBinding.tvLaborBadge.text = labor.name
            itemBinding.tvLaborFecha.text = sdf.format(Date(labor.timestamp * 1000))

            // 1. Cargar imagen si existe (Soporte para URI y File Path)
            if (!labor.fotoPath.isNullOrBlank()) {
                val imageSource: Any = if (labor.fotoPath.startsWith("content://") || labor.fotoPath.startsWith("file://")) {
                    Uri.parse(labor.fotoPath)
                } else {
                    java.io.File(labor.fotoPath)
                }

                itemBinding.ivLaborFoto.visibility = View.VISIBLE
                itemBinding.vGradientOverlay.visibility = View.VISIBLE
                itemBinding.flHeaderContainer.background = null // Quitar fondo verde si hay imagen
                com.bumptech.glide.Glide.with(this)
                    .load(imageSource)
                    .centerCrop()
                    .into(itemBinding.ivLaborFoto)
            } else {
                itemBinding.ivLaborFoto.visibility = View.GONE
                itemBinding.vGradientOverlay.visibility = View.GONE
                // Restaurar fondo verde si no hay imagen
                itemBinding.flHeaderContainer.setBackgroundColor(android.graphics.Color.parseColor("#15803D"))
            }

            // 2. Ocultar el botón "VER DETALLES" en esta pantalla (Solicitud Punto 2)
            itemBinding.btnExpandAction.visibility = View.GONE
            
            val costoMO = labor.manoObra.sumOf { it.cantidad * it.dias * it.costoDia }
            val subtotalInsumos = labor.insumos.sumOf { (it.cantidad * it.precio) + it.flete }
            val subtotal = costoMO + labor.costoMaq + subtotalInsumos
            
            val totalPers = labor.manoObra.sumOf { it.cantidad }
            itemBinding.tvManoObraResumen.text = "Mano de obra $totalPers pers."
            itemBinding.tvManoObraResumen.visibility = View.VISIBLE
            itemBinding.tvLaborCostos.text = "S/ ${String.format("%.2f", subtotal)}"
            val labelInsumos = if (labor.insumos.size == 1) "1 insumo utilizado" else "${labor.insumos.size} insumos utilizados"
            itemBinding.tvLaborInsumosCount.text = labelInsumos

            itemBinding.tvLaborTotal.text = "Subtotal: S/ ${String.format("%.2f", subtotal)}"
            itemBinding.tvLaborManoObraCount.text = "$totalPers"

            itemBinding.tvLaborTotal.text = "Subtotal: S/ ${String.format("%.2f", subtotal)}"
            binding.tvCostoTotalSession.text = "S/ ${String.format("%.2f", subtotal)}"

            // Mostrar botones de edición y agregar insumos en esta pantalla
            itemBinding.btnEditLabor.visibility = View.VISIBLE
            itemBinding.btnRemoveLabor.visibility = View.VISIBLE
            itemBinding.btnAddInsumoItem.visibility = View.VISIBLE

            // Listar detalles de la labor (Descripción, Mano de Obra, Maquinaria e Insumos)
            itemBinding.llInsumosDetalle.removeAllViews()

            // 1. Mostrar Mano de Obra (Peones)
            labor.manoObra.forEach { mo ->
                val moRow = ItemInsumoRowBinding.inflate(layoutInflater, itemBinding.llInsumosDetalle, false)
                moRow.tvInsumoNombre.text = if (mo.tipoId == 1) "Peones (Varones)" else "Peones (Mujeres)"
                moRow.tvInsumoCant.text = "${mo.cantidad} pers."
                moRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", mo.cantidad * mo.dias * mo.costoDia)}"
                itemBinding.llInsumosDetalle.addView(moRow.root)
            }

            // 3. Mostrar Maquinaria
            if (labor.costoMaq > 0) {
                val maqRow = ItemInsumoRowBinding.inflate(layoutInflater, itemBinding.llInsumosDetalle, false)
                maqRow.tvInsumoNombre.text = "Maquinaria / Otros"
                maqRow.tvInsumoCant.text = ""
                maqRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", labor.costoMaq)}"
                itemBinding.llInsumosDetalle.addView(maqRow.root)
            }

            // 4. Listar Insumos
            labor.insumos.forEachIndexed { index, ins ->
                val insRow = ItemInsumoRowBinding.inflate(layoutInflater, itemBinding.llInsumosDetalle, false)
                insRow.tvInsumoNombre.text = ins.nombre
                
                lifecycleScope.launch {
                    val unidad = db.assetDao().getUnidadInsumo(ins.catId) ?: ""
                    val cantStr = if (ins.cantidad % 1.0 == 0.0) ins.cantidad.toInt().toString() else ins.cantidad.toString()
                    insRow.tvInsumoCant.text = "$cantStr $unidad"
                }

                insRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.precio) + ins.flete)}"
                
                insRow.btnEditInsumo.visibility = View.VISIBLE
                insRow.btnRemoveInsumo.visibility = View.VISIBLE
                
                insRow.btnEditInsumo.setOnClickListener {
                    openInsumoDialog(index) { nuevosInsumos ->
                        currentLabor = labor.copy(insumos = nuevosInsumos)
                        refreshLaboresList()
                    }
                }
                
                insRow.btnRemoveInsumo.setOnClickListener {
                    val nuevosInsumos = labor.insumos.toMutableList()
                    nuevosInsumos.removeAt(index)
                    currentLabor = labor.copy(insumos = nuevosInsumos)
                    refreshLaboresList()
                }

                insRow.root.setOnLongClickListener {
                    androidx.appcompat.app.AlertDialog.Builder(this)
                        .setMessage(getString(R.string.msg_edit_insumo_confirm))
                        .setPositiveButton("Sí") { _, _ ->
                            openInsumoDialog(index) { nuevosInsumos ->
                                currentLabor = labor.copy(insumos = nuevosInsumos)
                                refreshLaboresList()
                            }
                        }
                        .setNegativeButton("No", null)
                        .show()
                    true
                }

                itemBinding.llInsumosDetalle.addView(insRow.root)
            }

            itemBinding.btnAddInsumoItem.setOnClickListener {
                openInsumoDialog { nuevosInsumos ->
                    currentLabor = labor.copy(insumos = nuevosInsumos)
                    refreshLaboresList()
                }
            }

            itemBinding.btnEditLabor.setOnClickListener {
                showLaborDialog(labor.id, labor.name, labor)
            }

            itemBinding.btnRemoveLabor.setOnClickListener {
                currentLabor = null
                refreshLaboresList()
                refreshScreenState() // Restablecer bloqueos y flags
            }

            itemBinding.root.setOnLongClickListener {
                androidx.appcompat.app.AlertDialog.Builder(this)
                    .setMessage(getString(R.string.msg_edit_labor_confirm))
                    .setPositiveButton("Sí") { _, _ ->
                        showLaborDialog(labor.id, labor.name, labor)
                    }
                    .setNegativeButton("No", null)
                    .show()
                true
            }

            binding.llLaboresList.addView(itemBinding.root)
        }
    }

    private fun recalculateSequenceState() {
        lifecycleScope.launch {
            val realizadas = db.assetDao().getLaboresByCultivo(selectedCultivoId)
            val laborId = currentLabor?.id
            
            yaPreparo = realizadas.any { it.catalogo_labor_id == 1 } || laborId == 1
            yaSembro = realizadas.any { it.catalogo_labor_id == 2 } || laborId == 2
            
            updateLaborGridState()
        }
    }

    private fun showConfirmationDialog() {
        val labor = currentLabor ?: return
        val dialog = BottomSheetDialog(this)
        val cBinding = DialogConfirmRegistroBinding.inflate(layoutInflater)
        dialog.setContentView(cBinding.root)

        val costoMO = labor.manoObra.sumOf { it.cantidad * it.dias * it.costoDia }
        val total = costoMO + labor.costoMaq + labor.insumos.sumOf { i -> (i.cantidad * i.precio) + i.flete }
        
        cBinding.tvResumenFinal.visibility = View.GONE
        
        // Crear una tabla o vista detallada para la confirmación
        val detailView = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(0, 16, 0, 16)
        }

        // Título de la labor
        val laborTitle = TextView(this).apply {
            text = labor.name.uppercase()
            textSize = 14f
            setTextColor(androidx.core.content.ContextCompat.getColor(this@RegisterLaborActivity, android.R.color.black))
            setTypeface(null, android.graphics.Typeface.BOLD)
        }
        detailView.addView(laborTitle)

        // Detalle de costos directos
        val directCosts = TextView(this).apply {
            text = getString(R.string.label_direct_costs, costoMO + labor.costoMaq)
            textSize = 13f
            setPadding(0, 4, 0, 8)
        }
        detailView.addView(directCosts)

        // Tabla de Insumos si existen
        if (labor.insumos.isNotEmpty()) {
            val insumosTitle = TextView(this).apply {
                text = getString(R.string.label_detailed_insumos)
                textSize = 12f
                setTypeface(null, android.graphics.Typeface.BOLD)
                setPadding(0, 8, 0, 4)
            }
            detailView.addView(insumosTitle)

            labor.insumos.forEach { ins ->
                val insRow = ItemInsumoRowBinding.inflate(layoutInflater)
                insRow.tvInsumoNombre.text = ins.nombre
                
                lifecycleScope.launch {
                    val unidad = db.assetDao().getUnidadInsumo(ins.catId) ?: ""
                    val cantStr = if (ins.cantidad % 1.0 == 0.0) ins.cantidad.toInt().toString() else ins.cantidad.toString()
                    insRow.tvInsumoCant.text = "$cantStr $unidad"
                }

                insRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.precio) + ins.flete)}"
                detailView.addView(insRow.root)
            }
        }

        val totalView = TextView(this).apply {
            text = getString(R.string.label_total_to_register, total)
            textSize = 16f
            setTextColor(androidx.core.content.ContextCompat.getColor(this@RegisterLaborActivity, R.color.green_primary))
            setTypeface(null, android.graphics.Typeface.BOLD)
            gravity = android.view.Gravity.END
        }
        detailView.addView(totalView)

        // Insertar la vista de detalle antes de los botones
        val parent = cBinding.tvResumenFinal.parent as LinearLayout
        val index = parent.indexOfChild(cBinding.tvResumenFinal)
        parent.addView(detailView, index + 1)

        cBinding.btnConfirmar.setOnClickListener {
            saveEverything()
            dialog.dismiss()
        }
        cBinding.btnRevisar.setOnClickListener { dialog.dismiss() }
        dialog.show()
    }

    private fun saveEverything() {
        val item = currentLabor ?: return
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        val userName = sharedPref.getString("USER_NAME", "user") ?: "user"

        lifecycleScope.launch {
            var finalFotoPath: String? = item.fotoPath
            
            // Guardar foto localmente si es un Uri temporal
            item.fotoPath?.let { path ->
                val uri = Uri.parse(path)
                if (uri.scheme == "content" || uri.scheme == "file") {
                    val savedPath = com.sigcpa.agrosys.util.FileUtils.saveImageLocally(
                        this@RegisterLaborActivity,
                        uri,
                        userId,
                        userName,
                        com.sigcpa.agrosys.util.FileUtils.getLaboresFolder(item.name),
                        "labor_${item.id}_${System.currentTimeMillis()}.jpg"
                    )
                    finalFotoPath = savedPath
                }
            }

            // Guardar labor en DB
            val costoMO_total = item.manoObra.sumOf { it.cantidad * it.dias * it.costoDia }
            val laborId = db.assetDao().insertLabor(LaborRealizadaEntity(
                cultivo_id = selectedCultivoId,
                catalogo_labor_id = item.id,
                fecha_realizacion = item.timestamp,
                costo_mano_obra_total = costoMO_total,
                costo_maquinaria_total = item.costoMaq,
                observaciones = item.desc,
                foto_path = finalFotoPath,
                creado_por_usuario_id = userId
            )).toInt()

            // Si es labor de SIEMBRA (ID 2), este es el REGISTRO MAESTRO que actualiza al Cultivo
            // Solo actualizamos la fecha del cultivo si es la PRIMERA vez para no confundir registros.
            if (item.id == 2) {
                db.assetDao().getCultivoById(selectedCultivoId)?.let { cultivo ->
                    if (cultivo.fecha_siembra == null) {
                        db.assetDao().updateCultivo(cultivo.copy(
                            fecha_siembra = item.timestamp,
                            estado = if (cultivo.estado == "planificado") "activo" else cultivo.estado
                        ))
                    }
                }
            }

            item.manoObra.forEach { mo ->
                db.assetDao().insertManoObra(ManoObraEntity(
                    labor_realizada_id = laborId,
                    tipo_id = mo.tipoId,
                    cantidad_trabajadores = mo.cantidad,
                    dias_trabajados = mo.dias,
                    costo_por_dia = mo.costoDia,
                    subtotal = mo.cantidad * mo.dias * mo.costoDia
                ))
            }

            item.insumos.forEach { ins ->
                db.assetDao().insertInsumoUsado(InsumoUsadoEntity(
                    labor_id = laborId,
                    catalogo_insumo_id = ins.catId,
                    proveedor_id = null,
                    cantidad = ins.cantidad,
                    costo_unitario = ins.precio,
                    costo_flete = ins.flete,
                    nombre_proveedor_manual = ins.proveedor
                ))
            }
            
            Toast.makeText(this@RegisterLaborActivity, getString(R.string.msg_labor_saved), Toast.LENGTH_SHORT).show()
            
            // --- DIÁLOGO DE SEGUIMIENTO ---
            val isCosecha = item.id == 9
            val builder = androidx.appcompat.app.AlertDialog.Builder(this@RegisterLaborActivity)
                .setTitle("Registro Exitoso")
                .setMessage(if (isCosecha) "¿Deseas agregar insumos o registrar el detalle de la cosecha ahora?" 
                           else "¿Deseas agregar insumos a esta labor ahora?")
            
            builder.setPositiveButton("Agregar Insumos") { _, _ ->
                openInsumoDialog { nuevosInsumos ->
                    // Si el usuario decide agregar insumos DESPUÉS de guardar, 
                    // tendríamos que insertarlos directamente en la DB.
                    // Para simplificar esta navegación, si el usuario ya guardó, 
                    // lo redirigimos al detalle de la labor para gestionar insumos.
                    val intent = Intent(this@RegisterLaborActivity, LaborTypeDetailActivity::class.java)
                    intent.putExtra("CULTIVO_ID", selectedCultivoId)
                    intent.putExtra("LABOR_ID", item.id)
                    intent.putExtra("LABOR_NOMBRE", item.name)
                    startActivity(intent)
                }
            }

            if (isCosecha) {
                builder.setNeutralButton("Registrar Cosecha") { _, _ ->
                    val hIntent = Intent(this@RegisterLaborActivity, RegisterCosechaActivity::class.java)
                    hIntent.putExtra("CULTIVO_ID", selectedCultivoId)
                    hIntent.putExtra("LABOR_ID", laborId)
                    startActivity(hIntent)
                }
            }

            builder.setNegativeButton("Después", null)
            builder.show()

            // Limpiar para un nuevo registro sin cerrar la actividad
            currentLabor = null
            binding.tvCostoTotalSession.text = "S/ 0.00"
            refreshLaboresList()
            refreshScreenState() // Refresca TODO: Header, Grid, Counts y Flags desde DB
        }
    }

    data class LaborSessionItem(val id: Int, val name: String, val timestamp: Long, val desc: String, val costoMaq: Double, val manoObra: List<ManoObraSessionItem>, val insumos: List<InsumoSessionItem>, val fotoPath: String? = null)
    data class ManoObraSessionItem(val tipoId: Int, val cantidad: Int, val dias: Int, val costoDia: Double)
    data class InsumoSessionItem(val catId: Int, val nombre: String, val cantidad: Double, val precio: Double, val flete: Double = 0.0, val proveedor: String? = null)
}
