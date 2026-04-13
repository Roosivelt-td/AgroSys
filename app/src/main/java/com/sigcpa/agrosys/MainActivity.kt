package com.sigcpa.agrosys

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.text.method.HideReturnsTransformationMethod
import android.text.method.PasswordTransformationMethod
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.databinding.ActivityMainBinding
import com.sigcpa.agrosys.repository.AuthRepository
import com.sigcpa.agrosys.ui.DashboardActivity
import com.sigcpa.agrosys.ui.RegisterStep1Activity
import kotlinx.coroutines.launch

class MainActivity : AppCompatActivity() {
    private lateinit var binding: ActivityMainBinding
    private lateinit var authRepository: AuthRepository
    private var isPasswordVisible = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        authRepository = AuthRepository(this)
        
        // Verificar sesión activa
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)
        if (userId != -1) {
            navigateToDashboard(userId)
            return
        }

        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupUI()
    }

    private fun setupUI() {
        // Mostrar/Ocultar Contraseña
        binding.ivTogglePassword.setOnClickListener {
            isPasswordVisible = !isPasswordVisible
            if (isPasswordVisible) {
                binding.etLoginPassword.transformationMethod = HideReturnsTransformationMethod.getInstance()
                binding.ivTogglePassword.setImageResource(android.R.drawable.ic_menu_close_clear_cancel)
            } else {
                binding.etLoginPassword.transformationMethod = PasswordTransformationMethod.getInstance()
                binding.ivTogglePassword.setImageResource(android.R.drawable.ic_menu_view)
            }
            binding.etLoginPassword.setSelection(binding.etLoginPassword.text.length)
        }

        // Botón Ingresar
        binding.btnLogin.setOnClickListener {
            val email = binding.etLoginEmail.text.toString().trim()
            val pass = binding.etLoginPassword.text.toString().trim()

            if (email.isNotEmpty() && pass.isNotEmpty()) {
                lifecycleScope.launch {
                    val user = authRepository.loginLocal(email, pass)
                    if (user != null) {
                        saveSession(user.id)
                        navigateToDashboard(user.id)
                    } else {
                        Toast.makeText(this@MainActivity, "❌ Correo o contraseña incorrectos", Toast.LENGTH_SHORT).show()
                    }
                }
            } else {
                Toast.makeText(this, "⚠️ Completa todos los campos", Toast.LENGTH_SHORT).show()
            }
        }

        // Enlace para registrarse -> Paso 1
        binding.tvSwitchToRegister.setOnClickListener {
            startActivity(Intent(this, RegisterStep1Activity::class.java))
        }
    }

    private fun saveSession(userId: Int) {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        with(sharedPref.edit()) {
            putInt("USER_ID", userId)
            apply()
        }
    }

    private fun navigateToDashboard(userId: Int) {
        lifecycleScope.launch {
            val user = authRepository.getUserById(userId)
            val intent = Intent(this@MainActivity, DashboardActivity::class.java).apply {
                putExtra("USER_NAME", user?.nombre ?: "Usuario")
                putExtra("USER_ROLE", user?.rol ?: "agricultor")
            }
            startActivity(intent)
            finish()
        }
    }
}
