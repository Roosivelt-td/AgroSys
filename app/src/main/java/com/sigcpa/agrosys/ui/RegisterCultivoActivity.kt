package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.graphics.Color
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.LinearLayout
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.graphics.toColorInt
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CatalogoCultivoEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.database.entities.LaborRealizadaEntity
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterCultivoBinding
import com.sigcpa.agrosys.databinding.DialogAddCustomCultivoBinding
import com.sigcpa.agrosys.ui.adapters.TerrenoSelectorAdapter
import com.sigcpa.agrosys.util.FileUtils
import kotlinx.coroutines.launch
import java.io.File
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Date
import java.util.Locale

class RegisterCultivoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterCultivoBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var selectedTerreno: TerrenoEntity? = null
    private var selectedCatalogo: CatalogoCultivoEntity? = null
    private var selectedStatus: String = "activo"
    private val calendarUnico = Calendar.getInstance()
    private var hasSelectedFecha = false

    private var currentUserId: Int = -1

    private var selectedImageUri: Uri? = null
    private var cameraImageUri: Uri? = null
    private val pickImageLauncher = registerForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        uri?.let { showSelectedImage(it) }
    }
    private val takePhotoLauncher = registerForActivityResult(ActivityResultContracts.TakePicture()) { success ->
        if (success) {
            cameraImageUri?.let { showSelectedImage(it) }
        }
    }
    private fun showSelectedImage(uri: Uri) {
        selectedImageUri = uri
        binding.ivCultivo.setImageURI(uri)
        binding.ivCultivo.setPadding(0, 0, 0, 0)
        binding.ivCultivo.scaleType = android.widget.ImageView.ScaleType.CENTER_CROP
        binding.ivCultivo.imageTintList = null
        binding.tvAddFotoLabel.visibility = View.GONE
    }
    private fun showImagePickerDialog() {
        val options = arrayOf("Galería", "Cámara")
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle("Seleccionar imagen")
            .setItems(options) { _, which ->
                if (which == 0) pickImageLauncher.launch("image/*")
                else openCamera()
            }.show()
    }
    private fun openCamera() {
        try {
            val photoFile = File(
                getExternalFilesDir(android.os.Environment.DIRECTORY_PICTURES),
                "temp_cultivo_${System.currentTimeMillis()}.jpg"
            )
            val uri = androidx.core.content.FileProvider.getUriForFile(
                this,
                "${packageName}.fileprovider",
                photoFile
            )
            cameraImageUri = uri
            takePhotoLauncher.launch(uri)
        } catch (e: Exception) {
            e.printStackTrace()
            Toast.makeText(this, "Error al abrir la cámara: ${e.message}", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterCultivoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Branding
        window.statusBarColor = Color.parseColor("#15803D")
        
        // Uso de decorView para mayor compatibilidad en versiones antiguas
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.M) {
            WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false
        }
        
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            window.navigationBarColor = Color.WHITE
            WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightNavigationBars = true
        }

        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            
            val topInset = if (systemBars.top > 0) systemBars.top else 0
            val bottomInset = if (systemBars.bottom > 0) systemBars.bottom else 0

            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                topInset,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            
            binding.scrollContainer.setPadding(0, 0, 0, bottomInset)

            insets
        }

        setupUI()
        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        binding.btnSelectTerreno.setOnClickListener { showTerrenoSelectionDialog() }
        binding.etFechaUnica.setOnClickListener { showDatePicker() }
        binding.btnSaveCultivo.setOnClickListener { saveCultivo() }
        binding.ivCultivo.setOnClickListener { showImagePickerDialog() }
        
        binding.rgTipoFecha.setOnCheckedChangeListener { _, checkedId ->
            if (checkedId == R.id.rbSiembra) {
                updateStatus("activo")
            } else {
                updateStatus("planificado")
            }
        }

        binding.btnStatusPlanificado.setOnClickListener { 
            updateStatus("planificado")
            binding.rbPlanificada.isChecked = true
        }
        binding.btnStatusActivo.setOnClickListener { 
            updateStatus("activo")
            binding.rbSiembra.isChecked = true
        }
        updateStatus("activo")
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
        val ciclo = if (it.tipo_ciclo == "perenne") getString(R.string.label_perenne) else "${it.dias_a_cosecha_promedio ?: "--"} ${getString(R.string.unit_days)}"
        binding.tvCicloInfo.text = getString(R.string.label_ciclo_estimado, ciclo)
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
            currentUserId = userId
            if (userId == -1) {
                finish()
                return@launch
            }
            
            val id = intent.getIntExtra("CULTIVO_ID", -1)
            if (id != -1) {
                binding.tvHeaderTitle.text = getString(R.string.header_editar_cultivo)
                binding.btnSaveCultivo.text = getString(R.string.btn_actualizar_cultivo)
                
                // Bloquear edición del tipo de cultivo en modo edición
                binding.autoCompleteCultivo.isEnabled = false
                binding.tilCultivo.isEnabled = false
                binding.gridSugerencias.visibility = View.GONE

                val cultivo = db.assetDao().getCultivoById(id)
                if (cultivo != null) {
                    fillCultivoData(cultivo)
                }
            } else if (preSelectedTerrenoId != -1) {
                val terreno = db.assetDao().getTerrenoById(preSelectedTerrenoId)
                if (terreno != null) displaySelectedTerreno(terreno)
            } else {
                // Si no hay terreno pre-seleccionado ni es edición, forzar la selección al inicio
                showTerrenoSelectionDialog()
            }

            val catalogo = db.assetDao().getCatalogoByUsuario(userId)
            val sugerencias = db.assetDao().getMostUsedCatalogoCultivos()
            setupSugerencias(sugerencias)

            val nombresCultivos = catalogo.map { it.nombre }
            val adapterCultivo = ArrayAdapter(this@RegisterCultivoActivity, android.R.layout.simple_dropdown_item_1line, nombresCultivos)
            binding.autoCompleteCultivo.setAdapter(adapterCultivo)
            
            // Asegurar que el filtro no bloquee el texto actual al editar
            binding.autoCompleteCultivo.threshold = 1
            
            binding.autoCompleteCultivo.setOnItemClickListener { _, _, position, _ ->
                val nombreSeleccionado = binding.autoCompleteCultivo.adapter.getItem(position) as String
                seleccionarCultivoPorNombre(nombreSeleccionado)
            }

            // Mostrar sugerencias al hacer clic
            binding.autoCompleteCultivo.setOnClickListener {
                binding.autoCompleteCultivo.showDropDown()
            }
            
            binding.autoCompleteCultivo.setOnFocusChangeListener { _, hasFocus ->
                if (hasFocus && binding.autoCompleteCultivo.text.isNotEmpty()) {
                    binding.autoCompleteCultivo.showDropDown()
                }
            }
        }
    }

    private fun displaySelectedTerreno(terreno: TerrenoEntity) {
        selectedTerreno = terreno
        binding.terrenoInfoContainer.visibility = View.VISIBLE
        binding.selectorTerrenoContainer.visibility = View.GONE

        // Validar si la fecha ya seleccionada es válida para el nuevo terreno
        if (hasSelectedFecha) {
            val fechaAlquiler = terreno.fecha_alquiler // en segundos
            if (fechaAlquiler != null && (calendarUnico.timeInMillis / 1000) < fechaAlquiler) {
                binding.etFechaUnica.setText("")
                hasSelectedFecha = false
                Toast.makeText(this, "Fecha reiniciada: debe ser después del alquiler del terreno", Toast.LENGTH_LONG).show()
            }
        }

        lifecycleScope.launch {
            val cultivosActivos = db.assetDao().getCultivosActivosByTerreno(terreno.id)
            val areaOcupada = cultivosActivos.sumOf { it.area_destinada ?: 0.0 }
            
            val cultivoId = intent.getIntExtra("CULTIVO_ID", -1)
            val areaPropia = if (cultivoId != -1) {
                val existing = db.assetDao().getCultivoById(cultivoId)
                if (existing?.terreno_id == terreno.id) existing.area_destinada ?: 0.0 else 0.0
            } else 0.0

            val areaDisponible = terreno.area_hectareas - areaOcupada + areaPropia

            binding.tvSelectedTerrenoName.text = terreno.nombre
            binding.tvSelectedTerrenoArea.text = getString(R.string.label_ha_free, areaDisponible)
            binding.tvSelectedTerrenoUbicacion.text = terreno.direccion_referencia ?: terreno.ubicacion_geo ?: getString(R.string.label_no_location)
            binding.badgeStatus.text = terreno.tipo_tenencia.replaceFirstChar { it.uppercase() }
        }
    }

    private fun showDatePicker() {
        if (selectedTerreno == null) {
            Toast.makeText(this, "Debe seleccionar un terreno primero", Toast.LENGTH_SHORT).show()
            return
        }

        val dialog = DatePickerDialog(this, { _, year, month, day ->
            calendarUnico.set(year, month, day)
            val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            binding.etFechaUnica.setText(format.format(calendarUnico.time))
            hasSelectedFecha = true
        }, calendarUnico.get(Calendar.YEAR), calendarUnico.get(Calendar.MONTH), calendarUnico.get(Calendar.DAY_OF_MONTH))

        selectedTerreno?.fecha_alquiler?.let {
            dialog.datePicker.minDate = it * 1000
        }

        dialog.show()
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
            val user = db.userDao().getUsuarioById(userId) ?: return@launch
            val terrenos = db.assetDao().getTerrenosByAgricultor(user.usuario.id)
            
            val terrenosConArea = terrenos.map { terreno ->
                val areaOcupada = db.assetDao().getAreaOcupadaByTerreno(terreno.id) ?: 0.0
                Pair(terreno, areaOcupada)
            }

            if (terrenos.isEmpty()) {
                Toast.makeText(this@RegisterCultivoActivity, getString(R.string.error_no_terrenos), Toast.LENGTH_LONG).show()
                finish()
                return@launch
            }

            var tempSelected: TerrenoEntity? = null
            
            val adapter = TerrenoSelectorAdapter(terrenosConArea) { terreno ->
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
        }

        dialog.show()
    }

    private fun updateStatus(status: String) {
        selectedStatus = status
        binding.btnStatusPlanificado.alpha = if (status == "planificado") 1.0f else 0.5f
        binding.btnStatusActivo.alpha = if (status == "activo") 1.0f else 0.5f
    }

    private suspend fun fillCultivoData(cultivo: CultivoEntity) {
        val terreno = db.assetDao().getTerrenoById(cultivo.terreno_id)
        if (terreno != null) displaySelectedTerreno(terreno)
        
        val cat = db.assetDao().getCatalogoCultivoById(cultivo.catalogo_cultivo_id)
        if (cat != null) {
            binding.autoCompleteCultivo.setText(cat.nombre, false)
            selectedCatalogo = cat
            updateCicloInfo(cat)
        }
        
        binding.etVariedad.setText(cultivo.variedad)
        binding.etAreaDestinada.setText(cultivo.area_destinada.toString())
        binding.etObservaciones.setText(cultivo.observaciones)
        
        val fechaLong = (cultivo.fecha_siembra ?: cultivo.fecha_planificada ?: 0L) * 1000
        calendarUnico.timeInMillis = fechaLong
        val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        binding.etFechaUnica.setText(format.format(Date(fechaLong)))
        hasSelectedFecha = true
        
        if (cultivo.fecha_siembra != null) {
            binding.rbSiembra.isChecked = true
            updateStatus("activo")
        } else {
            binding.rbPlanificada.isChecked = true
            updateStatus("planificado")
        }
        
        if (!cultivo.foto_path.isNullOrEmpty()) {
            val file = File(cultivo.foto_path)
            val uri = if (file.exists()) Uri.fromFile(file) else Uri.parse(cultivo.foto_path)
            showSelectedImage(uri)
        }
    }

    private fun saveCultivo() {
        val nombreDigitado = binding.autoCompleteCultivo.text.toString().trim()
        val variedad = binding.etVariedad.text.toString().trim()
        val areaStr = binding.etAreaDestinada.text.toString().trim()
        val fechaUnicaStr = binding.etFechaUnica.text.toString().trim()
        val observaciones = binding.etObservaciones.text.toString().trim()
        
        if (selectedTerreno == null) {
            Toast.makeText(this, getString(R.string.error_terreno_required), Toast.LENGTH_SHORT).show()
            return
        }
        if (nombreDigitado.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_cultivo_type_required), Toast.LENGTH_SHORT).show()
            return
        }

        lifecycleScope.launch {
            // Verificar si el cultivo existe en el catálogo (propio o general)
            val catalogoExistente = db.assetDao().getCatalogoByUsuario(currentUserId)
            val coincidencia = catalogoExistente.find { it.nombre.equals(nombreDigitado, ignoreCase = true) }

            if (coincidencia == null) {
                showRegisterNewCultivoDialog(nombreDigitado)
                return@launch
            } else {
                selectedCatalogo = coincidencia
            }

            if (variedad.isEmpty()) {
                Toast.makeText(this@RegisterCultivoActivity, getString(R.string.error_variedad_required), Toast.LENGTH_SHORT).show()
                return@launch
            }
            if (!hasSelectedFecha) {
                Toast.makeText(this@RegisterCultivoActivity, "Debe seleccionar una fecha", Toast.LENGTH_SHORT).show()
                return@launch
            }
            if (areaStr.isEmpty()) {
                Toast.makeText(this@RegisterCultivoActivity, getString(R.string.error_area_required), Toast.LENGTH_SHORT).show()
                return@launch
            }

            val areaNueva = areaStr.toDoubleOrNull() ?: 0.0
            if (areaNueva <= 0) {
                Toast.makeText(this@RegisterCultivoActivity, getString(R.string.error_invalid_area), Toast.LENGTH_SHORT).show()
                return@launch
            }

            val terreno = selectedTerreno!!
            val timestamp = calendarUnico.timeInMillis / 1000
            
            // Validación de fecha vs Alquiler
            if (terreno.fecha_alquiler != null && timestamp < terreno.fecha_alquiler) {
                val format = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
                Toast.makeText(this@RegisterCultivoActivity, "La fecha no puede ser anterior al alquiler (${format.format(Date(terreno.fecha_alquiler * 1000))})", Toast.LENGTH_LONG).show()
                return@launch
            }

            val cultivosActivos = db.assetDao().getCultivosActivosByTerreno(terreno.id)
            val areaOcupada = cultivosActivos.sumOf { it.area_destinada ?: 0.0 }
            
            val cultivoId = intent.getIntExtra("CULTIVO_ID", -1)
            val areaPropia = if (cultivoId != -1) {
                db.assetDao().getCultivoById(cultivoId)?.area_destinada ?: 0.0
            } else 0.0

            val areaDisponible = terreno.area_hectareas - areaOcupada + areaPropia

            if (areaNueva > areaDisponible) {
                Toast.makeText(this@RegisterCultivoActivity, getString(R.string.error_insufficient_area, areaDisponible), Toast.LENGTH_LONG).show()
                return@launch
            }

            if (areaNueva > terreno.area_hectareas) {
                Toast.makeText(this@RegisterCultivoActivity, "El área no puede ser mayor al total del terreno (${terreno.area_hectareas} ha)", Toast.LENGTH_LONG).show()
                return@launch
            }

            val isSiembra = binding.rbSiembra.isChecked
            val existingCultivo = if (cultivoId != -1) db.assetDao().getCultivoById(cultivoId) else null

            var finalFotoPath: String? = existingCultivo?.foto_path
            if (selectedImageUri != null && selectedImageUri.toString() != existingCultivo?.foto_path) {
                selectedImageUri?.let { uri ->
                    if (uri.scheme == "content" || uri.scheme == "file") {
                        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
                        val userName = sharedPref.getString("USER_NAME", "user") ?: "user"
                        val savedPath = FileUtils.saveImageLocally(
                            this@RegisterCultivoActivity,
                            uri,
                            currentUserId,
                            userName,
                            FileUtils.getCultivoFolder(currentUserId, userName),
                            "cultivo_${System.currentTimeMillis()}.jpg"
                        )
                        finalFotoPath = savedPath
                    } else {
                        finalFotoPath = uri.toString()
                    }
                }
            }

            val nuevoCultivo = if (existingCultivo != null) {
                existingCultivo.copy(
                    terreno_id = terreno.id,
                    catalogo_cultivo_id = selectedCatalogo!!.id,
                    nombre_lote = "${selectedCatalogo?.nombre} - $variedad",
                    fecha_planificada = if (isSiembra) (existingCultivo.fecha_planificada ?: timestamp) else timestamp,
                    fecha_siembra = if (isSiembra) timestamp else null,
                    estado = selectedStatus,
                    area_destinada = areaNueva,
                    variedad = variedad,
                    observaciones = observaciones,
                    foto_path = finalFotoPath,
                    updated_at = System.currentTimeMillis() / 1000
                )
            } else {
                CultivoEntity(
                    terreno_id = terreno.id,
                    catalogo_cultivo_id = selectedCatalogo!!.id,
                    nombre_lote = "${selectedCatalogo?.nombre} - $variedad",
                    fecha_planificada = timestamp,
                    fecha_siembra = if (isSiembra) timestamp else null,
                    fecha_finalizacion = null,
                    estado = selectedStatus,
                    area_destinada = areaNueva,
                    variedad = variedad,
                    plantas_estimadas = 0,
                    observaciones = observaciones,
                    foto_path = finalFotoPath
                )
            }

            val id = if (existingCultivo != null) {
                db.assetDao().updateCultivo(nuevoCultivo)
                existingCultivo.id.toLong()
            } else {
                db.assetDao().insertCultivo(nuevoCultivo)
            }

            if (id > 0) {
                // Registrar costo de alquiler como labor inicial si el terreno es alquilado
                if (terreno.tipo_tenencia.lowercase() == "alquilado" && existingCultivo == null) {
                    val baseCosto = terreno.costo_alquiler_anual
                    val areaTotal = terreno.area_hectareas
                    
                    val costoProrrateado = if (terreno.alquiler_modalidad == "hectarea") {
                        baseCosto * areaNueva
                    } else {
                        (baseCosto / areaTotal) * areaNueva
                    }

                    if (costoProrrateado > 0) {
                        val laborAlquiler = LaborRealizadaEntity(
                            cultivo_id = id.toInt(),
                            catalogo_labor_id = 10, // ID 10 para 'Alquiler de Terreno'
                            fecha_realizacion = timestamp,
                            costo_mano_obra_total = 0.0,
                            costo_maquinaria_total = costoProrrateado,
                            observaciones = "Costo de alquiler prorrateado por área (${areaNueva} ha)",
                            creado_por_usuario_id = currentUserId
                        )
                        db.assetDao().insertLabor(laborAlquiler)
                    }
                }

                val msg = if (existingCultivo != null) "Cultivo actualizado" else getString(R.string.msg_cultivo_registered)
                Toast.makeText(this@RegisterCultivoActivity, msg, Toast.LENGTH_LONG).show()
                finish()
            }
        }
    }

    private fun showRegisterNewCultivoDialog(nombre: String) {
        val builder = com.google.android.material.dialog.MaterialAlertDialogBuilder(this)
        builder.setTitle("Cultivo no encontrado")
        builder.setMessage("El cultivo '$nombre' no existe en el catálogo. ¿Desea registrarlo como un nuevo tipo de cultivo?")
        
        builder.setPositiveButton("Registrar") { _, _ ->
            showAdvancedRegisterDialog(nombre)
        }
        builder.setNegativeButton("Cancelar", null)
        builder.show()
    }

    private fun showAdvancedRegisterDialog(nombre: String) {
        val dialogBinding = DialogAddCustomCultivoBinding.inflate(layoutInflater)
        dialogBinding.etCustomNombre.setText(nombre)

        val dialog = com.google.android.material.dialog.MaterialAlertDialogBuilder(this)
            .setTitle("Nuevo Tipo de Cultivo")
            .setView(dialogBinding.root)
            .setPositiveButton("Guardar", null)
            .setNegativeButton("Cancelar", null)
            .create()

        dialog.setOnShowListener {
            val button = dialog.getButton(android.content.DialogInterface.BUTTON_POSITIVE)
            button.setOnClickListener {
                val nombreFinal = dialogBinding.etCustomNombre.text.toString().trim()
                val vidaUtil = dialogBinding.etCustomVidaUtil.text.toString().toIntOrNull()
                val diasCosecha = dialogBinding.etCustomDiasCosecha.text.toString().toIntOrNull()
                val isPerenne = dialogBinding.chipPerenne.isChecked
                val tipoCiclo = if (isPerenne) "perenne" else "ciclo_corto"

                if (nombreFinal.isEmpty()) {
                    dialogBinding.etCustomNombre.error = "El nombre es obligatorio"
                    return@setOnClickListener
                }

                lifecycleScope.launch {
                    val nuevoCatalogo = CatalogoCultivoEntity(
                        nombre = nombreFinal,
                        nombre_cientifico = null,
                        tipo_ciclo = tipoCiclo,
                        vida_util_estimada_meses = vidaUtil,
                        dias_a_cosecha_promedio = diasCosecha,
                        instrucciones_base_riego = null,
                        instrucciones_base_plagas = null,
                        es_personalizado = 1,
                        usuario_creador_id = currentUserId
                    )
                    val id = db.assetDao().insertCatalogoCultivo(nuevoCatalogo)
                    if (id > 0) {
                        Toast.makeText(this@RegisterCultivoActivity, "Tipo de cultivo guardado", Toast.LENGTH_SHORT).show()
                        loadData() 
                        binding.autoCompleteCultivo.setText(nuevoCatalogo.nombre, false)
                        selectedCatalogo = nuevoCatalogo.copy(id = id.toInt())
                        updateCicloInfo(selectedCatalogo!!)
                        dialog.dismiss()
                    } else {
                        Toast.makeText(this@RegisterCultivoActivity, "Error: El nombre ya existe", Toast.LENGTH_SHORT).show()
                    }
                }
            }
        }
        dialog.show()
    }
}
