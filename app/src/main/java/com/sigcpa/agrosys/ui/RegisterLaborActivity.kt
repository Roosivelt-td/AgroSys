package com.sigcpa.agrosys.ui

import android.app.DatePickerDialog
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.google.android.material.bottomsheet.BottomSheetDialog
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.databinding.*
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class RegisterLaborActivity : AppCompatActivity() {

    private lateinit var binding: ActivityRegisterLaborBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var selectedCultivoId: Int = -1
    private var currentLabor: LaborSessionItem? = null
    private val calendar = Calendar.getInstance()
    private var laborsHistory: List<LaborRealizadaEntity> = emptyList()

    // Estado de la secuencia
    private var yaPreparo = false
    private var yaSembro = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterLaborBinding.inflate(layoutInflater)
        setContentView(binding.root)

        selectedCultivoId = intent.getIntExtra("CULTIVO_ID", -1)
        binding.tvHeaderTitle.text = intent.getStringExtra("TERRENO_NOMBRE") ?: "Terreno"
        binding.tvHeaderSubtitle.text = intent.getStringExtra("CULTIVO_DETALLE") ?: "Detalle del cultivo"

        setupListeners()
        loadInitialState()
        refreshLaboresList()
    }

    private fun setupListeners() {
        binding.btnBack.setOnClickListener { finish() }
        
        // Listeners para las tarjetas de labor
        binding.cardPreparacion.setOnClickListener { handleLaborClick(1, "Preparación") }
        binding.cardSiembra.setOnClickListener { handleLaborClick(2, "Siembra") }
        binding.cardRiego.setOnClickListener { handleLaborClick(3, "Riego") }
        binding.cardFumigacion.setOnClickListener { handleLaborClick(4, "Fumigación") }
        binding.cardAporque.setOnClickListener { handleLaborClick(5, "Aporque") }
        binding.cardDeshierbe.setOnClickListener { handleLaborClick(6, "Deshierbe") }
        binding.cardFertilizacion.setOnClickListener { handleLaborClick(7, "Abonado / Fertilización") }
        binding.cardPoda.setOnClickListener { handleLaborClick(8, "Poda") }
        binding.cardCosecha.setOnClickListener { handleLaborClick(9, "Cosecha") }

        binding.btnFinalizarRegistro.setOnClickListener { showConfirmationDialog() }
    }

    private fun loadInitialState() {
        lifecycleScope.launch {
            laborsHistory = db.assetDao().getLaboresByCultivo(selectedCultivoId)
            yaPreparo = laborsHistory.any { it.catalogo_labor_id == 1 }
            yaSembro = laborsHistory.any { it.catalogo_labor_id == 2 }
            updateLaborGridState()
            updateCounts()
        }
    }

    private fun updateCounts() {
        val counts = laborsHistory.groupBy { it.catalogo_labor_id }.mapValues { it.value.size }
        binding.tvCountPreparacion.text = (counts[1] ?: 0).toString()
        binding.tvCountSiembra.text = (counts[2] ?: 0).toString()
        binding.tvCountRiego.text = (counts[3] ?: 0).toString()
        binding.tvCountFumigacion.text = (counts[4] ?: 0).toString()
        binding.tvCountAporque.text = (counts[5] ?: 0).toString()
        binding.tvCountDeshierbe.text = (counts[6] ?: 0).toString()
        binding.tvCountFertilizacion.text = (counts[7] ?: 0).toString()
        binding.tvCountOtros.text = (counts[8] ?: 0).toString()
        binding.tvCountCosecha.text = (counts[9] ?: 0).toString()
    }

    private fun handleLaborClick(laborId: Int, laborName: String) {
        val history = laborsHistory.filter { it.catalogo_labor_id == laborId }
        if (history.isNotEmpty()) {
            showHistoryModal(laborId, laborName, history)
        } else {
            openLaborDialog(laborId, laborName)
        }
    }

    private fun showHistoryModal(laborId: Int, laborName: String, history: List<LaborRealizadaEntity>) {
        val last = history.maxByOrNull { it.fecha_realizacion } ?: return
        val dialog = BottomSheetDialog(this)
        val hBinding = DialogLaborHistoryBriefBinding.inflate(layoutInflater)
        dialog.setContentView(hBinding.root)

        hBinding.tvTitle.text = "$laborName (${history.size})"
        hBinding.tvLastDate.text = "Última vez: " + SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(last.fecha_realizacion * 1000))
        
        lifecycleScope.launch {
            val mo = db.assetDao().getManoObraByLabor(last.id)
            val totalMO = mo.sumOf { it.subtotal }
            val cantMO = mo.sumOf { it.cantidad_trabajadores }
            val insumosCosto = db.assetDao().getCostoInsumosByLabor(last.id) ?: 0.0
            
            hBinding.tvSummary.text = "Gasto: S/ ${String.format("%.2f", totalMO + last.costo_maquinaria_total + insumosCosto)}\nMano de obra: $cantMO peones"
        }

        hBinding.btnVerDetalles.setOnClickListener {
            val intent = Intent(this, LaborTypeDetailActivity::class.java)
            intent.putExtra("CULTIVO_ID", selectedCultivoId)
            intent.putExtra("LABOR_ID", laborId)
            intent.putExtra("LABOR_NOMBRE", laborName)
            startActivity(intent)
            dialog.dismiss()
        }

        hBinding.btnNuevaLabor.setOnClickListener {
            openLaborDialog(laborId, laborName)
            dialog.dismiss()
        }

        dialog.show()
    }

    private fun updateLaborGridState() {
        // Bloqueos visuales según la secuencia agrícola
        binding.cardSiembra.alpha = if (yaPreparo) 1.0f else 0.4f
        val maintenanceEnabled = yaSembro
        binding.cardRiego.alpha = if (maintenanceEnabled) 1.0f else 0.4f
        binding.cardFumigacion.alpha = if (maintenanceEnabled) 1.0f else 0.4f
        binding.cardAporque.alpha = if (maintenanceEnabled) 1.0f else 0.4f
        binding.cardDeshierbe.alpha = if (maintenanceEnabled) 1.0f else 0.4f
        binding.cardFertilizacion.alpha = if (maintenanceEnabled) 1.0f else 0.4f
        binding.cardPoda.alpha = if (maintenanceEnabled) 1.0f else 0.4f
        binding.cardCosecha.alpha = if (maintenanceEnabled) 1.0f else 0.4f
    }

    private fun openLaborDialog(laborId: Int, laborName: String) {
        if (currentLabor != null) {
            androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("Labor en curso")
                .setMessage("¿Estás seguro de registrar otra labor sin antes guardar la actual?")
                .setPositiveButton("Sí, descartar y nueva") { _, _ ->
                    currentLabor = null
                    showLaborDialog(laborId, laborName)
                }
                .setNegativeButton("No", null)
                .show()
        } else {
            showLaborDialog(laborId, laborName)
        }
    }

    private fun showLaborDialog(laborId: Int, laborName: String, existingLabor: LaborSessionItem? = null) {
        // Validar secuencia antes de abrir (solo si es nueva labor)
        if (existingLabor == null) {
            if (laborId == 1 && yaPreparo) {
                Toast.makeText(this, "La preparación ya ha sido registrada anteriormente.", Toast.LENGTH_SHORT).show()
            }
            if (laborId == 2 && !yaPreparo) {
                Toast.makeText(this, "⚠️ Primero debes registrar la Preparación del terreno", Toast.LENGTH_SHORT).show()
                return
            }
            if (laborId > 2 && !yaSembro) {
                Toast.makeText(this, "⚠️ Primero debes registrar la Siembra", Toast.LENGTH_SHORT).show()
                return
            }
        }

        val dialog = BottomSheetDialog(this)
        val dBinding = DialogAddLaborBinding.inflate(layoutInflater)
        dialog.setContentView(dBinding.root)

        dBinding.tvDialogTitle.text = laborName
        
        // Si estamos editando, cargar datos previos
        existingLabor?.let { labor ->
            calendar.timeInMillis = labor.timestamp * 1000
            dBinding.etFecha.setText(SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(calendar.time))
            dBinding.etDescripcion.setText(labor.desc)
            dBinding.etCostoMaq.setText(labor.costoMaq.toString())
            
            labor.manoObra.forEach { mo ->
                if (mo.tipoId == 1) {
                    dBinding.etVaronCant.setText(mo.cantidad.toString())
                    dBinding.etVaronDias.setText(mo.dias.toString())
                    dBinding.etVaronCosto.setText(mo.costoDia.toString())
                } else if (mo.tipoId == 2) {
                    dBinding.etMujerCant.setText(mo.cantidad.toString())
                    dBinding.etMujerDias.setText(mo.dias.toString())
                    dBinding.etMujerCosto.setText(mo.costoDia.toString())
                }
            }
        }

        val updateSubtotal = {
            val varonSub = (dBinding.etVaronCant.text.toString().toDoubleOrNull() ?: 0.0) * 
                           (dBinding.etVaronDias.text.toString().toDoubleOrNull() ?: 0.0) * 
                           (dBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0)
            
            val mujerSub = (dBinding.etMujerCant.text.toString().toDoubleOrNull() ?: 0.0) * 
                            (dBinding.etMujerDias.text.toString().toDoubleOrNull() ?: 0.0) * 
                            (dBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0)

            val maq = dBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
            
            dBinding.tvLaborSubtotal.text = "S/ ${String.format("%.2f", varonSub + mujerSub + maq)}"
        }

        val textWatcher = object : android.text.TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) { updateSubtotal() }
            override fun afterTextChanged(s: android.text.Editable?) {}
        }
        
        dBinding.etVaronCant.addTextChangedListener(textWatcher)
        dBinding.etVaronDias.addTextChangedListener(textWatcher)
        dBinding.etVaronCosto.addTextChangedListener(textWatcher)
        dBinding.etMujerCant.addTextChangedListener(textWatcher)
        dBinding.etMujerDias.addTextChangedListener(textWatcher)
        dBinding.etMujerCosto.addTextChangedListener(textWatcher)
        dBinding.etCostoMaq.addTextChangedListener(textWatcher)

        dBinding.etFecha.setOnClickListener {
            DatePickerDialog(this, { _, y, m, d ->
                calendar.set(y, m, d)
                dBinding.etFecha.setText(SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(calendar.time))
            }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH)).show()
        }
        dBinding.etFecha.setText(SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(calendar.time))

        dBinding.btnAccept.setOnClickListener {
            val fechaSeleccionada = calendar.timeInMillis / 1000
            
            lifecycleScope.launch {
                // Validación de fecha: Siembra (ID 2) >= Preparación (ID 1)
                if (laborId == 2) {
                    val prepLabor = db.assetDao().getLaboresByCultivo(selectedCultivoId).find { it.catalogo_labor_id == 1 }
                    if (prepLabor != null && fechaSeleccionada < prepLabor.fecha_realizacion) {
                        val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
                        Toast.makeText(this@RegisterLaborActivity, "⚠️ La fecha de siembra no puede ser anterior a la preparación (${sdf.format(Date(prepLabor.fecha_realizacion * 1000))})", Toast.LENGTH_LONG).show()
                        return@launch
                    }
                }

                val maq = dBinding.etCostoMaq.text.toString().toDoubleOrNull() ?: 0.0
                val manoObraItems = mutableListOf<ManoObraSessionItem>()
                
                // ... (resto de la lógica de guardado de peones)
                val vCant = dBinding.etVaronCant.text.toString().toIntOrNull() ?: 0
                val vDias = dBinding.etVaronDias.text.toString().toIntOrNull() ?: 0
                val vCosto = dBinding.etVaronCosto.text.toString().toDoubleOrNull() ?: 0.0
                if (vCant > 0) manoObraItems.add(ManoObraSessionItem(1, vCant, vDias, vCosto))

                val mCant = dBinding.etMujerCant.text.toString().toIntOrNull() ?: 0
                val mDias = dBinding.etMujerDias.text.toString().toIntOrNull() ?: 0
                val mCosto = dBinding.etMujerCosto.text.toString().toDoubleOrNull() ?: 0.0
                if (mCant > 0) manoObraItems.add(ManoObraSessionItem(2, mCant, mDias, mCosto))

                currentLabor = LaborSessionItem(
                    laborId, laborName, fechaSeleccionada, 
                    dBinding.etDescripcion.text.toString(), maq, manoObraItems, existingLabor?.insumos ?: emptyList()
                )
                
                refreshLaboresList()
                dialog.dismiss()
            }
        }

        dBinding.btnCancel.setOnClickListener { dialog.dismiss() }
        dialog.show()
    }

    private fun openInsumoDialog(editingIndex: Int? = null, onSync: (List<InsumoSessionItem>) -> Unit) {
        val dialog = BottomSheetDialog(this)
        val iBinding = DialogAddInsumoBinding.inflate(layoutInflater)
        dialog.setContentView(iBinding.root)

        val tempInsumosList = currentLabor?.insumos?.toMutableList() ?: mutableListOf()
        var currentEditingIndex: Int? = editingIndex

        fun loadInsumoToEdit(index: Int) {
            val ins = tempInsumosList[index]
            iBinding.etNombreInsumo.setText(ins.nombre)
            iBinding.etCantidad.setText(ins.cantidad.toString())
            iBinding.etPrecio.setText(ins.precio.toString())
            iBinding.etFlete.setText(ins.flete.toString())
            iBinding.cbHasProveedor.isChecked = ins.proveedor != null
            iBinding.etProveedor.setText(ins.proveedor ?: "")
            iBinding.btnAcceptInsumo.text = "Actualizar Insumo"
        }

        if (currentEditingIndex != null) {
            loadInsumoToEdit(currentEditingIndex!!)
        }

        fun refreshInsumoListInDialog() {
            iBinding.llInsumosAgregados.removeAllViews()
            if (tempInsumosList.isEmpty()) {
                iBinding.tvInsumosAgregadosTitle.visibility = View.GONE
            } else {
                iBinding.tvInsumosAgregadosTitle.visibility = View.VISIBLE
                iBinding.tvInsumosTitle.text = "Insumos (${tempInsumosList.size})"
                
                tempInsumosList.forEachIndexed { index, ins ->
                    val row = ItemInsumoRowBinding.inflate(layoutInflater, iBinding.llInsumosAgregados, false)
                    row.tvInsumoNombre.text = ins.nombre
                    
                    lifecycleScope.launch {
                        val unidad = db.assetDao().getUnidadInsumo(ins.catId) ?: ""
                        row.tvInsumoCant.text = "${ins.cantidad} $unidad"
                    }
                    
                    row.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.precio) + ins.flete)}"
                    
                    row.root.setOnClickListener {
                        androidx.appcompat.app.AlertDialog.Builder(this)
                            .setTitle("Editar Insumo")
                            .setMessage("¿Desea editar o eliminar este insumo?")
                            .setPositiveButton("Editar") { _, _ ->
                                loadInsumoToEdit(index)
                                currentEditingIndex = index
                            }
                            .setNegativeButton("Eliminar") { _, _ ->
                                tempInsumosList.removeAt(index)
                                refreshInsumoListInDialog()
                                onSync(tempInsumosList)
                            }
                            .show()
                    }
                    iBinding.llInsumosAgregados.addView(row.root)
                }
            }
        }

        refreshInsumoListInDialog()

        iBinding.cbHasProveedor.setOnCheckedChangeListener { _, isChecked ->
            iBinding.llProveedor.visibility = if (isChecked) View.VISIBLE else View.GONE
        }

        lifecycleScope.launch {
            val catalogo = db.assetDao().getCatalogoInsumos()
            val adapter = ArrayAdapter(this@RegisterLaborActivity, android.R.layout.simple_dropdown_item_1line, catalogo.map { it.nombre })
            iBinding.etNombreInsumo.setAdapter(adapter)
        }

        iBinding.btnAcceptInsumo.setOnClickListener {
            val nombre = iBinding.etNombreInsumo.text.toString()
            val cant = iBinding.etCantidad.text.toString().toDoubleOrNull() ?: 0.0
            val precio = iBinding.etPrecio.text.toString().toDoubleOrNull() ?: 0.0
            val flete = iBinding.etFlete.text.toString().toDoubleOrNull() ?: 0.0
            val unidad = iBinding.etUnidad.text.toString()
            val proveedor = if (iBinding.cbHasProveedor.isChecked) iBinding.etProveedor.text.toString() else null

            if (nombre.isNotEmpty() && cant > 0) {
                lifecycleScope.launch {
                    val catalogo = db.assetDao().getCatalogoInsumos()
                    var catId = catalogo.find { it.nombre.lowercase() == nombre.lowercase() }?.id
                    
                    if (catId == null) {
                        catId = db.assetDao().insertCatalogoInsumo(CatalogoInsumoEntity(
                            nombre = nombre,
                            categoria = "Otros",
                            unidad_medida = unidad,
                            descripcion = ""
                        )).toInt()
                    }
                    
                    val insumo = InsumoSessionItem(catId, nombre, cant, precio, flete, proveedor)
                    
                    if (currentEditingIndex != null) {
                        tempInsumosList[currentEditingIndex!!] = insumo
                        currentEditingIndex = null
                        iBinding.btnAcceptInsumo.text = "Agregar Insumo"
                    } else {
                        tempInsumosList.add(insumo)
                    }

                    onSync(tempInsumosList)
                    refreshInsumoListInDialog()

                    // Limpiar campos
                    iBinding.etNombreInsumo.setText("")
                    iBinding.etCantidad.setText("")
                    iBinding.etPrecio.setText("")
                    iBinding.etFlete.setText("")
                    iBinding.etProveedor.setText("")

                    androidx.appcompat.app.AlertDialog.Builder(this@RegisterLaborActivity)
                        .setTitle("¿Agregar más?")
                        .setMessage("¿Deseas agregar otro insumo para esta labor?")
                        .setPositiveButton("Sí") { _, _ -> /* Se queda en el modal */ }
                        .setNegativeButton("No, listo") { _, _ -> dialog.dismiss() }
                        .show()
                }
            } else {
                Toast.makeText(this, "Completa los datos del insumo", Toast.LENGTH_SHORT).show()
            }
        }

        iBinding.btnCancelInsumo.setOnClickListener { dialog.dismiss() }
        dialog.show()
    }

    private fun refreshLaboresList() {
        binding.llLaboresList.removeAllViews()
        val labor = currentLabor
        
        if (labor == null) {
            binding.tvLaboresListTitle.text = "Labor para registrar en esta sesión:"
            binding.tvEmptyLabores.visibility = View.VISIBLE
            binding.tvEmptyLabores.text = "No has seleccionado ninguna labor todavía"
            binding.btnFinalizarRegistro.isEnabled = false
            binding.tvCostoTotalSession.text = "S/ 0.00"
        } else {
            binding.tvLaboresListTitle.text = "Labor a registrar: ${labor.name}"
            binding.tvEmptyLabores.visibility = View.GONE
            binding.btnFinalizarRegistro.isEnabled = true
            
            val itemBinding = ItemLaborDetalleBinding.inflate(layoutInflater, binding.llLaboresList, false)
            itemBinding.tvLaborNombre.text = labor.name
            itemBinding.tvLaborFecha.text = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(Date(labor.timestamp * 1000))
            
            val costoMO = labor.manoObra.sumOf { it.cantidad * it.dias * it.costoDia }
            itemBinding.tvLaborCostos.text = "MO: S/ ${String.format("%.2f", costoMO)} | Maq: S/ ${String.format("%.2f", labor.costoMaq)}"
            
            val subtotalInsumos = labor.insumos.sumOf { (it.cantidad * it.precio) + it.flete }
            itemBinding.tvLaborInsumosCount.text = "${labor.insumos.size} items"
            
            val subtotal = costoMO + labor.costoMaq + subtotalInsumos
            itemBinding.tvLaborTotal.text = "Subtotal: S/ ${String.format("%.2f", subtotal)}"
            binding.tvCostoTotalSession.text = "S/ ${String.format("%.2f", subtotal)}"

            // Listar detalles de insumos bajo la labor
            itemBinding.llInsumosDetalle.removeAllViews()
            labor.insumos.forEachIndexed { index, ins ->
                val insRow = ItemInsumoRowBinding.inflate(layoutInflater, itemBinding.llInsumosDetalle, false)
                insRow.tvInsumoNombre.text = ins.nombre
                
                lifecycleScope.launch {
                    val unidad = db.assetDao().getUnidadInsumo(ins.catId) ?: ""
                    val cantStr = if (ins.cantidad % 1.0 == 0.0) ins.cantidad.toInt().toString() else ins.cantidad.toString()
                    insRow.tvInsumoCant.text = "$cantStr $unidad"
                }

                insRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.precio) + ins.flete)}"
                
                insRow.btnEditInsumo.visibility = View.VISIBLE
                insRow.btnRemoveInsumo.visibility = View.VISIBLE
                
                insRow.btnEditInsumo.setOnClickListener {
                    openInsumoDialog(index) { nuevosInsumos ->
                        currentLabor = labor.copy(insumos = nuevosInsumos)
                        refreshLaboresList()
                    }
                }
                
                insRow.btnRemoveInsumo.setOnClickListener {
                    val nuevosInsumos = labor.insumos.toMutableList()
                    nuevosInsumos.removeAt(index)
                    currentLabor = labor.copy(insumos = nuevosInsumos)
                    refreshLaboresList()
                }

                insRow.root.setOnLongClickListener {
                    androidx.appcompat.app.AlertDialog.Builder(this)
                        .setMessage("¿Deseas editar este insumo?")
                        .setPositiveButton("Sí") { _, _ ->
                            openInsumoDialog(index) { nuevosInsumos ->
                                currentLabor = labor.copy(insumos = nuevosInsumos)
                                refreshLaboresList()
                            }
                        }
                        .setNegativeButton("No", null)
                        .show()
                    true
                }

                itemBinding.llInsumosDetalle.addView(insRow.root)
            }

            itemBinding.btnAddInsumoItem.setOnClickListener {
                openInsumoDialog { nuevosInsumos ->
                    currentLabor = labor.copy(insumos = nuevosInsumos)
                    refreshLaboresList()
                }
            }

            itemBinding.btnEditLabor.setOnClickListener {
                showLaborDialog(labor.id, labor.name, labor)
            }

            itemBinding.btnRemoveLabor.setOnClickListener {
                currentLabor = null
                refreshLaboresList()
            }

            itemBinding.root.setOnLongClickListener {
                androidx.appcompat.app.AlertDialog.Builder(this)
                    .setMessage("¿Deseas editar esta labor?")
                    .setPositiveButton("Sí") { _, _ ->
                        showLaborDialog(labor.id, labor.name, labor)
                    }
                    .setNegativeButton("No", null)
                    .show()
                true
            }

            binding.llLaboresList.addView(itemBinding.root)
        }
    }

    private fun recalculateSequenceState() {
        lifecycleScope.launch {
            val realizadas = db.assetDao().getLaboresByCultivo(selectedCultivoId)
            val laborId = currentLabor?.id
            
            yaPreparo = realizadas.any { it.catalogo_labor_id == 1 } || laborId == 1
            yaSembro = realizadas.any { it.catalogo_labor_id == 2 } || laborId == 2
            
            updateLaborGridState()
        }
    }

    private fun showConfirmationDialog() {
        val labor = currentLabor ?: return
        val dialog = BottomSheetDialog(this)
        val cBinding = DialogConfirmRegistroBinding.inflate(layoutInflater)
        dialog.setContentView(cBinding.root)

        val costoMO = labor.manoObra.sumOf { it.cantidad * it.dias * it.costoDia }
        val total = costoMO + labor.costoMaq + labor.insumos.sumOf { i -> (i.cantidad * i.precio) + i.flete }
        
        cBinding.tvResumenFinal.visibility = View.GONE
        
        // Crear una tabla o vista detallada para la confirmación
        val detailView = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(0, 16, 0, 16)
        }

        // Título de la labor
        val laborTitle = TextView(this).apply {
            text = labor.name.uppercase()
            textSize = 14f
            setTextColor(androidx.core.content.ContextCompat.getColor(this@RegisterLaborActivity, android.R.color.black))
            setTypeface(null, android.graphics.Typeface.BOLD)
        }
        detailView.addView(laborTitle)

        // Detalle de costos directos
        val directCosts = TextView(this).apply {
            text = "Costos Directos: S/ ${String.format("%.2f", costoMO + labor.costoMaq)}"
            textSize = 13f
            setPadding(0, 4, 0, 8)
        }
        detailView.addView(directCosts)

        // Tabla de Insumos si existen
        if (labor.insumos.isNotEmpty()) {
            val insumosTitle = TextView(this).apply {
                text = "Insumos Detallados:"
                textSize = 12f
                setTypeface(null, android.graphics.Typeface.BOLD)
                setPadding(0, 8, 0, 4)
            }
            detailView.addView(insumosTitle)

            labor.insumos.forEach { ins ->
                val insRow = ItemInsumoRowBinding.inflate(layoutInflater)
                insRow.tvInsumoNombre.text = ins.nombre
                
                lifecycleScope.launch {
                    val unidad = db.assetDao().getUnidadInsumo(ins.catId) ?: ""
                    val cantStr = if (ins.cantidad % 1.0 == 0.0) ins.cantidad.toInt().toString() else ins.cantidad.toString()
                    insRow.tvInsumoCant.text = "$cantStr $unidad"
                }

                insRow.tvInsumoSubtotal.text = "S/ ${String.format("%.2f", (ins.cantidad * ins.precio) + ins.flete)}"
                detailView.addView(insRow.root)
            }
        }

        val totalView = TextView(this).apply {
            text = "\nTOTAL A REGISTRAR: S/ ${String.format("%.2f", total)}"
            textSize = 16f
            setTextColor(androidx.core.content.ContextCompat.getColor(this@RegisterLaborActivity, R.color.green_primary))
            setTypeface(null, android.graphics.Typeface.BOLD)
            gravity = android.view.Gravity.END
        }
        detailView.addView(totalView)

        // Insertar la vista de detalle antes de los botones
        val parent = cBinding.tvResumenFinal.parent as LinearLayout
        val index = parent.indexOfChild(cBinding.tvResumenFinal)
        parent.addView(detailView, index + 1)

        cBinding.btnConfirmar.setOnClickListener {
            saveEverything()
            dialog.dismiss()
        }
        cBinding.btnRevisar.setOnClickListener { dialog.dismiss() }
        dialog.show()
    }

    private fun saveEverything() {
        val item = currentLabor ?: return
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val costoMO_total = item.manoObra.sumOf { it.cantidad * it.dias * it.costoDia }
            val laborId = db.assetDao().insertLabor(LaborRealizadaEntity(
                cultivo_id = selectedCultivoId,
                catalogo_labor_id = item.id,
                fecha_realizacion = item.timestamp,
                costo_mano_obra_total = costoMO_total,
                costo_maquinaria_total = item.costoMaq,
                observaciones = item.desc,
                creado_por_usuario_id = userId
            )).toInt()

            item.manoObra.forEach { mo ->
                db.assetDao().insertManoObra(ManoObraEntity(
                    labor_realizada_id = laborId,
                    tipo_id = mo.tipoId,
                    cantidad_trabajadores = mo.cantidad,
                    dias_trabajados = mo.dias,
                    costo_por_dia = mo.costoDia,
                    subtotal = mo.cantidad * mo.dias * mo.costoDia
                ))
            }

            item.insumos.forEach { ins ->
                db.assetDao().insertInsumoUsado(InsumoUsadoEntity(
                    labor_id = laborId,
                    catalogo_insumo_id = ins.catId,
                    proveedor_id = null,
                    cantidad = ins.cantidad,
                    costo_unitario = ins.precio,
                    costo_flete = ins.flete,
                    nombre_proveedor_manual = ins.proveedor
                ))
            }
            
            Toast.makeText(this@RegisterLaborActivity, "✅ Registro de labor guardado", Toast.LENGTH_SHORT).show()
            
            // Limpiar para un nuevo registro sin cerrar la actividad
            currentLabor = null
            binding.tvCostoTotalSession.text = "S/ 0.00"
            refreshLaboresList() // Limpia el contenedor de la lista visual
            updateCounts()      // Actualiza los contadores en el grid superior
        }
    }

    data class LaborSessionItem(val id: Int, val name: String, val timestamp: Long, val desc: String, val costoMaq: Double, val manoObra: List<ManoObraSessionItem>, val insumos: List<InsumoSessionItem>)
    data class ManoObraSessionItem(val tipoId: Int, val cantidad: Int, val dias: Int, val costoDia: Double)
    data class InsumoSessionItem(val catId: Int, val nombre: String, val cantidad: Double, val precio: Double, val flete: Double = 0.0, val proveedor: String? = null)
}
