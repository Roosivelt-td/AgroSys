package com.sigcpa.agrosys.ui

data class ChatMessage(val text: String, val isUser: Boolean, val timestamp: Long = System.currentTimeMillis())
