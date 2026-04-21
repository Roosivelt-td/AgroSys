package com.sigcpa.agrosys.ui

import android.content.Context
import android.net.Uri
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.canhub.cropper.CropImageContract
import com.canhub.cropper.CropImageContractOptions
import com.canhub.cropper.CropImageOptions
import com.canhub.cropper.CropImageView
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.AgricultorEntity
import com.sigcpa.agrosys.database.entities.RedSocialEntity
import com.sigcpa.agrosys.database.entities.UsuarioEntity
import com.sigcpa.agrosys.database.dao.RedSocialWithMetadata
import com.sigcpa.agrosys.databinding.ActivityProfileBinding
import com.sigcpa.agrosys.databinding.DialogAddSocialNetworkBinding
import com.sigcpa.agrosys.databinding.DialogChangePasswordBinding
import com.google.android.material.card.MaterialCardView
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import com.google.android.material.switchmaterial.SwitchMaterial
import com.google.android.material.textfield.TextInputEditText
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileOutputStream

class ProfileActivity : AppCompatActivity() {

    private lateinit var binding: ActivityProfileBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentUser: UsuarioEntity? = null
    private var currentAgricultor: AgricultorEntity? = null
    private var isPickingProfile = true

    private val cropImage = registerForActivityResult(CropImageContract()) { result ->
        if (result.isSuccessful) {
            val uriContent = result.uriContent
            if (uriContent != null) {
                saveImageLocally(uriContent, isPickingProfile)
            }
        } else {
            val error = result.error
            error?.printStackTrace()
        }
    }

    // Lista de redes basada en tus drawables disponibles
    private val availableSocialTypes = listOf(
        SocialTypeData("Facebook", R.drawable.red_social_facebook, "#ebfff4"),
        SocialTypeData("Instagram", R.drawable.red_social_instagram, "#ebfff4"),
        SocialTypeData("WhatsApp", R.drawable.red_social_whatsapp, "#ebfff4"),
        SocialTypeData("TikTok", R.drawable.red_social_tiktok, "#ebfff4"),
        SocialTypeData("Telegram", R.drawable.red_socila_telegram, "#ebfff4"),
        SocialTypeData("YouTube", R.drawable.ic_youtube, "#ebfff4"),
        SocialTypeData("Twitter", R.drawable.red_social_twitter_01, "#ebfff4"),
        SocialTypeData("Threads", R.drawable.red_social_hreads, "#ebfff4"), 
    )

    data class SocialTypeData(val name: String, val iconRes: Int, val color: String)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityProfileBinding.inflate(layoutInflater)
        setContentView(binding.root)

