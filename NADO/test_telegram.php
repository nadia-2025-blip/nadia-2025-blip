<?php
// Test Telegram Bot Connection
require_once 'config.php';

function testTelegramBot($botToken, $chatId) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    
    $message = "🧪 اختبار الاتصال مع بوت التيليجرام\n";
    $message .= "⏰ الوقت: " . date('d/m/Y - H:i:s') . "\n";
    $message .= "✅ البوت يعمل بشكل صحيح!";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'success' => $httpCode === 200 && empty($error),
        'http_code' => $httpCode,
        'error' => $error,
        'response' => $result
    ];
}

// Run test
$testResult = testTelegramBot($botToken, $chatId);

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'bot_token' => substr($botToken, 0, 10) . '...',
    'chat_id' => $chatId,
    'test_result' => $testResult
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>