package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.net.Uri
import android.os.Bundle
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.FileProvider
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import com.google.android.material.textfield.TextInputEditText
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CompradorEntity
import com.sigcpa.agrosys.database.entities.CosechaEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.database.entities.VentaEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterVentaBinding
import com.sigcpa.agrosys.util.FileUtils
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class RegisterVentaActivity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterVentaBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var selectedCultivo: CultivoEntity? = null
    private var selectedCosecha: CosechaEntity? = null
    private var selectedComprador: CompradorEntity? = null
    
    private val calendar = Calendar.getInstance()
    private val dateFormat = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())

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
        binding = ActivityRegisterVentaBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupToolbar()
        loadInitialData()
        setupListeners()
    }

    private fun setupToolbar() {
        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        binding.toolbar.setNavigationOnClickListener { finish() }
    }

    private fun loadInitialData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val cultivos = db.assetDao().getCultivosByAgricultor(userId)
            
            // Necesitamos un mapa o clase helper para mostrar nombres de cultivos
            val cultivoNombres = cultivos.map { "Lote: ${it.nombre_lote ?: it.id.toString()}" }
            val cultivoAdapter = ArrayAdapter(this@RegisterVentaActivity, android.R.layout.simple_dropdown_item_1line, cultivoNombres)
            binding.actvCultivo.setAdapter(cultivoAdapter)

            binding.actvCultivo.setOnItemClickListener { _, _, position, _ ->
                selectedCultivo = cultivos[position]
                binding.actvCosecha.setText("", false)
                selectedCosecha = null
                loadCosechas(selectedCultivo!!.id)
            }

            loadCompradores()
        }
        
        binding.etFecha.setText(dateFormat.format(calendar.time))
        
        val tiposDoc = arrayOf("Recibo", "Boleta", "Factura", "Guía de Remisión")
        val adapterTipos = ArrayAdapter(this, android.R.layout.simple_dropdown_item_1line, tiposDoc)
        binding.actvTipoDoc.setAdapter(adapterTipos)
    }

    private fun loadCosechas(cultivoId: Int) {
        lifecycleScope.launch {
            val cosechas = db.assetDao().getCosechasByCultivo(cultivoId)
            val cosechaNombres = cosechas.map { "Lote: ${it.lote_codigo} (${it.cantidad_kg} Kg) - ${dateFormat.format(Date(it.fecha_cosecha * 1000))}" }
            val adapter = ArrayAdapter(this@RegisterVentaActivity, android.R.layout.simple_dropdown_item_1line, cosechaNombres)
            binding.actvCosecha.setAdapter(adapter)
            
            binding.actvCosecha.setOnItemClickListener { _, _, position, _ ->
                selectedCosecha = cosechas[position]
                binding.etCantidad.setText(selectedCosecha!!.cantidad_kg.toString())
            }
        }
    }

    private fun loadCompradores() {
        lifecycleScope.launch {
            val compradores = db.assetDao().getAllCompradores()
            val nombres = compradores.map { it.nombre }
            val adapter = ArrayAdapter(this@RegisterVentaActivity, android.R.layout.simple_dropdown_item_1line, nombres)
            binding.actvComprador.setAdapter(adapter)

            binding.actvComprador.setOnItemClickListener { _, _, position, _ ->
                selectedComprador = compradores[position]
            }
        }
    }

    private fun setupListeners() {
        binding.etFecha.setOnClickListener { showDatePicker() }
        
        binding.btnNuevoComprador.setOnClickListener { showAddCompradorDialog() }

        // Calcular total automáticamente
        val watcher = object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) { calculateTotal() }
            override fun afterTextChanged(s: android.text.Editable?) {}
        }
        binding.etCantidad.addTextChangedListener(watcher)
        binding.etPrecio.addTextChangedListener(watcher)

        setupPhotoLogic()
        binding.btnGuardarVenta.setOnClickListener { validateAndSave() }
    }

    private fun setupPhotoLogic() {
        binding.cardFotoVenta.setOnClickListener {
            val options = arrayOf("Tomar Foto", "Elegir de Galería")
            MaterialAlertDialogBuilder(this)
                .setTitle("Comprobante de Venta")
                .setItems(options) { _, which ->
                    when (which) {
                        0 -> openCamera()
                        1 -> pickImageLauncher.launch("image/*")
                    }
                }
                .show()
        }

        binding.btnRemoveFoto.setOnClickListener {
            selectedImageUri = null
            binding.ivFotoVenta.setImageResource(R.drawable.uploap)
            binding.ivFotoVenta.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            val padding = (64 * resources.displayMetrics.density).toInt()
            binding.ivFotoVenta.setPadding(padding, padding, padding, padding)
            binding.btnRemoveFoto.visibility = android.view.View.GONE
        }
    }

    private fun openCamera() {
        val photoFile = File(getExternalFilesDir(android.os.Environment.DIRECTORY_PICTURES), "VENTA_${System.currentTimeMillis()}.jpg")
        cameraImageUri = FileProvider.getUriForFile(this, "${packageName}.fileprovider", photoFile)
        takePhotoLauncher.launch(cameraImageUri!!)
    }

    private fun showSelectedImage(uri: Uri) {
        binding.ivFotoVenta.scaleType = android.widget.ImageView.ScaleType.CENTER_CROP
        Glide.with(this)
            .load(uri)
            .placeholder(R.drawable.uploap)
            .into(binding.ivFotoVenta)
        binding.ivFotoVenta.setPadding(0, 0, 0, 0)
        binding.btnRemoveFoto.visibility = android.view.View.VISIBLE
    }

    private fun calculateTotal() {
        val cant = binding.etCantidad.text.toString().toDoubleOrNull() ?: 0.0
        val precio = binding.etPrecio.text.toString().toDoubleOrNull() ?: 0.0
        val total = cant * precio
        binding.tvTotalIngreso.text = "S/ %.2f".format(total)
    }

    private fun showDatePicker() {
        DatePickerDialog(this, { _, year, month, day ->
            calendar.set(year, month, day)
            binding.etFecha.setText(dateFormat.format(calendar.time))
        }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH)).show()
    }

    private fun showAddCompradorDialog() {
        val dialogView = layoutInflater.inflate(R.layout.dialog_add_comprador, null)
        val etNombre = dialogView.findViewById<TextInputEditText>(R.id.etNombreComprador)
        
        MaterialAlertDialogBuilder(this)
            .setTitle("Nuevo Comprador")
            .setView(dialogView)
            .setPositiveButton("Guardar") { _, _ ->
                val nombre = etNombre.text.toString()
                if (nombre.isNotBlank()) {
                    lifecycleScope.launch {
                        db.assetDao().insertComprador(CompradorEntity(nombre = nombre, ruc_dni = null, telefono = null, email = null, direccion = null))
                        loadCompradores()
                    }
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun validateAndSave() {
        val cantidad = binding.etCantidad.text.toString().toDoubleOrNull() ?: 0.0
        val precio = binding.etPrecio.text.toString().toDoubleOrNull() ?: 0.0
        val flete = binding.etFlete.text.toString().toDoubleOrNull() ?: 0.0
        
        if (selectedCultivo == null) {
            binding.tilCultivo.error = "Seleccione un cultivo"
            return
        }
        binding.tilCultivo.error = null

        if (selectedCosecha == null) {
            binding.tilCosecha.error = "Seleccione un lote de cosecha"
            return
        }
        binding.tilCosecha.error = null

        if (selectedComprador == null) {
            binding.tilComprador.error = "Seleccione un comprador"
            return
        }
        binding.tilComprador.error = null

        if (cantidad <= 0) {
            binding.tilCantidad.error = "Cantidad inválida"
            return
        }
        binding.tilCantidad.error = null

        if (precio <= 0) {
            binding.tilPrecio.error = "Precio inválido"
            return
        }
        binding.tilPrecio.error = null

        lifecycleScope.launch {
            var finalFotoPath: String? = selectedImageUri?.toString()
            val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
            val userId = sharedPref.getInt("USER_ID", -1)
            val userName = sharedPref.getString("USER_NAME", "user") ?: "user"

            selectedImageUri?.let { uri ->
                if (uri.scheme == "content" || uri.scheme == "file") {
                    val savedPath = FileUtils.saveImageLocally(
                        this@RegisterVentaActivity,
                        uri,
                        userId,
                        userName,
                        "venta",
                        "venta_${System.currentTimeMillis()}.jpg"
                    )
                    finalFotoPath = savedPath
                }
            }

            val venta = VentaEntity(
                cosecha_id = selectedCosecha!!.id,
                comprador_id = selectedComprador!!.id,
                fecha_venta = calendar.timeInMillis / 1000,
                cantidad_vendida_kg = cantidad,
                precio_por_kg = precio,
                costo_flete = flete,
                comprobante_tipo = binding.actvTipoDoc.text.toString(),
                comprobante_numero = binding.etNumDoc.text.toString(),
                foto_path = finalFotoPath
            )
            db.assetDao().insertVenta(venta)
            
            // Opcionalmente marcar el cultivo como finalizado si ya se vendió todo? 
            // Por ahora solo registramos la venta.

            withContext(Dispatchers.Main) {
                Toast.makeText(this@RegisterVentaActivity, "Venta registrada con éxito", Toast.LENGTH_SHORT).show()
                finish()
            }
        }
    }
}
