<?php
// Simple debug script to test Telegram connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>اختبار اتصال بوت التيليجرام</h2>";

try {
    require_once 'config.php';
    echo "<p>✅ تم تحميل ملف التكوين بنجاح</p>";
    
    echo "<p><strong>Bot Token:</strong> " . substr($botToken, 0, 10) . "...</p>";
    echo "<p><strong>Chat ID:</strong> " . $chatId . "</p>";
    
    // Test simple message
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    $message = "🧪 اختبار الاتصال - " . date('Y-m-d H:i:s');
    
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
    
    echo "<h3>نتيجة الاختبار:</h3>";
    echo "<p><strong>HTTP Code:</strong> " . $httpCode . "</p>";
    
    if (!empty($error)) {
        echo "<p style='color: red;'><strong>cURL Error:</strong> " . $error . "</p>";
    }
    
    if ($result) {
        $response = json_decode($result, true);
        echo "<p><strong>Response:</strong></p>";
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
        if ($response && $response['ok']) {
            echo "<p style='color: green;'>✅ تم إرسال الرسالة بنجاح!</p>";
        } else {
            echo "<p style='color: red;'>❌ فشل في إرسال الرسالة</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ لم يتم الحصول على استجابة</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>خطأ:</strong> " . $e->getMessage() . "</p>";
}
?>