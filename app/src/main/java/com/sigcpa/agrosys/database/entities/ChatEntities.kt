package com.sigcpa.agrosys.database.entities

import androidx.room.*

@Entity(
    tableName = "chat_sessions",
    foreignKeys = [
        ForeignKey(
            entity = UsuarioEntity::class,
            parentColumns = ["id"],
            childColumns = ["usuario_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["usuario_id"])]
)
data class ChatSessionEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val usuario_id: Int,
    val titulo: String,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000,
    val last_message_at: Long = System.currentTimeMillis() / 1000,
    val is_archived: Boolean = false,
    val is_ai: Boolean = true,
    val contact_id: Int? = null
)

@Entity(
    tableName = "chat_messages_history",
    foreignKeys = [
        ForeignKey(
            entity = ChatSessionEntity::class,
            parentColumns = ["id"],
            childColumns = ["session_id"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["session_id"])]
)
data class ChatMessageHistoryEntity(
    @PrimaryKey(autoGenerate = true) val id: Int = 0,
    val session_id: Int,
    val text: String,
    val is_user: Boolean,
    @ColumnInfo(defaultValue = "(strftime('%s', 'now'))") val created_at: Long = System.currentTimeMillis() / 1000
)
