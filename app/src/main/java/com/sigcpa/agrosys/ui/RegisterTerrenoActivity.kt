package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.graphics.Color
import android.net.Uri
import android.os.Bundle
import android.os.Environment
import android.provider.MediaStore
import android.view.View
import android.widget.LinearLayout
import android.widget.Toast
import java.io.File
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.FileProvider
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ActivityRegisterTerrenoBinding
import com.sigcpa.agrosys.repository.AssetRepository
import com.sigcpa.agrosys.util.FileUtils
import kotlinx.coroutines.launch
import java.util.Calendar

class RegisterTerrenoActivity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterTerrenoBinding
    private lateinit var assetRepository: AssetRepository
    private var agricultorId: Int = -1
    private var fechaAlquilerMs: Long? = null
    private var fechaVencimientoMs: Long? = null
    private var selectedImageUri: Uri? = null
    private var cameraImageUri: Uri? = null

    private var currentTerrenoId: Int = -1
    private var areaOcupadaActual: Double = 0.0

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
        
        currentTerrenoId = intent.getIntExtra("TERRENO_ID", -1)
        areaOcupadaActual = intent.getDoubleExtra("AREA_OCUPADA", 0.0)

        lifecycleScope.launch {
            val id = assetRepository.getAgricultorIdByUserId(userId)
            if (id != null) {
                agricultorId = id
            } else {
                Toast.makeText(this@RegisterTerrenoActivity, getString(R.string.error_profile_not_found), Toast.LENGTH_SHORT).show()
                finish()
            }

            if (currentTerrenoId != -1) {
                loadTerrenoData(currentTerrenoId)
            }
        }

        setupUI()
    }

    private fun loadTerrenoData(id: Int) {
        lifecycleScope.launch {
            val db = com.sigcpa.agrosys.database.AppDatabase.getDatabase(this@RegisterTerrenoActivity)
            val terreno = db.assetDao().getTerrenoById(id)
            terreno?.let {
                binding.tvHeaderTitle.text = getString(R.string.title_editar_terreno)
                binding.etNombre.setText(it.nombre)
                binding.etUbicacion.setText(it.direccion_referencia)
                binding.etArea.setText(it.area_hectareas.toString())
                binding.etCalidadSuelo.setText(it.calidad_suelo)
                binding.etFuenteAgua.setText(it.fuente_agua)
                
                if (it.tipo_tenencia == "alquilado") {
                    binding.btnAlquilado.isChecked = true
                    binding.layoutCosto.visibility = View.VISIBLE
                    binding.etCostoAlquiler.setText(it.costo_alquiler_anual.toString())
                    
                    it.fecha_alquiler?.let { f ->
                        fechaAlquilerMs = f
                        binding.etFechaAlquiler.setText(formatDate(f))
                    }
                    it.fecha_vencimiento_alquiler?.let { f ->
                        fechaVencimientoMs = f
                        binding.etFechaVencimiento.setText(formatDate(f))
                    }
                } else {
                    binding.btnPropio.isChecked = true
                    binding.layoutCosto.visibility = View.GONE
                }

                it.foto_path?.let { path ->
                    if (path.isNotEmpty()) {
                        val file = java.io.File(path)
                        if (file.exists()) {
                            selectedImageUri = Uri.fromFile(file)
                            showSelectedImage(selectedImageUri!!)
                        }
                    }
                }
            }
        }
    }

    private fun showImagePickerDialog() {
        val options = arrayOf("Tomar Foto", "Elegir de Galería")
        AlertDialog.Builder(this)
            .setTitle("Seleccionar Imagen")
            .setItems(options) { _, which ->
                when (which) {
                    0 -> openCamera()
                    1 -> pickImageLauncher.launch("image/*")
                }
            }
            .show()
    }

    private fun openCamera() {
        val photoFile = File(
            getExternalFilesDir(Environment.DIRECTORY_PICTURES),
            "IMG_${System.currentTimeMillis()}.jpg"
        )
        cameraImageUri = FileProvider.getUriForFile(
            this,
            "${packageName}.fileprovider",
            photoFile
        )
        cameraImageUri?.let {
            takePhotoLauncher.launch(it)
        }
    }

    private fun showSelectedImage(uri: Uri) {
        binding.ivFotoTerreno.scaleType = android.widget.ImageView.ScaleType.CENTER_CROP
        binding.ivFotoTerreno.imageTintList = null
        Glide.with(this)
            .load(uri)
            .placeholder(R.drawable.uploap)
            .into(binding.ivFotoTerreno)
        
        binding.ivFotoTerreno.setPadding(0, 0, 0, 0)
        binding.btnRemoveFoto.visibility = View.VISIBLE
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }

        binding.cardFotoTerreno.setOnClickListener {
            showImagePickerDialog()
        }

        binding.btnRemoveFoto.setOnClickListener {
            selectedImageUri = null
            binding.ivFotoTerreno.setImageResource(R.drawable.uploap)
            binding.ivFotoTerreno.imageTintList = android.content.res.ColorStateList.valueOf(Color.parseColor("#D1D5DB"))
            binding.ivFotoTerreno.scaleType = android.widget.ImageView.ScaleType.CENTER_INSIDE
            val padding = (64 * resources.displayMetrics.density).toInt()
            binding.ivFotoTerreno.setPadding(padding, padding, padding, padding)
            binding.btnRemoveFoto.visibility = View.GONE
        }

        binding.toggleTenencia.addOnButtonCheckedListener { _, checkedId, isChecked ->
            if (isChecked) {
                binding.layoutCosto.visibility = if (checkedId == binding.btnAlquilado.id) View.VISIBLE else View.GONE
            }
        }

        binding.etFechaAlquiler.setOnClickListener { showDatePicker { date ->
            fechaAlquilerMs = date
            binding.etFechaAlquiler.setText(formatDate(date))
            
            // Auto-set expiration date to 6 months later
            val cal = Calendar.getInstance()
            cal.timeInMillis = date * 1000
            cal.add(Calendar.MONTH, 6)
            fechaVencimientoMs = cal.timeInMillis / 1000
            binding.etFechaVencimiento.setText(formatDate(fechaVencimientoMs!!))
        }}

        binding.etFechaVencimiento.setOnClickListener { 
            val minDate = if (fechaAlquilerMs != null) (fechaAlquilerMs!! * 1000) + 86400000 else null
            showDatePicker(minDate) { date ->
                fechaVencimientoMs = date
                binding.etFechaVencimiento.setText(formatDate(date))
            }
        }

        binding.btnSave.text = if (currentTerrenoId != -1) getString(R.string.btn_actualizar_terreno) else getString(R.string.btn_guardar_terreno)
        binding.btnSave.setOnClickListener {
            saveTerreno()
        }
    }

    private fun showDatePicker(minDate: Long? = null, onDateSelected: (Long) -> Unit) {
        val calendar = Calendar.getInstance()
        val dialog = DatePickerDialog(
            this,
            { _, year, month, dayOfMonth ->
                val selected = Calendar.getInstance()
                selected.set(year, month, dayOfMonth)
                onDateSelected(selected.timeInMillis / 1000)
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        )
        if (minDate != null) {
            dialog.datePicker.minDate = minDate
        }
        dialog.show()
    }

    private fun formatDate(seconds: Long): String {
        val cal = Calendar.getInstance()
        cal.timeInMillis = seconds * 1000
        return "${cal.get(Calendar.DAY_OF_MONTH)}/${cal.get(Calendar.MONTH) + 1}/${cal.get(Calendar.YEAR)}"
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

        if (area < areaOcupadaActual) {
            Toast.makeText(this, getString(R.string.error_area_menor_ocupada, areaOcupadaActual), Toast.LENGTH_LONG).show()
            binding.etArea.requestFocus()
            return
        }

        if (isAlquilado && costStr.isEmpty()) {
            Toast.makeText(this, getString(R.string.error_costo_required), Toast.LENGTH_SHORT).show()
            binding.etCostoAlquiler.requestFocus()
            return
        }

        if (isAlquilado && fechaAlquilerMs == null) {
            Toast.makeText(this, getString(R.string.error_fecha_alquiler_required), Toast.LENGTH_SHORT).show()
            return
        }

        if (isAlquilado && fechaVencimientoMs == null) {
            Toast.makeText(this, getString(R.string.error_fecha_vencimiento_required), Toast.LENGTH_SHORT).show()
            return
        }

        if (isAlquilado && fechaVencimientoMs != null && fechaAlquilerMs != null) {
            if (fechaVencimientoMs!! <= fechaAlquilerMs!!) {
                Toast.makeText(this, getString(R.string.error_fecha_vencimiento_invalid), Toast.LENGTH_SHORT).show()
                return
            }
        }

        val cost = costStr.toDoubleOrNull() ?: 0.0

        lifecycleScope.launch {
            var finalFotoPath: String? = selectedImageUri?.toString()
            
            // Si hay una nueva imagen seleccionada (Uri de galería/camera), guardarla localmente
            selectedImageUri?.let { uri ->
                if (uri.scheme == "content" || uri.scheme == "file") {
                    val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
                    val userName = sharedPref.getString("USER_NAME", "user") ?: "user"
                    
                    val savedPath = FileUtils.saveImageLocally(
                        this@RegisterTerrenoActivity,
                        uri,
                        agricultorId, // Usamos el ID del agricultor
                        userName,
                        FileUtils.getTerrenoFolder(agricultorId, userName),
                        "terreno_${System.currentTimeMillis()}.jpg"
                    )
                    if (savedPath != null) {
                        finalFotoPath = savedPath
                    }
                }
            }

            val terreno = TerrenoEntity(
                id = if (currentTerrenoId != -1) currentTerrenoId else 0,
                agricultor_id = agricultorId,
                nombre = nombre,
                ubicacion_geo = null,
                direccion_referencia = ubicacion,
                area_hectareas = area,
                tipo_tenencia = if (isAlquilado) "alquilado" else "propio",
                costo_alquiler_anual = cost,
                calidad_suelo = quality,
                fuente_agua = water,
                fecha_alquiler = if (isAlquilado) fechaAlquilerMs else null,
                fecha_vencimiento_alquiler = if (isAlquilado) fechaVencimientoMs else null,
                foto_path = finalFotoPath
            )

            val success = if (currentTerrenoId != -1) {
                val db = com.sigcpa.agrosys.database.AppDatabase.getDatabase(this@RegisterTerrenoActivity)
                db.assetDao().updateTerreno(terreno)
                true
            } else {
                assetRepository.saveTerreno(terreno)
            }

            if (success) {
                Toast.makeText(this@RegisterTerrenoActivity, getString(R.string.msg_terreno_saved), Toast.LENGTH_SHORT).show()
                finish()
            } else {
                Toast.makeText(this@RegisterTerrenoActivity, getString(R.string.error_db_save), Toast.LENGTH_SHORT).show()
            }
        }
    }
}
