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

    @Update
    suspend fun updateAgricultor(agricultor: AgricultorEntity)

    @Query("SELECT * FROM usuarios WHERE id = :id")
    suspend fun getUsuarioById(id: Int): UsuarioEntity?

    @Query("SELECT * FROM agricultores WHERE usuario_id = :userId LIMIT 1")
    suspend fun getAgricultorByUserId(userId: Int): AgricultorEntity?

    @Query("SELECT COUNT(*) FROM usuarios WHERE email = :email")
    suspend fun countUsersByEmail(email: String): Int

    // --- Redes Sociales ---
    @Query("SELECT * FROM tipos_red_social")
    suspend fun getAllTiposRedSocial(): List<TipoRedSocialEntity>

    @Query("""
        SELECT rs.*, trs.nombre as tipo_nombre, trs.color_hex 
        FROM redes_sociales rs 
        JOIN tipos_red_social trs ON rs.tipo_red_id = trs.id 
        WHERE rs.usuario_id = :userId
    """)
    suspend fun getRedesSocialesByUsuario(userId: Int): List<RedSocialWithMetadata>

    @Insert
    suspend fun insertRedSocial(redSocial: RedSocialEntity): Long

    @Update
    suspend fun updateRedSocial(redSocial: RedSocialEntity)

    @Delete
    suspend fun deleteRedSocial(redSocial: RedSocialEntity)
}

data class RedSocialWithMetadata(
    @Embedded val redSocial: RedSocialEntity,
    val tipo_nombre: String,
    val color_hex: String?
)
