<?php
// Database configuration
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "sweets_course";

// Telegram Bot configuration
$botToken = "8168229607:AAG1vhgduwBlIRAghxAkDu8LOVp8uLsQvdo";
$chatId = "7220908910";

// Validate configuration
if (empty($botToken) || empty($chatId)) {
    error_log("Telegram configuration is missing!");
    throw new Exception("Telegram configuration error");
}

// Check if bot token format is valid
if (!preg_match('/^\d+:[A-Za-z0-9_-]+$/', $botToken)) {
    error_log("Invalid bot token format!");
    throw new Exception("Invalid bot token format");
}

// Check if chat ID is numeric
if (!is_numeric($chatId)) {
    error_log("Invalid chat ID format!");
    throw new Exception("Invalid chat ID format");
}
?>