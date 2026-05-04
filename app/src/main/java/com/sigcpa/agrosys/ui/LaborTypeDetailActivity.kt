package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.content.res.AppCompatResources
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.canhub.cropper.CropImageContract
import com.canhub.cropper.CropImageContractOptions
import com.canhub.cropper.CropImageOptions
import com.canhub.cropper.CropImageView
import com.google.android.material.bottomsheet.BottomSheetDialog
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.databinding.*
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class LaborTypeDetailActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLaborTypeDetailBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var cultivoId: Int = -1
    private var laborId: Int = -1

    private var cultivoRelacionado: CultivoEntity? = null
    private var terrenoRelacionado: TerrenoEntity? = null
    private var catalogoCultivo: CatalogoCultivoEntity? = null
    private var laborsHistory: List<LaborRealizadaEntity> = emptyList()

    private var currentImageUri: android.net.Uri? = null
    private var cameraImageUri: android.net.Uri? = null
    private var dialogBindingForPhoto: DialogAddLaborBinding? = null

    private val cropImage = registerForActivityResult(CropImageContract()) { result ->
        if (result.isSuccessful) {
            val uri = result.uriContent
            currentImageUri = uri
            dialogBindingForPhoto?.let { binding ->
                binding.ivFotoLabor.setImageURI(uri)
                binding.ivFotoLabor.scaleType = android.widget.ImageView.ScaleType.CENTER_CROP
                binding.ivFotoLabor.imageTintList = null
                binding.btnRemoveFoto.visibility = android.view.View.VISIBLE
            }
        }
    }

    private val pickImageLauncher = registerForActivityResult(androidx.activity.result.contract.ActivityResultContracts.GetContent()) { uri ->
        uri?.let { startCrop(it) }
    }

    private val takePhotoLauncher = registerForActivityResult(androidx.activity.result.contract.ActivityResultContracts.TakePicture()) { success ->
        if (success) {
            cameraImageUri?.let { startCrop(it) }
        }
    }

    private fun startCrop(uri: android.net.Uri) {
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

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLaborTypeDetailBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Branding e Insets
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).apply {
            isAppearanceLightStatusBars = false
            window.navigationBarColor = Color.parseColor("#FDFBF7")
            isAppearanceLightNavigationBars = true
        }

        val initialFooterPadding = binding.footerContainer.paddingBottom
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            // Ajustar el footer para que no lo tape la barra de navegación del sistema
            binding.footerContainer.setPadding(
                binding.footerContainer.paddingLeft,
                binding.footerContainer.paddingTop,
                binding.footerContainer.paddingRight,
                initialFooterPadding + systemBars.bottom
            )
            insets
        }

        cultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        laborId = intent.getIntExtra("LABOR_ID", -1)
        
        binding.tvHeaderTitle.text = "Historial de ${intent.getStringExtra("LABOR_NOMBRE") ?: "Siembra"}"

        binding.btnBack.setOnClickListener { finish() }
        binding.rvLabores.layoutManager = LinearLayoutManager(this)

        binding.fabAddNewRecord.setOnClickListener {
            showAddNewLaborDialog()
        }

        binding.btnSync.setOnClickListener {
            it.animate().rotationBy(360f).setDuration(500).start()
            loadData()
        }

        loadData()
    }

    private fun showAddNewLaborDialog() {
        val dialog = BottomSheetDialog(this)
        val dialogBinding = DialogAddLaborBinding.inflate(layoutInflater)
        dialog.setContentView(dialogBinding.root)
        dialogBindingForPhoto = dialogBinding
        currentImageUri = null

        val laborNombre = intent.getStringExtra("LABOR_NOMBRE") ?: "Labor"
        dialogBinding.tvFormLabel.text = "DETALLES: ${laborNombre.uppercase()}"

        // Configuración de Fecha
        val cal = Calendar.getInstance()
        val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        dialogBinding.etFecha.setText(sdf.format(cal.time))

        // --- ADVERTENCIA PARA COSECHA TEMPRANA ---
        if (laborId == 9 || laborNombre.contains("Cosecha", ignoreCase = true)) {
            val sowingDate = laborsHistory.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }?.fecha_realizacion
                             ?: cultivoRelacionado?.fecha_siembra
            
            val expectedDays = catalogoCultivo?.dias_a_cosecha_promedio
            
            if (sowingDate != null && expectedDays != null && expectedDays > 0) {
                val todayMillis = System.currentTimeMillis()
                val sowingMillis = sowingDate * 1000
                val diffMillis = todayMillis - sowingMillis
                val daysPassed = diffMillis / (1000 * 60 * 60 * 24)
                
                if (daysPassed < (expectedDays * 0.75)) {
                    val progress = (daysPassed.toDouble() / expectedDays * 100).toInt()
                    AlertDialog.Builder(this)
                        .setTitle("⚠️ ¡Advertencia de Cosecha!")
                        .setMessage("Atención: Solo han pasado $daysPassed días desde la siembra (aproximadamente un $progress% del ciclo esperado de $expectedDays días).\n\n¿Estás seguro de que deseas registrar la cosecha tan pronto?")
                        .setPositiveButton("Sí, continuar", null)
                        .setNegativeButton("Cancelar") { _, _ -> dialog.dismiss() }
                        .show()
                }
            }
        }

        dialogBinding.etFecha.setOnClickListener {
            DatePickerDialog(this, { _, year, month, day ->
                cal.set(year, month, day)
                dialogBinding.etFecha.setText(sdf.format(cal.time))
            }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH)).show()
        }

        // Evidencia Fotográfica
        dialogBinding.cardFotoLabor.setOnClickListener {
            val options = arrayOf("Tomar Foto", "Elegir de Galería")
            androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("Seleccionar Imagen")
                .setItems(options) { _, which ->
                    when (which) {
                        0 -> {
                            try {
                                val photoFile = java.io.File(getExternalFilesDir(android.os.Environment.DIRECTORY_PICTURES), "LABOR_EDIT_${System.currentTimeMillis()}.jpg")
                                cameraImageUri = androidx.core.content.FileProvider.getUriForFile(this, "${packageName}.fileprovider", photoFile)
                                takePhotoLauncher.launch(cameraImageUri!!)
                            } catch (e: Exception) {
                                Toast.makeText(this@LaborTypeDetailActivity, "Error al abrir la cámara", Toast.LENGTH_SHORT).show()
                            }
                        }
                        1 -> pickImageLauncher.launch("image/*")
                    }
                }
                .show()
        }
        dialogBinding.btnRemoveFoto.setOnClickListener {
            currentImageUri = null
            dialogBinding.ivFotoLabor.setImageResource(R.drawable.uploap)
            dialogBinding.ivFotoLabor.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            dialogBinding.ivFotoLabor.imageTintList = android.content.res.ColorStateList.valueOf(Color.parseColor("#D1D5DB"))
            dialogBinding.btnRemoveFoto.visibility = View.GONE
        }

        // Lógica de cálculo de subtotal
        val watcher = object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: android.text.Editable?) {
                val vCant = dialogBinding.etVaronCant.text.toString().toIntOrNull() ?: 0
                val vPago = dialogBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0
                val mCant = dialogBinding.etMujerCant.text.toString().toIntOrNull() ?: 0
                val mPago = dialogBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0
                val maq = dialogBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
                
                val total = (vCant * vPago) + (mCant * mPago) + maq
                dialogBinding.tvLaborSubtotal.text = "S/ ${String.format("%.2f", total)}"
            }
        }

        dialogBinding.etVaronCant.addTextChangedListener(watcher)
        dialogBinding.etVaronCosto.addTextChangedListener(watcher)
        dialogBinding.etMujerCant.addTextChangedListener(watcher)
        dialogBinding.etMujerCosto.addTextChangedListener(watcher)
        dialogBinding.etCostoMaq.addTextChangedListener(watcher)

        // --- LÓGICA DE RESTRICCIÓN DE FECHAS PARA NUEVO REGISTRO ---
        var minDateMillis: Long = 0
        terrenoRelacionado?.fecha_alquiler?.let { minDateMillis = it * 1000 }

        val currentCatId = laborId // ID de la categoría (Siembra, Riego, etc.)
        val historyForRestrictions = laborsHistory.filter { it.catalogo_labor_id != 10 }

        when {
            currentCatId == 1 -> { // Preparación
                val lastPrep = historyForRestrictions.filter { it.catalogo_labor_id == 1 }.maxByOrNull { it.fecha_realizacion }
                lastPrep?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
            }
            currentCatId == 2 -> { // Siembra
                val lastPrep = historyForRestrictions.filter { it.catalogo_labor_id == 1 }.maxByOrNull { it.fecha_realizacion }
                lastPrep?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
                
                val primeraSiembra = historyForRestrictions.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                primeraSiembra?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
            }
            else -> { // Mantenimiento (3+) y Cosecha (9)
                val sowingLabor = historyForRestrictions.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                val sowingDate = sowingLabor?.fecha_realizacion 
                                 ?: cultivoRelacionado?.fecha_siembra 
                                 ?: cultivoRelacionado?.fecha_planificada
                
                sowingDate?.let { minDateMillis = maxOf(minDateMillis, it * 1000) }
                
                val ultimaLaborReal = historyForRestrictions.maxByOrNull { it.fecha_realizacion }
                if (ultimaLaborReal != null) {
                    minDateMillis = maxOf(minDateMillis, ultimaLaborReal.fecha_realizacion * 1000)
                }
            }
        }

        if (minDateMillis > 0) {
            dialogBinding.tvLabelFecha.text = "Fecha de realización (Mínimo: ${sdf.format(Date(minDateMillis))})"
        }

        dialogBinding.etFecha.setOnClickListener {
            val datePickerDialog = DatePickerDialog(this, { _, year, month, day ->
                cal.set(year, month, day)
                dialogBinding.etFecha.setText(sdf.format(cal.time))
            }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH))
            
            if (minDateMillis > 0) datePickerDialog.datePicker.minDate = minDateMillis
            datePickerDialog.datePicker.maxDate = System.currentTimeMillis()
            datePickerDialog.show()
        }

        dialogBinding.btnCancel.setOnClickListener { dialog.dismiss() }
        
        dialogBinding.btnAccept.setOnClickListener {
            val fechaSeleccionada = cal.timeInMillis / 1000
            
            if (minDateMillis > 0 && cal.timeInMillis < minDateMillis) {
                Toast.makeText(this, "⚠️ La fecha no puede ser anterior al límite permitido", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val obs = dialogBinding.etDescripcion.text.toString()
            val maqCost = dialogBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
            val vCant = dialogBinding.etVaronCant.text.toString().toIntOrNull() ?: 0
            val vPago = dialogBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0
            val mCant = dialogBinding.etMujerCant.text.toString().toIntOrNull() ?: 0
            val mPago = dialogBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0

            lifecycleScope.launch {
                val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
                val userId = sharedPref.getInt("USER_ID", -1)
                val finalUserId = if (userId == -1) null else userId

                val nuevaLabor = LaborRealizadaEntity(
                    cultivo_id = cultivoId,
                    catalogo_labor_id = laborId,
                    fecha_realizacion = cal.timeInMillis / 1000,
                    costo_mano_obra_total = (vCant * vPago) + (mCant * mPago),
                    costo_maquinaria_total = maqCost,
                    observaciones = obs,
                    foto_path = currentImageUri?.toString(),
                    creado_por_usuario_id = finalUserId,
                    sincronizado = 0
                )
                
                val idInsertada = db.assetDao().insertLabor(nuevaLabor).toInt()
                
                if (vCant > 0) {
                    db.assetDao().insertManoObra(ManoObraEntity(
                        labor_realizada_id = idInsertada,
                        tipo_id = 1,
                        cantidad_trabajadores = vCant,
                        dias_trabajados = 1,
                        costo_por_dia = vPago,
                        subtotal = vCant * vPago
                    ))
                }
                
                if (mCant > 0) {
                    db.assetDao().insertManoObra(ManoObraEntity(
                        labor_realizada_id = idInsertada,
                        tipo_id = 2,
                        cantidad_trabajadores = mCant,
                        dias_trabajados = 1,
                        costo_por_dia = mPago,
                        subtotal = mCant * mPago
                    ))
                }

                loadData()
                dialog.dismiss()
                Toast.makeText(this@LaborTypeDetailActivity, "¡Labor registrada!", Toast.LENGTH_SHORT).show()
                
                // --- DIÁLOGO DE SEGUIMIENTO ---
                val isCosecha = laborId == 9 || laborNombre.contains("Cosecha", ignoreCase = true)
                
                val builder = AlertDialog.Builder(this@LaborTypeDetailActivity)
                    .setTitle("Registro Exitoso")
                    .setMessage(if (isCosecha) "¿Deseas agregar insumos o registrar el detalle de la cosecha ahora?" 
                               else "¿Deseas agregar insumos a esta labor ahora?")
                
                builder.setPositiveButton("Agregar Insumos") { _, _ ->
                    showAddInsumoDialog(idInsertada) { loadData() }
                }

                if (isCosecha) {
                    builder.setNeutralButton("Registrar Cosecha") { _, _ ->
                        val intent = android.content.Intent(this@LaborTypeDetailActivity, RegisterCosechaActivity::class.java)
                        intent.putExtra("CULTIVO_ID", cultivoId)
                        intent.putExtra("LABOR_ID", idInsertada)
                        startActivity(intent)
                    }
                }

                builder.setNegativeButton("Después", null)
                builder.show()
            }
        }

        dialog.show()
    }

    private fun loadData() {
        lifecycleScope.launch {
            val fullHistory = db.assetDao().getLaboresByCultivo(cultivoId)
            laborsHistory = fullHistory
            
            cultivoRelacionado = db.assetDao().getCultivoById(cultivoId)
            cultivoRelacionado?.let {
                terrenoRelacionado = db.assetDao().getTerrenoById(it.terreno_id)
                catalogoCultivo = db.assetDao().getCatalogoCultivoById(it.catalogo_cultivo_id)
            }

            val labores = fullHistory
                .filter { it.catalogo_labor_id == laborId }
                .sortedByDescending { it.fecha_realizacion }
            
            var granTotal = 0.0
            labores.forEach { labor ->
                val insumos = db.assetDao().getInsumosByLaborId(labor.id)
                granTotal += labor.costo_mano_obra_total + labor.costo_maquinaria_total + 
                             insumos.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
            }

            binding.tvHeaderLabel.text = "${labores.size} registros"
            binding.tvTotalInversion.text = "S/ ${String.format("%.2f", granTotal)}"
            
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            binding.tvFechaActualizacion.text = if (labores.isNotEmpty()) sdf.format(Date(labores[0].fecha_realizacion * 1000)) else sdf.format(Date())
            
            binding.rvLabores.adapter = LaboresAdapter(labores)
        }
    }

    private fun showManageLaborBottomSheet(labor: LaborRealizadaEntity) {
        val dialog = BottomSheetDialog(this)
        val dialogBinding = DialogManageLaborBinding.inflate(layoutInflater)
        dialog.setContentView(dialogBinding.root)

        fun refreshManageUI() {
            lifecycleScope.launch {
                val updatedLabor = db.assetDao().getLaboresByCultivo(labor.cultivo_id).find { it.id == labor.id } ?: labor
                val insumos = db.assetDao().getInsumosByLaborId(labor.id)
                
                dialogBinding.tvLaborSummary.text = if (updatedLabor.observaciones.isNullOrBlank()) "Sin observaciones" else updatedLabor.observaciones
                
                dialogBinding.rvManageInsumos.layoutManager = LinearLayoutManager(this@LaborTypeDetailActivity)
                dialogBinding.rvManageInsumos.adapter = object : RecyclerView.Adapter<RecyclerView.ViewHolder>() {
                    inner class InsumoViewHolder(val itemBinding: ItemInsumoRowBinding) : RecyclerView.ViewHolder(itemBinding.root)
                    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = InsumoViewHolder(ItemInsumoRowBinding.inflate(LayoutInflater.from(parent.context), parent, false))
                    override fun getItemCount() = insumos.size
                    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
                        val ins = insumos[position]
                        val h = holder as InsumoViewHolder
                        lifecycleScope.launch {
                            val cat = db.assetDao().getCatalogoInsumoById(ins.catalogo_insumo_id)
                            h.itemBinding.tvInsumoNombre.text = cat?.nombre ?: "Insumo"
                            h.itemBinding.tvInsumoCant.text = "${ins.cantidad} ${cat?.unidad_medida ?: ""}"
                            h.itemBinding.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.costo_unitario) + ins.costo_flete)}"
                            
                            h.itemBinding.btnEditInsumo.visibility = View.VISIBLE
                            h.itemBinding.btnRemoveInsumo.visibility = View.VISIBLE
                            
                            h.itemBinding.btnEditInsumo.setOnClickListener {
                                showEditInsumoDialog(ins) { 
                                    refreshManageUI()
                                    loadData() 
                                }
                            }
                            
                            h.itemBinding.btnRemoveInsumo.setOnClickListener {
                                AlertDialog.Builder(this@LaborTypeDetailActivity)
                                    .setTitle("Eliminar insumo")
                                    .setMessage("¿Deseas quitar este insumo del registro?")
                                    .setPositiveButton("Eliminar") { _, _ ->
                                        lifecycleScope.launch {
                                            db.assetDao().deleteInsumoUsado(ins)
                                            refreshManageUI()
                                            loadData()
                                        }
                                    }
                                    .setNegativeButton("Cancelar", null)
                                    .show()
                            }
                        }
                    }
                }
            }
        }

        dialogBinding.btnEditLaborInfo.setOnClickListener {
            showEditLaborDialog(labor) {
                refreshManageUI()
                loadData()
            }
        }

        dialogBinding.btnAddInsumo.setOnClickListener {
            showAddInsumoDialog(labor.id) {
                refreshManageUI()
                loadData()
            }
        }

        dialogBinding.btnFinishManage.setOnClickListener { dialog.dismiss() }

        refreshManageUI()
        dialog.show()
    }

    private fun showEditInsumoDialog(insumo: InsumoUsadoEntity, onUpdated: () -> Unit) {
        val dialogBinding = ItemInsumoFormBinding.inflate(layoutInflater)
        dialogBinding.btnDeleteInsumo.visibility = View.GONE
        
        // Cargar datos actuales
        lifecycleScope.launch {
            val cat = db.assetDao().getCatalogoInsumoById(insumo.catalogo_insumo_id)
            dialogBinding.etNombreInsumo.setText(cat?.nombre)
            dialogBinding.etInsumoCantidad.setText(insumo.cantidad.toString())
            dialogBinding.etUnidadMedida.setText(cat?.unidad_medida)
            dialogBinding.etInsumoPrecio.setText(insumo.costo_unitario.toString())
            dialogBinding.tvInsumoTotal.text = "Total: S/ ${String.format("%.2f", (insumo.cantidad * insumo.costo_unitario) + insumo.costo_flete)}"
        }

        AlertDialog.Builder(this)
            .setTitle("Editar Insumo")
            .setView(dialogBinding.root)
            .setPositiveButton("Actualizar") { _, _ ->
                val cant = dialogBinding.etInsumoCantidad.text.toString().toDoubleOrNull() ?: insumo.cantidad
                val precio = dialogBinding.etInsumoPrecio.text.toString().toDoubleOrNull() ?: insumo.costo_unitario
                lifecycleScope.launch {
                    val updated = insumo.copy(cantidad = cant, costo_unitario = precio)
                    db.assetDao().updateInsumoUsado(updated)
                    onUpdated()
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun showAddInsumoDialog(laborId: Int, onAdded: () -> Unit) {
        val dialogBinding = ItemInsumoFormBinding.inflate(layoutInflater)
        dialogBinding.btnDeleteInsumo.visibility = View.GONE
        
        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoInsumos()
            val nombres = catalogo.map { it.nombre }
            val adapter = android.widget.ArrayAdapter(this@LaborTypeDetailActivity, android.R.layout.simple_dropdown_item_1line, nombres)
            dialogBinding.etNombreInsumo.setAdapter(adapter)
            
            dialogBinding.etNombreInsumo.setOnItemClickListener { _, _, position, _ ->
                val selectedName = adapter.getItem(position)
                val selected = catalogo.find { it.nombre == selectedName }
                if (selected != null) {
                    dialogBinding.etUnidadMedida.setText(selected.unidad_medida)
                }
            }
        }

        AlertDialog.Builder(this)
            .setTitle("Agregar Insumo")
            .setView(dialogBinding.root)
            .setPositiveButton("Agregar") { _, _ ->
                lifecycleScope.launch {
                    val nombre = dialogBinding.etNombreInsumo.text.toString()
                    val cant = dialogBinding.etInsumoCantidad.text.toString().toDoubleOrNull() ?: 0.0
                    val precio = dialogBinding.etInsumoPrecio.text.toString().toDoubleOrNull() ?: 0.0
                    
                    val catInsumos = db.assetDao().getCatalogoInsumos()
                    var catId = catInsumos.find { it.nombre.equals(nombre, true) }?.id
                    
                    if (catId == null && nombre.isNotBlank()) {
                        catId = db.assetDao().insertCatalogoInsumo(CatalogoInsumoEntity(
                            nombre = nombre,
                            categoria = "General",
                            unidad_medida = dialogBinding.etUnidadMedida.text.toString(),
                            descripcion = ""
                        )).toInt()
                    }

                    if (catId != null) {
                        val nuevoInsumo = InsumoUsadoEntity(
                            labor_id = laborId,
                            catalogo_insumo_id = catId,
                            proveedor_id = null,
                            cantidad = cant,
                            costo_unitario = precio,
                            costo_flete = 0.0
                        )
                        db.assetDao().insertInsumoUsado(nuevoInsumo)
                        onAdded()
                    }
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun showEditLaborDialog(labor: LaborRealizadaEntity, onUpdated: () -> Unit) {
        val dialog = BottomSheetDialog(this)
        val dialogBinding = DialogAddLaborBinding.inflate(layoutInflater)
        dialog.setContentView(dialogBinding.root)
        dialogBindingForPhoto = dialogBinding
        currentImageUri = if (labor.foto_path != null) android.net.Uri.parse(labor.foto_path) else null

        val laborNombre = intent.getStringExtra("LABOR_NOMBRE") ?: "Labor"
        dialogBinding.tvFormLabel.text = "EDITAR: ${laborNombre.uppercase()}"

        // Cargar datos actuales
        val cal = Calendar.getInstance().apply { timeInMillis = labor.fecha_realizacion * 1000 }
        val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        dialogBinding.etFecha.setText(sdf.format(cal.time))
        dialogBinding.etDescripcion.setText(labor.observaciones)
        dialogBinding.etCostoMaq.setText(labor.costo_maquinaria_total.toString())

        lifecycleScope.launch {
            val moList = db.assetDao().getManoObraByLabor(labor.id)
            val vMo = moList.find { it.tipo_id == 1 }
            val mMo = moList.find { it.tipo_id == 2 }
            
            dialogBinding.etVaronCant.setText(vMo?.cantidad_trabajadores?.toString() ?: "0")
            dialogBinding.etVaronCosto.setText(vMo?.costo_por_dia?.toString() ?: "0.00")
            dialogBinding.etMujerCant.setText(mMo?.cantidad_trabajadores?.toString() ?: "0")
            dialogBinding.etMujerCosto.setText(mMo?.costo_por_dia?.toString() ?: "0.00")
            
            val subtotal = labor.costo_mano_obra_total + labor.costo_maquinaria_total
            dialogBinding.tvLaborSubtotal.text = "S/ ${String.format("%.2f", subtotal)}"
        }

        if (labor.foto_path != null) {
            Glide.with(this).load(labor.foto_path).centerCrop().into(dialogBinding.ivFotoLabor)
            dialogBinding.ivFotoLabor.imageTintList = null
            dialogBinding.btnRemoveFoto.visibility = View.VISIBLE
        }

        dialogBinding.etFecha.setOnClickListener {
            DatePickerDialog(this, { _, year, month, day ->
                cal.set(year, month, day)
                dialogBinding.etFecha.setText(sdf.format(cal.time))
            }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH)).show()
        }

        dialogBinding.cardFotoLabor.setOnClickListener {
            val options = arrayOf("Tomar Foto", "Elegir de Galería")
            androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("Seleccionar Imagen")
                .setItems(options) { _, which ->
                    when (which) {
                        0 -> {
                            try {
                                val photoFile = java.io.File(getExternalFilesDir(android.os.Environment.DIRECTORY_PICTURES), "LABOR_EDIT_${System.currentTimeMillis()}.jpg")
                                cameraImageUri = androidx.core.content.FileProvider.getUriForFile(this, "${packageName}.fileprovider", photoFile)
                                takePhotoLauncher.launch(cameraImageUri!!)
                            } catch (e: Exception) {
                                Toast.makeText(this@LaborTypeDetailActivity, "Error al abrir la cámara", Toast.LENGTH_SHORT).show()
                            }
                        }
                        1 -> pickImageLauncher.launch("image/*")
                    }
                }
                .show()
        }
        dialogBinding.btnRemoveFoto.setOnClickListener {
            currentImageUri = null
            dialogBinding.ivFotoLabor.setImageResource(R.drawable.uploap)
            dialogBinding.ivFotoLabor.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            dialogBinding.ivFotoLabor.imageTintList = android.content.res.ColorStateList.valueOf(Color.parseColor("#D1D5DB"))
            dialogBinding.btnRemoveFoto.visibility = View.GONE
        }

        val watcher = object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: android.text.Editable?) {
                val vCant = dialogBinding.etVaronCant.text.toString().toIntOrNull() ?: 0
                val vPago = dialogBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0
                val mCant = dialogBinding.etMujerCant.text.toString().toIntOrNull() ?: 0
                val mPago = dialogBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0
                val maq = dialogBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
                val total = (vCant * vPago) + (mCant * mPago) + maq
                dialogBinding.tvLaborSubtotal.text = "S/ ${String.format("%.2f", total)}"
            }
        }
        dialogBinding.etVaronCant.addTextChangedListener(watcher)
        dialogBinding.etVaronCosto.addTextChangedListener(watcher)
        dialogBinding.etMujerCant.addTextChangedListener(watcher)
        dialogBinding.etMujerCosto.addTextChangedListener(watcher)
        dialogBinding.etCostoMaq.addTextChangedListener(watcher)

        // --- LÓGICA DE RESTRICCIÓN DE FECHAS PARA NUEVO REGISTRO ---
        var minDateMillis: Long = 0
        terrenoRelacionado?.fecha_alquiler?.let { minDateMillis = it * 1000 }

        val currentCatId = laborId // ID de la categoría (Siembra, Riego, etc.)
        val historyForRestrictions = laborsHistory.filter { it.catalogo_labor_id != 10 }

        when {
            currentCatId == 1 -> { // Preparación
                val lastPrep = historyForRestrictions.filter { it.catalogo_labor_id == 1 }.maxByOrNull { it.fecha_realizacion }
                lastPrep?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
            }
            currentCatId == 2 -> { // Siembra
                val lastPrep = historyForRestrictions.filter { it.catalogo_labor_id == 1 }.maxByOrNull { it.fecha_realizacion }
                lastPrep?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
                
                val primeraSiembra = historyForRestrictions.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                primeraSiembra?.let { minDateMillis = maxOf(minDateMillis, it.fecha_realizacion * 1000) }
            }
            else -> { // Mantenimiento (3+) y Cosecha (9)
                val sowingLabor = historyForRestrictions.filter { it.catalogo_labor_id == 2 }.minByOrNull { it.fecha_realizacion }
                val sowingDate = sowingLabor?.fecha_realizacion 
                                 ?: cultivoRelacionado?.fecha_siembra 
                                 ?: cultivoRelacionado?.fecha_planificada
                
                sowingDate?.let { minDateMillis = maxOf(minDateMillis, it * 1000) }
                
                val ultimaLaborReal = historyForRestrictions.maxByOrNull { it.fecha_realizacion }
                if (ultimaLaborReal != null) {
                    minDateMillis = maxOf(minDateMillis, ultimaLaborReal.fecha_realizacion * 1000)
                }
            }
        }

        if (minDateMillis > 0) {
            dialogBinding.tvLabelFecha.text = "Fecha de realización (Mínimo: ${sdf.format(Date(minDateMillis))})"
        }

        dialogBinding.etFecha.setOnClickListener {
            val datePickerDialog = DatePickerDialog(this, { _, year, month, day ->
                cal.set(year, month, day)
                dialogBinding.etFecha.setText(sdf.format(cal.time))
            }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH))
            
            if (minDateMillis > 0) datePickerDialog.datePicker.minDate = minDateMillis
            datePickerDialog.datePicker.maxDate = System.currentTimeMillis()
            datePickerDialog.show()
        }

        dialogBinding.btnCancel.setOnClickListener { dialog.dismiss() }
        
        dialogBinding.btnAccept.setOnClickListener {
            val fechaSeleccionada = cal.timeInMillis / 1000
            
            if (minDateMillis > 0 && cal.timeInMillis < minDateMillis) {
                Toast.makeText(this, "⚠️ La fecha no puede ser anterior al límite permitido", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val obs = dialogBinding.etDescripcion.text.toString()
            val maqCost = dialogBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
            val vCant = dialogBinding.etVaronCant.text.toString().toIntOrNull() ?: 0
            val vPago = dialogBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0
            val mCant = dialogBinding.etMujerCant.text.toString().toIntOrNull() ?: 0
            val mPago = dialogBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0

            lifecycleScope.launch {
                var finalFotoPath = labor.foto_path
                
                // Si la imagen cambió y es una URI temporal, guardarla localmente
                currentImageUri?.let { uri ->
                    if (uri.toString() != labor.foto_path) {
                        val sharedPref = getSharedPreferences("agrosys_prefs", android.content.Context.MODE_PRIVATE)
                        val userId = sharedPref.getInt("USER_ID", -1)
                        val userName = sharedPref.getString("USER_NAME", "Usuario") ?: "Usuario"

                        val savedPath = com.sigcpa.agrosys.util.FileUtils.saveImageLocally(
                            this@LaborTypeDetailActivity,
                            uri,
                            userId,
                            userName,
                            com.sigcpa.agrosys.util.FileUtils.getLaboresFolder(laborNombre),
                            "labor_edit_${labor.id}_${System.currentTimeMillis()}.jpg"
                        )
                        finalFotoPath = savedPath
                    }
                }

                val laborActualizada = labor.copy(
                    fecha_realizacion = cal.timeInMillis / 1000,
                    costo_mano_obra_total = (vCant * vPago) + (mCant * mPago),
                    costo_maquinaria_total = maqCost,
                    observaciones = obs,
                    foto_path = finalFotoPath,
                    updated_at = System.currentTimeMillis() / 1000
                )
                db.assetDao().updateLabor(laborActualizada)
                
                db.assetDao().deleteManoObraByLabor(labor.id)
                if (vCant > 0) {
                    db.assetDao().insertManoObra(ManoObraEntity(labor_realizada_id = labor.id, tipo_id = 1, cantidad_trabajadores = vCant, dias_trabajados = 1, costo_por_dia = vPago, subtotal = vCant * vPago))
                }
                if (mCant > 0) {
                    db.assetDao().insertManoObra(ManoObraEntity(labor_realizada_id = labor.id, tipo_id = 2, cantidad_trabajadores = mCant, dias_trabajados = 1, costo_por_dia = mPago, subtotal = mCant * mPago))
                }

                onUpdated()
                dialog.dismiss()
                Toast.makeText(this@LaborTypeDetailActivity, "¡Registro actualizado!", Toast.LENGTH_SHORT).show()
            }
        }
        dialog.show()
    }


    inner class LaboresAdapter(private val lista: List<LaborRealizadaEntity>) : RecyclerView.Adapter<LaboresAdapter.ViewHolder>() {
        
        private val expandedPositions = mutableSetOf<Int>()

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = ViewHolder(ItemLaborDetalleBinding.inflate(LayoutInflater.from(parent.context), parent, false))
        
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            val isExpanded = expandedPositions.contains(position)
            
            // Ocultar timeline para este modelo
            holder.binding.markerContainer.visibility = View.GONE
            holder.binding.timelineLine.visibility = View.GONE
            val cardParams = holder.binding.cardContainer.layoutParams as android.view.ViewGroup.MarginLayoutParams
            cardParams.marginStart = 0
            cardParams.leftMargin = 0
            holder.binding.cardContainer.layoutParams = cardParams

            holder.binding.tvLaborBadge.text = intent.getStringExtra("LABOR_NOMBRE") ?: "Labor"
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            holder.binding.tvLaborFecha.text = sdf.format(Date(item.fecha_realizacion * 1000))

            // Cargar imagen si existe (Soporte para URI y File Path)
            if (!item.foto_path.isNullOrBlank()) {
                val imageSource: Any = if (item.foto_path.startsWith("content://") || item.foto_path.startsWith("file://")) {
                    android.net.Uri.parse(item.foto_path)
                } else {
                    java.io.File(item.foto_path)
                }

                holder.binding.ivLaborFoto.visibility = View.VISIBLE
                holder.binding.vGradientOverlay.visibility = View.VISIBLE
                holder.binding.flHeaderContainer.background = null // Quitar fondo verde si hay imagen
                com.bumptech.glide.Glide.with(holder.itemView.context)
                    .load(imageSource)
                    .centerCrop()
                    .into(holder.binding.ivLaborFoto)
            } else {
                holder.binding.ivLaborFoto.visibility = View.GONE
                holder.binding.vGradientOverlay.visibility = View.GONE
                // Restaurar fondo verde si no hay imagen
                holder.binding.flHeaderContainer.setBackgroundColor(android.graphics.Color.parseColor("#15803D"))
            }

            // Configuración para Historial
            holder.binding.tvVerMasDetalles.text = if (isExpanded) "••• Cerrar" else "••• Detalle"
            
            lifecycleScope.launch {
                val insumos = db.assetDao().getInsumosByLaborId(item.id)
                val moList = db.assetDao().getManoObraByLabor(item.id)
                val totalPers = moList.sumOf { it.cantidad_trabajadores }

                val costoInsumos = insumos.sumOf { (it.cantidad * it.costo_unitario) + it.costo_flete }
                val totalSesion = item.costo_mano_obra_total + item.costo_maquinaria_total + costoInsumos
                
                holder.binding.tvLaborCostos.text = "S/ ${String.format("%.2f", totalSesion)}"
                val labelInsumos = if (insumos.size == 1) "1 insumo utilizado" else "${insumos.size} insumos utilizados"
                holder.binding.tvLaborInsumosCount.text = labelInsumos
                holder.binding.tvManoObraResumen.text = "Mano de obra $totalPers pers."
                holder.binding.tvManoObraResumen.visibility = View.VISIBLE
                holder.binding.tvLaborManoObraCount.text = "$totalPers"

                holder.binding.llInsumosDetalle.removeAllViews()
                holder.binding.llInsumosDetalle.visibility = if (isExpanded) View.VISIBLE else View.GONE
                
                if (isExpanded) {
                    // Nota/Descripción
                    if (!item.observaciones.isNullOrBlank()) {
                        val noteView = TextView(holder.itemView.context).apply {
                            text = "Nota: ${item.observaciones}"
                            textSize = 13f
                            setPadding(24, 12, 24, 12)
                            setTextColor(Color.parseColor("#92400E"))
                            val background = android.graphics.drawable.GradientDrawable().apply {
                                setColor(Color.parseColor("#FEF3C7"))
                                cornerRadius = 8f * holder.itemView.context.resources.displayMetrics.density
                            }
                            setBackground(background)
                            val params = android.widget.LinearLayout.LayoutParams(
                                android.widget.LinearLayout.LayoutParams.MATCH_PARENT,
                                android.widget.LinearLayout.LayoutParams.WRAP_CONTENT
                            )
                            params.setMargins(0, 8, 0, 16)
                            layoutParams = params
                        }
                        holder.binding.llInsumosDetalle.addView(noteView)
                    }

                    // Mano de Obra
                    if (item.costo_mano_obra_total > 0) {
                        val moTitle = TextView(holder.itemView.context).apply {
                            text = "Mano de Obra"
                            textSize = 14f
                            setPadding(0, 16, 0, 8)
                            setTypeface(null, android.graphics.Typeface.BOLD)
                            setTextColor(Color.parseColor("#718096"))
                        }
                        holder.binding.llInsumosDetalle.addView(moTitle)
                        
                        moList.forEach { mo ->
                            val moRow = TextView(holder.itemView.context).apply {
                                val tipo = if (mo.tipo_id == 1) "Varones" else "Mujeres"
                                text = "  • $tipo: ${mo.cantidad_trabajadores} pers. x ${mo.dias_trabajados} d.  →  S/ ${String.format("%.2f", mo.subtotal)}"
                                textSize = 13f
                                setPadding(12, 4, 0, 4)
                                setTextColor(Color.parseColor("#4A5568"))
                            }
                            holder.binding.llInsumosDetalle.addView(moRow)
                        }
                    }

                    // Insumos
                    if (insumos.isNotEmpty()) {
                        val insTitle = TextView(holder.itemView.context).apply {
                            text = "Insumos Detallados"
                            textSize = 14f
                            setPadding(0, 16, 0, 8)
                            setTypeface(null, android.graphics.Typeface.BOLD)
                            setTextColor(Color.parseColor("#718096"))
                        }
                        holder.binding.llInsumosDetalle.addView(insTitle)

                        insumos.forEach { ins ->
                            val cat = db.assetDao().getCatalogoInsumoById(ins.catalogo_insumo_id)
                            val container = android.widget.LinearLayout(holder.itemView.context).apply {
                                orientation = android.widget.LinearLayout.HORIZONTAL
                                setPadding(12, 6, 0, 6)
                                weightSum = 3f
                            }
                            
                            val nameView = TextView(holder.itemView.context).apply {
                                text = cat?.nombre ?: "Insumo"
                                layoutParams = android.widget.LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1.2f)
                                textSize = 13f
                                setTextColor(Color.parseColor("#2D3748"))
                            }
                            
                            val qtyView = TextView(holder.itemView.context).apply {
                                val unidad = cat?.unidad_medida ?: ""
                                val cantStr = if (ins.cantidad % 1.0 == 0.0) ins.cantidad.toInt().toString() else ins.cantidad.toString()
                                text = "$cantStr $unidad"
                                layoutParams = android.widget.LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 0.8f)
                                textSize = 13f
                                gravity = android.view.Gravity.CENTER
                                setTextColor(Color.parseColor("#718096"))
                            }
                            
                            val priceView = TextView(holder.itemView.context).apply {
                                text = "S/ ${String.format("%.2f", (ins.cantidad * ins.costo_unitario) + ins.costo_flete)}"
                                layoutParams = android.widget.LinearLayout.LayoutParams(0, ViewGroup.LayoutParams.WRAP_CONTENT, 1.0f)
                                textSize = 13f
                                gravity = android.view.Gravity.END
                                setTypeface(null, android.graphics.Typeface.BOLD)
                                setTextColor(Color.parseColor("#2D3748"))
                            }
                            
                            container.addView(nameView)
                            container.addView(qtyView)
                            container.addView(priceView)
                            holder.binding.llInsumosDetalle.addView(container)
                        }
                    }

                    // Total Sesión final
                    val totalView = TextView(holder.itemView.context).apply {
                        text = "Total Sesión: S/ ${String.format("%.2f", totalSesion)}"
                        textSize = 15f
                        setTextColor(Color.parseColor("#1A202C"))
                        setTypeface(null, android.graphics.Typeface.BOLD)
                        gravity = android.view.Gravity.END
                        setPadding(0, 24, 0, 0)
                    }
                    holder.binding.llInsumosDetalle.addView(totalView)

                    // --- DETALLE DE COSECHA (PRODUCCIÓN) ---
                    if (laborId == 9) {
                        val cosechas = db.assetDao().getCosechasByCultivo(cultivoId)
                        // Buscar específicamente la cosecha vinculada a ESTE ID de labor
                        val cosechaVinculada = cosechas.find { it.labor_id == item.id }

                        if (cosechaVinculada != null) {
                            val harvestTitle = TextView(holder.itemView.context).apply {
                                text = "Producción Obtenida"
                                textSize = 14f
                                setPadding(0, 24, 0, 8)
                                setTypeface(null, android.graphics.Typeface.BOLD)
                                setTextColor(Color.parseColor("#059669")) // Verde esmeralda
                            }
                            holder.binding.llInsumosDetalle.addView(harvestTitle)

                            val cosRow = TextView(holder.itemView.context).apply {
                                val cos = cosechaVinculada
                                val cantStr = if (cos.cantidad_kg % 1.0 == 0.0) cos.cantidad_kg.toInt().toString() else cos.cantidad_kg.toString()
                                val unidad = cos.unidad_medida ?: "kg"
                                val calidadStr = if (cos.calidad != null) " - Calidad: ${cos.calidad}" else ""
                                text = "  • $cantStr $unidad$calidadStr"
                                textSize = 13f
                                setPadding(12, 4, 0, 4)
                                setTextColor(Color.parseColor("#065F46"))
                            }
                            holder.binding.llInsumosDetalle.addView(cosRow)
                            
                            val btnEditCos = TextView(holder.itemView.context).apply {
                                text = "✎ Editar cantidad cosechada"
                                textSize = 13f
                                setTextColor(Color.parseColor("#059669"))
                                setTypeface(null, android.graphics.Typeface.BOLD)
                                setPadding(12, 16, 12, 16)
                                setOnClickListener {
                                    val intent = android.content.Intent(holder.itemView.context, RegisterCosechaActivity::class.java)
                                    intent.putExtra("CULTIVO_ID", cultivoId)
                                    intent.putExtra("LABOR_ID", item.id)
                                    holder.itemView.context.startActivity(intent)
                                }
                            }
                            holder.binding.llInsumosDetalle.addView(btnEditCos)
                        } else {
                            // Si la labor existe pero no se ha registrado la cantidad aún
                            val btnAddCos = TextView(holder.itemView.context).apply {
                                text = "+ Registrar cantidad de cosecha"
                                textSize = 13f
                                setTextColor(Color.parseColor("#059669"))
                                setTypeface(null, android.graphics.Typeface.BOLD)
                                setPadding(12, 16, 12, 16)
                                setOnClickListener {
                                    val intent = android.content.Intent(holder.itemView.context, RegisterCosechaActivity::class.java)
                                    intent.putExtra("CULTIVO_ID", cultivoId)
                                    intent.putExtra("LABOR_ID", item.id)
                                    holder.itemView.context.startActivity(intent)
                                }
                            }
                            holder.binding.llInsumosDetalle.addView(btnAddCos)
                        }
                    }
                }
            }

            holder.binding.btnEditLabor.visibility = View.VISIBLE
            holder.binding.btnRemoveLabor.visibility = View.VISIBLE

            holder.binding.btnEditLabor.setOnClickListener {
                AlertDialog.Builder(holder.itemView.context)
                    .setTitle("Gestionar registro")
                    .setMessage("¿Deseas gestionar los detalles de este registro?")
                    .setPositiveButton("Gestionar") { _, _ ->
                        showManageLaborBottomSheet(item)
                    }
                    .setNegativeButton("Cancelar", null)
                    .show()
            }
            
            holder.binding.btnRemoveLabor.setOnClickListener {
                AlertDialog.Builder(holder.itemView.context)
                    .setTitle("Confirmar eliminación")
                    .setMessage("¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.")
                    .setPositiveButton("Eliminar") { _, _ ->
                        lifecycleScope.launch {
                            db.assetDao().deleteInsumosByLabor(item.id)
                            db.assetDao().deleteManoObraByLabor(item.id)
                            db.assetDao().deleteLabor(item)
                            loadData()
                            Toast.makeText(holder.itemView.context, "Registro eliminado correctamente", Toast.LENGTH_SHORT).show()
                        }
                    }
                    .setNegativeButton("Cancelar", null)
                    .show()
            }

            holder.binding.btnExpandAction.setOnClickListener {
                if (expandedPositions.contains(position)) {
                    expandedPositions.remove(position)
                } else {
                    expandedPositions.add(position)
                }
                notifyItemChanged(position)
            }
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemLaborDetalleBinding) : RecyclerView.ViewHolder(binding.root)
    }
}
