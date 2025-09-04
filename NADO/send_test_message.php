<?php
require_once 'config.php';

$url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
$message = "🧪 رسالة اختبار من موقع دورة الأستاذة نادية\n";
$message .= "📅 التاريخ: " . date('d/m/Y') . "\n";
$message .= "⏰ الوقت: " . date('H:i:s') . "\n";
$message .= "✅ النظام يعمل بشكل ممتاز!";

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

if ($httpCode === 200 && empty($error)) {
    $response = json_decode($result, true);
    if ($response && $response['ok']) {
        echo "✅ تم إرسال الرسالة بنجاح!\n";
        echo "📱 تحقق من بوت التيليجرام الآن\n";
        echo "🆔 Message ID: " . $response['result']['message_id'];
    } else {
        echo "❌ فشل الإرسال: " . ($response['description'] ?? 'خطأ غير معروف');
    }
} else {
    echo "❌ خطأ في الاتصال: HTTP " . $httpCode;
    if (!empty($error)) {
        echo "\n❌ خطأ cURL: " . $error;
    }
}
?>