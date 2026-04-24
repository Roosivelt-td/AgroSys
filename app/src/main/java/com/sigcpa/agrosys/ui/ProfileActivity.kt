package com.sigcpa.agrosys.ui

import android.content.Context
import android.os.Bundle
import android.view.LayoutInflater
import android.view.WindowManager
import android.widget.ImageView
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
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.database.dao.RedSocialWithMetadata
import com.sigcpa.agrosys.databinding.ActivityProfileBinding
import com.sigcpa.agrosys.databinding.DialogAddSocialNetworkBinding
import com.google.android.material.card.MaterialCardView
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileOutputStream

class ProfileActivity : AppCompatActivity() {

    private lateinit var binding: ActivityProfileBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentUser: UsuarioEntity? = null
    private var currentRol: RolEntity? = null
    private var isPickingProfile = true

    private val cropImage = registerForActivityResult(CropImageContract()) { result ->
        if (result.isSuccessful) {
            val uriContent = result.uriContent
            if (uriContent != null) {
                saveImageLocally(uriContent, isPickingProfile)
            }
        }
    }

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
        binding.menuPersonalInfo.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_user_modern)
            tvMenuTitle.text = "Información Personal"
        }

        binding.menuSecurity.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_admin)
            tvMenuTitle.text = "Seguridad y Accesos"
            root.setOnClickListener { showSecurityOptions() }
        }

        binding.menuLogout.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_cerrar_sesion)
            ivMenuIcon.setColorFilter(android.graphics.Color.parseColor("#DC2626"))
            tvMenuTitle.text = "Salir"
            tvMenuTitle.setTextColor(android.graphics.Color.parseColor("#DC2626"))
            root.setOnClickListener { showLogoutConfirmationDialog() }
        }
    }

    private fun showSecurityOptions() {
        val options = mutableListOf("Cambiar Contraseña")
        
        currentRol?.let { rol ->
            if (rol.nivel >= 3) {
                options.add("Gestionar Miembros")
            } else {
                options.add("Solicitar Cambio de Nivel")
            }
        }

        MaterialAlertDialogBuilder(this)
            .setTitle("Seguridad y Accesos")
            .setItems(options.toTypedArray()) { _, which ->
                when(which) {
                    0 -> Toast.makeText(this, "Funcionalidad en desarrollo", Toast.LENGTH_SHORT).show()
                    1 -> if (options[1].contains("Gestionar")) {
                        // Navegar a ManageMembersActivity
                    } else {
                        showRequestLevelUp()
                    }
                }
            }
            .show()
    }

    private fun showRequestLevelUp() {
        lifecycleScope.launch {
            val roles = db.userDao().getAllRoles().filter { it.nivel > (currentRol?.nivel ?: 1) }
            val roleNames = roles.map { it.nombre }.toTypedArray()

            if (roleNames.isEmpty()) {
                Toast.makeText(this@ProfileActivity, "Ya tienes el nivel máximo", Toast.LENGTH_SHORT).show()
                return@launch
            }

            MaterialAlertDialogBuilder(this@ProfileActivity)
                .setTitle("Solicitar Nuevo Nivel")
                .setItems(roleNames) { _, which ->
                    createRequestNotification(roles[which])
                }
                .show()
        }
    }

    private fun createRequestNotification(rol: RolEntity) {
        lifecycleScope.launch {
            val user = currentUser ?: return@launch
            // En lugar de SolicitudRolEntity, enviamos una notificación al sistema/admin
            val notif = NotificacionEntity(
                usuario_id = user.id,
                titulo = "Solicitud de Rango",
                mensaje = "${user.nombre} solicita subir al nivel: ${rol.nombre}",
                tipo = "solicitud_rol",
                datos_relacionados = "{\"rol_id\": ${rol.id}}"
            )
            db.userDao().insertNotificacion(notif)
            Toast.makeText(this@ProfileActivity, "Solicitud enviada al Administrador", Toast.LENGTH_LONG).show()
        }
    }

    private fun showLogoutConfirmationDialog() {
        MaterialAlertDialogBuilder(this)
            .setTitle("Cerrar Sesión")
            .setMessage("¿Estás seguro de que deseas salir?")
            .setNegativeButton("Cancelar", null)
            .setPositiveButton("Salir") { _, _ -> finish() }
            .show()
    }

    private fun setupListeners() {
        binding.btnChangeAvatar.setOnClickListener {
            isPickingProfile = true
            startImagePicker(true)
        }
        binding.btnAddSocial.setOnClickListener {
            showAddSocialDialog()
        }
    }

    private fun loadUserData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            currentUser = db.userDao().getUsuarioById(userId)
            currentUser?.let { user ->
                // Buscar rol en la organización activa
                val miembros = db.userDao().getMiembrosConRolPorUsuario(user.id)
                if (miembros.isNotEmpty()) {
                    currentRol = db.userDao().getRolById(miembros[0].rol_id)
                }
            }
            updateUIWithUserData()
        }
    }

    private fun updateUIWithUserData() {
        currentUser?.let { user ->
            binding.tvProfileName.text = "${user.nombre} ${user.apellidos}"
            binding.tvProfileLocation.text = "${user.ciudad ?: ""}, ${user.direccion ?: "Sin dirección"}"
            
            currentRol?.let { rol ->
                binding.tvProfileDescription.text = "Nivel: ${rol.nombre.uppercase()}\nJerarquía: ${rol.nivel}"
            }

            user.foto_perfil_url?.let { path ->
                val file = File(path)
                if (file.exists()) {
                    com.bumptech.glide.Glide.with(this)
                        .load(file)
                        .circleCrop()
                        .into(binding.ivProfileAvatar)
                }
            }

            loadUserSocialNetworks(user.id)
        }
    }

    private fun startImagePicker(isProfile: Boolean) {
        val options = CropImageOptions(
            imageSourceIncludeGallery = true,
            imageSourceIncludeCamera = true,
            cropShape = if (isProfile) CropImageView.CropShape.OVAL else CropImageView.CropShape.RECTANGLE,
            fixAspectRatio = true,
            aspectRatioX = 1,
            aspectRatioY = 1,
            activityTitle = "Recortar Imagen",
        )
        cropImage.launch(CropImageContractOptions(uri = null, cropImageOptions = options))
    }

    private fun saveImageLocally(uri: android.net.Uri, isProfile: Boolean) {
        lifecycleScope.launch(Dispatchers.IO) {
            try {
                val user = currentUser ?: return@launch
                val inputStream = contentResolver.openInputStream(uri)
                val bytes = inputStream?.readBytes() ?: return@launch
                
                val directory = File(filesDir, "perfil")
                if (!directory.exists()) directory.mkdirs()

                val file = File(directory, "profile_${user.id}.jpg")
                FileOutputStream(file).use { it.write(bytes) }

                val updatedUser = user.copy(foto_perfil_url = file.absolutePath)
                db.userDao().updateUsuario(updatedUser)
                currentUser = updatedUser
                
                withContext(Dispatchers.Main) {
                    updateUIWithUserData()
                    Toast.makeText(this@ProfileActivity, "Imagen actualizada", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) { e.printStackTrace() }
        }
    }

    private fun loadUserSocialNetworks(userId: Int) {
        lifecycleScope.launch {
            val redes = db.userDao().getRedesSocialesByUsuario(userId)
            binding.llSocialContainer.removeAllViews()
            redes.forEach { red ->
                val view = LayoutInflater.from(this@ProfileActivity).inflate(R.layout.item_social_icon, binding.llSocialContainer, false)
                view.findViewById<ImageView>(R.id.ivSocialIcon).setImageResource(getSocialIcon(red.tipo_nombre))
                view.setOnClickListener { openSocialUrl(red.redSocial.url) }
                binding.llSocialContainer.addView(view)
            }
            binding.llSocialContainer.addView(binding.btnAddSocial)
        }
    }

    private fun getSocialIcon(name: String): Int {
        return availableSocialTypes.find { it.name.lowercase() == name.lowercase() }?.iconRes ?: R.drawable.ic_edit_01
    }

    private fun openSocialUrl(url: String) {
        try {
            val finalUrl = if (!url.startsWith("http")) "https://$url" else url
            startActivity(android.content.Intent(android.content.Intent.ACTION_VIEW, android.net.Uri.parse(finalUrl)))
        } catch (e: Exception) {
            Toast.makeText(this, "Enlace no válido", Toast.LENGTH_SHORT).show()
        }
    }

    private fun showAddSocialDialog() {
        val dialogBinding = DialogAddSocialNetworkBinding.inflate(layoutInflater)
        val dialog = MaterialAlertDialogBuilder(this)
            .setView(dialogBinding.root)
            .create()
        
        dialog.window?.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE)

        var selectedTipoId: Int? = null

        lifecycleScope.launch {
            val tipos = db.userDao().getAllTiposRedSocial()
            withContext(Dispatchers.Main) {
                tipos.forEach { tipo ->
                    val iconView = LayoutInflater.from(this@ProfileActivity)
                        .inflate(R.layout.item_social_icon, dialogBinding.llSocialIconsSelector, false)
                    
                    val imageView = iconView.findViewById<ImageView>(R.id.ivSocialIcon)
                    val cardView = iconView.findViewById<MaterialCardView>(R.id.cardSocialIcon)
                    
                    imageView.setImageResource(getSocialIcon(tipo.nombre))
                    
                    iconView.setOnClickListener {
                        selectedTipoId = tipo.id
                        dialogBinding.tvSelectedSocialName.text = tipo.nombre
                        for (i in 0 until dialogBinding.llSocialIconsSelector.childCount) {
                            val child = dialogBinding.llSocialIconsSelector.getChildAt(i)
                            child.findViewById<MaterialCardView>(R.id.cardSocialIcon)
                                .setStrokeColor(android.graphics.Color.parseColor("#E5E7EB"))
                        }
                        cardView.setStrokeColor(android.graphics.Color.parseColor("#15803D"))
                    }
                    dialogBinding.llSocialIconsSelector.addView(iconView)
                }
            }
        }

        dialogBinding.btnSaveSocial.setOnClickListener {
            val url = dialogBinding.etSocialUrl.text.toString()
            if (selectedTipoId != null && url.isNotEmpty()) {
                lifecycleScope.launch {
                    db.userDao().insertRedSocial(RedSocialEntity(
                        usuario_id = currentUser?.id ?: 0,
                        tipo_red_id = selectedTipoId!!,
                        url = url
                    ))
                    loadUserSocialNetworks(currentUser?.id ?: 0)
                    dialog.dismiss()
                }
            }
        }
        dialog.show()
    }
}