        window.statusBarColor = android.graphics.Color.parseColor("#00582a")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        // Aplicar Insets
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            binding.appBarLayout.setPadding(0, systemBars.top, 0, 0)
            insets
        }

        setupStats()
        setupMenu()
        setupListeners()
        loadUserData()
    }

    private fun setupStats() {
        binding.statCrops.apply {
            findViewById<ImageView>(R.id.ivStatIcon).setImageResource(R.drawable.agro_planta_hoja)
            findViewById<TextView>(R.id.tvStatValue).text = "8"
            findViewById<TextView>(R.id.tvStatLabel).text = "CULTIVOS"
        }
        binding.statBalance.apply {
            findViewById<ImageView>(R.id.ivStatIcon).setImageResource(R.drawable.ic_money_01)
            findViewById<TextView>(R.id.tvStatValue).text = "S/ 12,450"
            findViewById<TextView>(R.id.tvStatLabel).text = "BALANCE"
        }
        binding.statHarvests.apply {
            findViewById<ImageView>(R.id.ivStatIcon).setImageResource(R.drawable.agro_maquinaria_tractor)
            findViewById<TextView>(R.id.tvStatValue).text = "24"
            findViewById<TextView>(R.id.tvStatLabel).text = "COSECHAS"
        }
    }

    private fun setupMenu() {
        // 1. Información Personal
        binding.menuPersonalInfo.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_user_modern)
            tvMenuTitle.text = "Información Personal"
            root.setOnClickListener { showEditProfileModal() }
        }

        // 2. Cambio de Contraseña
        binding.menuSecurity.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_admin)
            tvMenuTitle.text = "Cambio de Contraseña"
            root.setOnClickListener { showChangePasswordDialog() }
        }

        // 3. Salir
        binding.menuLogout.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_cerrar_sesion)
            ivMenuIcon.setColorFilter(android.graphics.Color.parseColor("#DC2626"))
            tvMenuTitle.text = "Salir"
            tvMenuTitle.setTextColor(android.graphics.Color.parseColor("#DC2626"))
            root.setOnClickListener { showLogoutConfirmationDialog() }
        }
    }

    private fun showLogoutConfirmationDialog() {
        MaterialAlertDialogBuilder(this)
            .setTitle("Cerrar Sesión")
            .setMessage("¿Estás seguro de que deseas salir de AgroSys?")
            .setNegativeButton("Cancelar", null)
            .setPositiveButton("Salir") { _, _ ->
                finish()
            }
            .show()
    }

    private fun setupListeners() {
        binding.btnChangeAvatar.setOnClickListener {
            isPickingProfile = true
            startImagePicker(true)
        }
        binding.btnEditProfileHeader.setOnClickListener {
            isPickingProfile = false
            startImagePicker(false)
        }
        binding.tvVerMasGallery.setOnClickListener {
            Toast.makeText(this, "Abriendo Multimedia...", Toast.LENGTH_SHORT).show()
        }
        binding.btnAddSocial.setOnClickListener {
            showAddSocialDialog(null)
        }
    }

    private fun loadUserData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            currentUser = db.userDao().getUsuarioById(userId)
            currentAgricultor = db.userDao().getAgricultorByUserId(userId)
            updateUIWithUserData()
        }
    }

    private fun updateUIWithUserData() {
        currentUser?.let { user ->
            binding.tvProfileName.text = "${user.nombre} ${user.apellidos}"
            binding.tvProfileLocation.text = user.ubicacion ?: "Ubicación no definida"
            
            // Cargar foto de perfil si existe
            user.foto_perfil_url?.let { path ->
                val file = File(path)
                if (file.exists()) {
                    com.bumptech.glide.Glide.with(this)
                        .load(file)
                        .circleCrop()
                        .diskCacheStrategy(com.bumptech.glide.load.engine.DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                        .into(binding.ivProfileAvatar)
                    
                    // IMPORTANTE: Quitar tinte y padding para ver la foto real
                    binding.ivProfileAvatar.imageTintList = null
                    binding.ivProfileAvatar.setPadding(0, 0, 0, 0)
                }
            }

            // Cargar foto de portada si existe
            user.foto_portada_url?.let { path ->
                val file = File(path)
                if (file.exists()) {
                    com.bumptech.glide.Glide.with(this)
                        .load(file)
                        .centerCrop()
                        .diskCacheStrategy(com.bumptech.glide.load.engine.DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                        .into(binding.ivProfilePortada)
                }
            }

            // Actualizar la descripción con el formato e iconos solicitados
            val agricultor = currentAgricultor
            val infoDescription = StringBuilder()
            
            if (agricultor != null) {
                // Línea 1: 🎓 Nivel Educativo | 📘 DNI | 📱 Teléfono
                val linea1Items = mutableListOf<String>()
                agricultor.nivel_educativo?.let { linea1Items.add("🎓 Nivel Educativo: $it") }
                linea1Items.add("📘 DNI/RUC: ${agricultor.dni}")
                user.telefono?.let { linea1Items.add("📱 $it") }
                val linea1 = linea1Items.joinToString(" | ")
                
                // Línea 2: Frase con experiencia dinámica
                val linea2 = "🌾 Apasionado por la agricultura sostenible | 🚜 ${agricultor.experiencia_anios} años cultivando la tierra | 📸 Compartiendo mi día a día en el campo"
                
                infoDescription.append(linea1).append("\n").append(linea2)
            } else {
                user.telefono?.let { infoDescription.append("📱 Teléfono: $it") }
            }
            
            binding.tvProfileDescription.text = infoDescription.toString()
            
            loadUserSocialNetworks(user.id)
        }
    }

    private fun startImagePicker(isProfile: Boolean) {
        val options = CropImageOptions(
            imageSourceIncludeGallery = true,
            imageSourceIncludeCamera = true,
            guidelines = CropImageView.Guidelines.ON,
            cropShape = if (isProfile) CropImageView.CropShape.OVAL else CropImageView.CropShape.RECTANGLE,
            fixAspectRatio = true,
            aspectRatioX = if (isProfile) 1 else 16,
            aspectRatioY = if (isProfile) 1 else 9,
            // Colores y botones visibles
            activityBackgroundColor = android.graphics.Color.parseColor("#77e0b4"),
            toolbarColor = android.graphics.Color.parseColor("#15803D"),
            toolbarTintColor = android.graphics.Color.WHITE,
            activityMenuIconColor = android.graphics.Color.WHITE,
            progressBarColor = android.graphics.Color.WHITE,
            cropMenuCropButtonTitle = "GUARDAR",
            activityTitle = if (isProfile) "Recortar Perfil" else "Recortar Portada",
            showCropOverlay = true,
            showProgressBar = true
        )

        cropImage.launch(
            CropImageContractOptions(
                uri = null,
                cropImageOptions = options
            )
        )
    }

    private fun saveImageLocally(uri: android.net.Uri, isProfile: Boolean) {
        lifecycleScope.launch(Dispatchers.IO) {
            try {
                val user = currentUser ?: return@launch
                val inputStream = contentResolver.openInputStream(uri)
                val bytes = inputStream?.readBytes() ?: return@launch
                
                // Crear carpeta perfil/id_nombreUsuario
                val folderName = "perfil/${user.id}_${user.nombre.replace(" ", "_")}"
                val directory = File(filesDir, folderName)
                if (!directory.exists()) directory.mkdirs()

                val fileName = if (isProfile) "profile.jpg" else "cover.jpg"
                val file = File(directory, fileName)
                
                // Sobrescribir el archivo existente
                FileOutputStream(file).use { it.write(bytes) }

                // Actualizar DB
                val updatedUser = if (isProfile) {
                    user.copy(foto_perfil_url = file.absolutePath)
                } else {
                    user.copy(foto_portada_url = file.absolutePath)
                }
                
                db.userDao().updateUsuario(updatedUser)
                currentUser = updatedUser
                
                withContext(Dispatchers.Main) {
                    updateUIWithUserData()
                    val msg = if (isProfile) "Foto de perfil actualizada" else "Portada actualizada"
                    Toast.makeText(this@ProfileActivity, msg, Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                e.printStackTrace()
                withContext(Dispatchers.Main) {
                    Toast.makeText(this@ProfileActivity, "Error al guardar imagen", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun loadUserSocialNetworks(userId: Int) {
        lifecycleScope.launch {
            val redes = db.userDao().getRedesSocialesByUsuario(userId)
            binding.llSocialContainer.removeAllViews()

            val agrupadas = redes.groupBy { it.redSocial.tipo_red_id }

            redes.forEach { red ->
                val view = LayoutInflater.from(this@ProfileActivity).inflate(R.layout.item_social_icon, binding.llSocialContainer, false)
                val card = view.findViewById<MaterialCardView>(R.id.cardSocialIcon)
                val icon = view.findViewById<ImageView>(R.id.ivSocialIcon)
                val badge = view.findViewById<TextView>(R.id.tvSocialBadge)
                
                // Numeración si hay más de una
                val delMismoTipo = agrupadas[red.redSocial.tipo_red_id] ?: emptyList()
                if (delMismoTipo.size > 1) {
                    badge.visibility = View.VISIBLE
                    badge.text = (delMismoTipo.indexOf(red) + 1).toString()
                } else {
                    badge.visibility = View.GONE
                }

                icon.setImageResource(getSocialIcon(red.tipo_nombre))
                card.setCardBackgroundColor(android.graphics.Color.parseColor(red.color_hex ?: "#F3F4F6"))

                view.setOnClickListener {
                    openSocialUrl(red.redSocial.link)
                }

                view.setOnLongClickListener {
                    showSocialOptionsDialog(red)
                    true
                }

                binding.llSocialContainer.addView(view)
            }
            binding.llSocialContainer.addView(binding.btnAddSocial)
        }
    }

    private fun getSocialIcon(name: String): Int {
        return availableSocialTypes.find { it.name.lowercase() == name.lowercase() }?.iconRes ?: R.drawable.ic_edit_01
    }

    private fun openSocialUrl(url: String) {
        var finalUrl = url.trim()
        if (!finalUrl.startsWith("http://") && !finalUrl.startsWith("https://")) {
            finalUrl = "https://$finalUrl"
        }
        try {
            val intent = android.content.Intent(android.content.Intent.ACTION_VIEW, android.net.Uri.parse(finalUrl))
            startActivity(intent)
        } catch (e: Exception) {
            Toast.makeText(this, "No se pudo abrir el enlace", Toast.LENGTH_SHORT).show()
        }
    }

    private fun isValidSocialUrl(typeName: String, url: String): Boolean {
        val lowerUrl = url.lowercase()
        return when (typeName) {
            "Facebook" -> lowerUrl.contains("facebook.com/") || lowerUrl.contains("fb.com/")
            "Instagram" -> lowerUrl.contains("instagram.com/")
            "WhatsApp" -> lowerUrl.contains("wa.me/") || lowerUrl.contains("whatsapp.com/") || lowerUrl.contains("api.whatsapp.com/")
            "TikTok" -> lowerUrl.contains("tiktok.com/")
            "Telegram" -> lowerUrl.contains("t.me/") || lowerUrl.contains("telegram.me/")
            "YouTube" -> lowerUrl.contains("youtube.com/") || lowerUrl.contains("youtu.be/")
            "Twitter" -> lowerUrl.contains("twitter.com/") || lowerUrl.contains("x.com/")
            "Threads" -> lowerUrl.contains("threads.net/")
            else -> true
        }
    }

    private fun showSocialOptionsDialog(red: RedSocialWithMetadata) {
        val options = arrayOf("Editar", "Eliminar")
        MaterialAlertDialogBuilder(this)
            .setTitle(red.tipo_nombre)
            .setItems(options) { _, which ->
                when(which) {
                    0 -> showAddSocialDialog(red)
                    1 -> deleteSocialNetwork(red.redSocial)
                }
            }
            .show()
    }

    private fun deleteSocialNetwork(red: RedSocialEntity) {
        lifecycleScope.launch {
            db.userDao().deleteRedSocial(red)
            loadUserSocialNetworks(red.usuario_id)
            Toast.makeText(this@ProfileActivity, "Red social eliminada", Toast.LENGTH_SHORT).show()
        }
    }

    private fun showAddSocialDialog(redParaEditar: RedSocialWithMetadata?) {
        val dialogBinding = DialogAddSocialNetworkBinding.inflate(layoutInflater)
        var tipoSeleccionado: SocialTypeData? = null

        if (redParaEditar != null) {
            dialogBinding.tvDialogTitle.text = "Editar Red Social"
            dialogBinding.tvSelectTypeLabel.visibility = View.GONE
            dialogBinding.llSocialIconsSelector.visibility = View.GONE
            dialogBinding.tvSelectedSocialName.text = "Editando: ${redParaEditar.tipo_nombre}"
            dialogBinding.etSocialUrl.setText(redParaEditar.redSocial.link)
            dialogBinding.etSocialDesc.setText(redParaEditar.redSocial.descripcion)
            tipoSeleccionado = availableSocialTypes.find { it.name == redParaEditar.tipo_nombre }
        } else {
            // Selector de iconos basado en la lista local
            availableSocialTypes.forEach { type ->
                val iconView = LayoutInflater.from(this).inflate(R.layout.item_social_icon, dialogBinding.llSocialIconsSelector, false)
                val iconImg = iconView.findViewById<ImageView>(R.id.ivSocialIcon)
                val card = iconView.findViewById<MaterialCardView>(R.id.cardSocialIcon)
                
                iconImg.setImageResource(type.iconRes)
                card.setCardBackgroundColor(android.graphics.Color.parseColor(type.color))
                
                iconView.setOnClickListener {
                    tipoSeleccionado = type
                    dialogBinding.tvSelectedSocialName.text = "Seleccionado: ${type.name}"
                    // Resaltar selección
                    dialogBinding.llSocialIconsSelector.childNodes().forEach { child ->
                        child.alpha = 0.5f
                    }
                    iconView.alpha = 1.0f
                }
                dialogBinding.llSocialIconsSelector.addView(iconView)
            }
        }

        val dialog = MaterialAlertDialogBuilder(this)
            .setView(dialogBinding.root)
            .setCancelable(true)
            .create()

        dialogBinding.btnCloseDialog.setOnClickListener { dialog.dismiss() }

        dialogBinding.btnSaveSocial.setOnClickListener {
            val urlSocial = dialogBinding.etSocialUrl.text.toString().trim()
            val descSocial = dialogBinding.etSocialDesc.text.toString().trim()

            if (tipoSeleccionado == null || urlSocial.isEmpty()) {
                Toast.makeText(this, "Selecciona una red y pon el enlace", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            // Validar que la URL coincida con el tipo de red social
            if (!isValidSocialUrl(tipoSeleccionado!!.name, urlSocial)) {
                Toast.makeText(this, "El enlace no parece ser de ${tipoSeleccionado!!.name}", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            lifecycleScope.launch {
                val userId = currentUser?.id ?: 0
                
                // Buscar el ID real en la DB basado en el nombre
                val tiposDB = db.userDao().getAllTiposRedSocial()
                val tipoDB = tiposDB.find { it.nombre == tipoSeleccionado!!.name } ?: return@launch

                val redesExistentes = db.userDao().getRedesSocialesByUsuario(userId)
                val delMismoTipo = redesExistentes.filter { it.redSocial.tipo_red_id == tipoDB.id }

                if (redParaEditar == null && delMismoTipo.size >= 2) {
                    Toast.makeText(this@ProfileActivity, "Máximo 2 redes por tipo", Toast.LENGTH_SHORT).show()
                    return@launch
                }

                if (redParaEditar != null) {
                    val redUpdate = redParaEditar.redSocial.copy(link = urlSocial, descripcion = descSocial)
                    db.userDao().updateRedSocial(redUpdate)
                } else {
                    val nuevaRed = RedSocialEntity(
                        usuario_id = userId,
                        tipo_red_id = tipoDB.id,
                        link = urlSocial,
                        usuario_social = currentUser?.nombre ?: "Usuario",
                        descripcion = descSocial
                    )
                    db.userDao().insertRedSocial(nuevaRed)
                }
                loadUserSocialNetworks(userId)
                dialog.dismiss()
            }
        }
        dialog.show()
    }

    private fun android.view.ViewGroup.childNodes(): List<View> {
        val children = mutableListOf<View>()
        for (i in 0 until childCount) children.add(getChildAt(i))
        return children
    }

    private fun showEditProfileModal() {
        val user = currentUser ?: return
        val agricultor = currentAgricultor
        val dialogView = layoutInflater.inflate(R.layout.dialog_edit_profile, null)
        
        val etNombre = dialogView.findViewById<TextInputEditText>(R.id.etEditNombre)
        val etApellidos = dialogView.findViewById<TextInputEditText>(R.id.etEditApellidos)
        val etDni = dialogView.findViewById<TextInputEditText>(R.id.etEditDni)
        val etTelefono = dialogView.findViewById<TextInputEditText>(R.id.etEditTelefono)
        val etUbicacion = dialogView.findViewById<TextInputEditText>(R.id.etEditUbicacion)
        val etNivelEducativo = dialogView.findViewById<TextInputEditText>(R.id.etEditNivelEducativo)
        val etExperiencia = dialogView.findViewById<TextInputEditText>(R.id.etEditExperiencia)
        val swActivo = dialogView.findViewById<SwitchMaterial>(R.id.swEditActivo)
        val etEmail = dialogView.findViewById<TextInputEditText>(R.id.etEditEmail)
        val etRol = dialogView.findViewById<TextInputEditText>(R.id.etEditRol)

        etNombre.setText(user.nombre)
        etApellidos.setText(user.apellidos)
        etTelefono.setText(user.telefono)
        etUbicacion.setText(user.ubicacion)
        etEmail.setText(user.email)
        etRol.setText(user.rol)
        swActivo.isChecked = user.is_activo == 1
        
        agricultor?.let {
            etDni.setText(it.dni)
            etNivelEducativo.setText(it.nivel_educativo)
            etExperiencia.setText(it.experiencia_anios.toString())
        }

        val dialog = MaterialAlertDialogBuilder(this)
            .setView(dialogView)
            .create()

        dialogView.findViewById<com.google.android.material.button.MaterialButton>(R.id.btnSaveProfile).setOnClickListener {
            val nuevoNombre = etNombre.text.toString()
            val nuevosApellidos = etApellidos.text.toString()
            val nuevoTelefono = etTelefono.text.toString()
            val nuevaUbicacion = etUbicacion.text.toString()
            val nuevoDni = etDni.text.toString()
            val nuevoNivel = etNivelEducativo.text.toString()
            val nuevaExperiencia = etExperiencia.text.toString().toIntOrNull() ?: 0
            val nuevoEstado = if (swActivo.isChecked) 1 else 0

            if (nuevoNombre.isEmpty() || nuevosApellidos.isEmpty() || nuevoDni.isEmpty()) {
                Toast.makeText(this, "Nombre, apellidos y DNI son obligatorios", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            lifecycleScope.launch {
                val userActualizado = user.copy(
                    nombre = nuevoNombre,
                    apellidos = nuevosApellidos,
                    telefono = nuevoTelefono,
                    ubicacion = nuevaUbicacion,
                    is_activo = nuevoEstado,
                    updated_at = System.currentTimeMillis() / 1000
                )
                db.userDao().updateUsuario(userActualizado)
                currentUser = userActualizado

                if (agricultor != null) {
                    val agriActualizado = agricultor.copy(
                        dni = nuevoDni,
                        nivel_educativo = nuevoNivel,
                        experiencia_anios = nuevaExperiencia,
                        updated_at = System.currentTimeMillis() / 1000
                    )
                    db.userDao().updateAgricultor(agriActualizado)
                    currentAgricultor = agriActualizado
                } else {
                    val nuevoAgri = AgricultorEntity(
                        usuario_id = user.id,
                        dni = nuevoDni,
                        nivel_educativo = nuevoNivel,
                        experiencia_anios = nuevaExperiencia
                    )
                    db.userDao().insertAgricultor(nuevoAgri)
                    currentAgricultor = nuevoAgri
                }

                updateUIWithUserData()
                Toast.makeText(this@ProfileActivity, "Perfil actualizado correctamente", Toast.LENGTH_SHORT).show()
                dialog.dismiss()
            }
        }
        dialog.show()
    }

    private fun showChangePasswordDialog() {
        val dialogBinding = DialogChangePasswordBinding.inflate(layoutInflater)
        val dialog = MaterialAlertDialogBuilder(this)
            .setView(dialogBinding.root)
            .create()

        dialog.window?.setBackgroundDrawableResource(android.R.color.transparent)
        
        dialogBinding.btnCancelPassword.setOnClickListener { dialog.dismiss() }

        dialogBinding.btnConfirmChange.setOnClickListener {
            val currentPass = dialogBinding.etCurrentPassword.text.toString()
            val newPass = dialogBinding.etNewPassword.text.toString()
            val confirmPass = dialogBinding.etConfirmPassword.text.toString()

            if (currentPass.isEmpty() || newPass.isEmpty() || confirmPass.isEmpty()) {
                Toast.makeText(this, "Completa todos los campos", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (newPass != confirmPass) {
                Toast.makeText(this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            lifecycleScope.launch {
                val user = currentUser
                if (user != null && user.password == currentPass) {
                    db.userDao().updateUsuario(user.copy(password = newPass))
                    Toast.makeText(this@ProfileActivity, "Contraseña actualizada", Toast.LENGTH_SHORT).show()
                    dialog.dismiss()
                } else {
                    Toast.makeText(this@ProfileActivity, "Contraseña actual incorrecta", Toast.LENGTH_SHORT).show()
                }
            }
        }
        dialog.show()
    }
}
