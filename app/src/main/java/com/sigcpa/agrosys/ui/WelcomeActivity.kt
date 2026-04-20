package com.sigcpa.agrosys.ui

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import androidx.viewpager2.widget.ViewPager2
import com.google.android.material.tabs.TabLayoutMediator
import com.sigcpa.agrosys.MainActivity
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.databinding.ActivityWelcomeBinding

class WelcomeActivity : AppCompatActivity() {

    private lateinit var binding: ActivityWelcomeBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        if (!sharedPref.getBoolean("IS_FIRST_TIME", true)) {
            startActivity(Intent(this, MainActivity::class.java))
            finish()
            return
        }

        binding = ActivityWelcomeBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupViewPager()
    }

    private fun setupViewPager() {
        val slides = listOf(
            WelcomeSlide(
                R.drawable.agro_planta_buena,
                R.drawable.title_02,
                "¡Bienvenido a AgroSys!", 
                "Tu aliado inteligente para la gestión agrícola",
                "Controla tus cultivos, terrenos y finanzas desde un solo lugar"
            ),
            WelcomeSlide(
                R.drawable.ic_money_01,
                R.drawable.title_02, 
                "Controla tus Finanzas",
                "Registra costos, ingresos y obtén reportes de rentabilidad",
                "Visualiza ganancias por cultivo y toma mejores decisiones"
            ),
            WelcomeSlide(
                R.drawable.ic_lista,
                R.drawable.title_02,
                "Organiza tus Labores",
                "Registra cada actividad agrícola con costos detallados",
                "Mano de obra, maquinaria e insumos en un solo registro"
            )
        )

        val adapter = WelcomeAdapter(slides)
        binding.viewPager.adapter = adapter

        // Vincular TabLayout (dots) con ViewPager2
        TabLayoutMediator(binding.tabLayout, binding.viewPager) { _, _ -> }.attach()

        // Lógica de botones Siguiente/Anterior
        binding.btnNext.setOnClickListener {
            if (binding.viewPager.currentItem < slides.size - 1) {
                binding.viewPager.currentItem += 1
            } else {
                finishWelcome()
            }
        }

        binding.btnBack.setOnClickListener {
            if (binding.viewPager.currentItem > 0) {
                binding.viewPager.currentItem -= 1
            }
        }

        // Actualizar visibilidad de botones según la página
        binding.viewPager.registerOnPageChangeCallback(object : ViewPager2.OnPageChangeCallback() {
            override fun onPageSelected(position: Int) {
                binding.btnBack.visibility = if (position == 0) View.INVISIBLE else View.VISIBLE
                binding.btnNext.text = if (position == slides.size - 1) "Comenzar" else "Siguiente"
            }
        })
    }

    private fun finishWelcome() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        with(sharedPref.edit()) {
            putBoolean("IS_FIRST_TIME", false)
            apply()
        }
        startActivity(Intent(this, MainActivity::class.java))
        finish()
    }

    data class WelcomeSlide(
        val iconRes: Int,
        val titleRes: Int, 
        val mainTitle: String,
        val subtitle: String,
        val footer: String
    )

    inner class WelcomeAdapter(private val slides: List<WelcomeSlide>) : RecyclerView.Adapter<WelcomeAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val view = LayoutInflater.from(parent.context).inflate(R.layout.item_welcome_slide, parent, false)
            return ViewHolder(view)
        }
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val slide = slides[position]
            holder.ivMainIcon.setImageResource(slide.iconRes)
            holder.ivTitleLogo.setImageResource(slide.titleRes)
            holder.tvMainTitle.text = slide.mainTitle
            holder.tvSubtitle.text = slide.subtitle
            holder.tvFooterText.text = slide.footer
        }
        override fun getItemCount() = slides.size
        inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
            val ivMainIcon: ImageView = view.findViewById(R.id.ivMainIcon)
            val ivTitleLogo: ImageView = view.findViewById(R.id.ivTitleLogo)
            val tvMainTitle: TextView = view.findViewById(R.id.tvMainTitle)
            val tvSubtitle: TextView = view.findViewById(R.id.tvSubtitle)
            val tvFooterText: TextView = view.findViewById(R.id.tvFooterText)
        }
    }
}
