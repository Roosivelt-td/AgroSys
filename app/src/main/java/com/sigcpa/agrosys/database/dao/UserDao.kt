package com.sigcpa.agrosys.database.dao

import androidx.room.*
import com.sigcpa.agrosys.database.entities.*

@Dao
interface UserDao {
    @Insert(onConflict = OnConflictStrategy.ABORT)
    suspend fun insertUsuario(usuario: UsuarioEntity): Long

    @Update
    suspend fun updateUsuario(usuario: UsuarioEntity)

    @Query("SELECT * FROM usuarios WHERE email = :email LIMIT 1")
    suspend fun getUsuarioByEmail(email: String): UsuarioEntity?

    @Insert
    suspend fun insertOrganizacion(organizacion: OrganizacionEntity): Long

    @Insert(onConflict = OnConflictStrategy.ABORT)
    suspend fun insertAgricultor(agricultor: AgricultorEntity): Long

    @Query("SELECT * FROM usuarios WHERE id = :id")
    suspend fun getUsuarioById(id: Int): UsuarioEntity?

    @Query("SELECT * FROM agricultores WHERE usuario_id = :userId LIMIT 1")
    suspend fun getAgricultorByUserId(userId: Int): AgricultorEntity?

    @Query("SELECT COUNT(*) FROM usuarios WHERE email = :email")
    suspend fun countUsersByEmail(email: String): Int
}
