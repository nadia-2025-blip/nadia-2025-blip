<?php
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html dir='rtl'>";
echo "<head><meta charset='utf-8'><title>اختبار بوت التيليجرام</title></head>";
echo "<body style='font-family: Arial; padding: 20px; background: #f5f5f5;'>";
echo "<div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h2 style='color: #D2691E; text-align: center;'>🤖 اختبار بوت التيليجرام</h2>";

try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ تم تحميل ملف التكوين</p>";
    
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    $message = "🧪 رسالة اختبار من موقع دورة الأستاذة نادية\n";
    $message .= "⏰ التوقيت: " . date('d/m/Y - H:i:s') . "\n";
    $message .= "✅ البوت يعمل بشكل ممتاز!";
    
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
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<strong>نتائج الاختبار:</strong><br>";
    echo "HTTP Code: " . $httpCode . "<br>";
    
    if (!empty($error)) {
        echo "<span style='color: red;'>خطأ cURL: " . $error . "</span><br>";
    }
    
    if ($result) {
        $response = json_decode($result, true);
        if ($response && $response['ok']) {
            echo "<span style='color: green; font-size: 18px;'>🎉 تم إرسال الرسالة بنجاح!</span><br>";
            echo "<small>تحقق من بوت التيليجرام الآن</small>";
        } else {
            echo "<span style='color: red;'>❌ فشل الإرسال: " . ($response['description'] ?? 'خطأ غير معروف') . "</span>";
        }
    } else {
        echo "<span style='color: red;'>❌ لم يتم الحصول على استجابة</span>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red; background: #ffe6e6; padding: 10px; border-radius: 5px;'>";
    echo "<strong>خطأ:</strong> " . $e->getMessage();
    echo "</p>";
}

echo "</div></body></html>";
?>