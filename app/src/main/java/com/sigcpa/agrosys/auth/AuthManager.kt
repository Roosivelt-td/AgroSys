package com.sigcpa.agrosys.auth

import com.google.firebase.auth.FirebaseAuth
import com.google.firebase.firestore.FirebaseFirestore

class AuthManager {
    private val auth = FirebaseAuth.getInstance()
    private val db = FirebaseFirestore.getInstance()

    fun login(email: String, password: String, onResult: (Boolean, String?) -> Unit) {
        auth.signInWithEmailAndPassword(email, password)
            .addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    onResult(true, null)
                } else {
                    onResult(false, task.exception?.message)
                }
            }
    }

    fun register(userData: Map<String, String>, onResult: (Boolean, String?) -> Unit) {
        val email = userData["email"] ?: ""
        val password = userData["password"] ?: ""

        auth.createUserWithEmailAndPassword(email, password)
            .addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    val userId = auth.currentUser?.uid
                    if (userId != null) {
                        db.collection("users").document(userId)
                            .set(userData.filterKeys { it != "password" })
                            .addOnSuccessListener { onResult(true, null) }
                            .addOnFailureListener { onResult(false, it.message) }
                    }
                } else {
                    onResult(false, task.exception?.message)
                }
            }
    }
}