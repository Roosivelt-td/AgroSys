package com.sigcpa.agrosys.ui

import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.sigcpa.agrosys.R
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
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

        // Insets & Branding
        window.statusBarColor = Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        ViewCompat.setOnApplyWindowInsetsListener(binding.headerContainer) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, systemBars.top, v.paddingRight, v.paddingBottom)
            insets
        }

        authRepository = AuthRepository(this)
        setupUI()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener {
            finish()
        }

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
            Toast.makeText(this, getString(R.string.msg_error_campos_obligatorios), Toast.LENGTH_SHORT).show()
            return
        }

        if (password != confirm) {
            Toast.makeText(this, getString(R.string.msg_error_passwords_no_coinciden), Toast.LENGTH_SHORT).show()
            return
        }

        lifecycleScope.launch {
            if (authRepository.checkEmailExists(email)) {
                Toast.makeText(this@RegisterStep1Activity, getString(R.string.msg_error_email_registrado), Toast.LENGTH_SHORT).show()
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
