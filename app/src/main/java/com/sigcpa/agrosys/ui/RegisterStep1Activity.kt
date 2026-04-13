package com.sigcpa.agrosys.ui

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.sigcpa.agrosys.databinding.ActivityRegisterStep1Binding
import com.sigcpa.agrosys.repository.AuthRepository
import kotlinx.coroutines.launch

class RegisterStep1Activity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterStep1Binding
    private lateinit var authRepository: AuthRepository

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterStep1Binding.inflate(layoutInflater)
        setContentView(binding.root)

        authRepository = AuthRepository(this)
        setupUI()
    }

    private fun setupUI() {
        binding.btnNextStep.setOnClickListener {
            validateAndGoToStep2()
        }

        binding.tvSwitchToLogin.setOnClickListener {
            finish()
        }
    }

    private fun validateAndGoToStep2() {
        val name = binding.etRegName.text.toString().trim()
        val lastName = binding.etRegLastName.text.toString().trim()
        val email = binding.etRegEmail.text.toString().trim()
        val password = binding.etRegPassword.text.toString()
        val confirm = binding.etRegConfirmPassword.text.toString()
        val phone = binding.etRegPhone.text.toString().trim()

        if (name.isEmpty() || lastName.isEmpty() || email.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "⚠️ Completa los campos obligatorios", Toast.LENGTH_SHORT).show()
            return
        }

        if (password != confirm) {
            Toast.makeText(this, "❌ Las contraseñas no coinciden", Toast.LENGTH_SHORT).show()
            return
        }

        lifecycleScope.launch {
            if (authRepository.checkEmailExists(email)) {
                Toast.makeText(this@RegisterStep1Activity, "❌ Este email ya está registrado", Toast.LENGTH_SHORT).show()
            } else {
                val intent = Intent(this@RegisterStep1Activity, RegisterStep2Activity::class.java).apply {
                    putExtra("REG_NAME", name)
                    putExtra("REG_LASTNAME", lastName)
                    putExtra("REG_EMAIL", email)
                    putExtra("REG_PASSWORD", password)
                    putExtra("REG_PHONE", phone)
                }
                startActivity(intent)
            }
        }
    }
}
