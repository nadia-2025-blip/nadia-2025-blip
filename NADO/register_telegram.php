<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load configuration
require_once 'config.php';

function sendTelegramMessage($botToken, $chatId, $message, $keyboard = null) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    
    // Use cURL instead of file_get_contents for better error handling
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
    
    if ($result === false || !empty($error)) {
        throw new Exception('cURL Error: ' . $error);
    }
    
    if ($httpCode !== 200) {
        throw new Exception('HTTP Error: ' . $httpCode);
    }
    
    $response = json_decode($result, true);
    if (!$response || !$response['ok']) {
        throw new Exception('Telegram API Error: ' . ($response['description'] ?? 'Unknown error'));
    }
    
    return $result;
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }
    
    // Get and validate input
    $inputRaw = file_get_contents('php://input');
    if (empty($inputRaw)) {
        throw new Exception('No data received');
    }
    
    $input = json_decode($inputRaw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Validate required fields
    $requiredFields = ['fullName', 'phone', 'city'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field]) || trim($input[$field]) === '') {
            throw new Exception('Missing required field: ' . $field);
        }
    }
    
    $fullName = trim($input['fullName']);
    $phone = trim($input['phone']);
    $city = trim($input['city']);
    $customCity = !empty($input['customCity']) ? trim($input['customCity']) : null;
    
    // Handle custom city
    if ($city === 'other' && !empty($customCity)) {
        $city = $customCity;
    }
    
    // Validate phone number format
    if (!preg_match('/^0[567]\d{8}$/', $phone)) {
        throw new Exception('Invalid phone number format');
    }
    
    // تحديد نوع الشبكة
    $network = "";
    if (substr($phone, 0, 2) === "05") $network = "Ooredoo";
    elseif (substr($phone, 0, 2) === "06") $network = "Mobilis";
    elseif (substr($phone, 0, 2) === "07") $network = "Djezzy";
    else $network = "غير محدد";
    
    // تنسيق الوقت
    date_default_timezone_set('Africa/Algiers');
    $timeFormatted = date('d/m/Y - H:i');
    
    // إنشاء رسالة بسيطة وواضحة
    $message = "🎯 تسجيل جديد في دورة الأستاذة نادية\n\n";
    $message .= "👤 الاسم: " . $fullName . "\n";
    $message .= "📱 الهاتف: " . $phone . " (" . $network . ")\n";
    $message .= "🏙️ البلدية: " . $city . "\n";
    $message .= "⏰ التوقيت: " . $timeFormatted . "\n\n";
    $message .= "💬 رابط واتساب: https://wa.me/213" . substr($phone, 1);
    
    // إرسال الرسالة للأدمن
    $result = sendTelegramMessage($botToken, $chatId, $message);
    
    // إنشاء رسالة تأكيد للمستخدم
    $userPhone = '213' . substr($phone, 1); // تحويل للصيغة الدولية
    $userMessage = "🎉 مرحباً " . $fullName . "!\n\n";
    $userMessage .= "✅ تم تسجيلك بنجاح في دورة الحلويات مع الأستاذة نادية\n\n";
    $userMessage .= "📋 بياناتك المسجلة:\n";
    $userMessage .= "👤 الاسم: " . $fullName . "\n";
    $userMessage .= "📱 الهاتف: " . $phone . "\n";
    $userMessage .= "🏙️ البلدية: " . $city . "\n";
    $userMessage .= "⏰ وقت التسجيل: " . $timeFormatted . "\n\n";
    $userMessage .= "📞 ستتصل بك الأستاذة نادية خلال 24 ساعة\n";
    $userMessage .= "💰 رسوم الدورة: 150 ألف دج شهرياً\n";
    $userMessage .= "📜 مدة الدورة: 8 أشهر (4 حلويات + 4 مرطبات)\n\n";
    $userMessage .= "🌟 نتطلع لرؤيتك قريباً في عالم الحلويات!";
    
    // إرسال رسالة التأكيد للمستخدم عبر واتساب API (محاكاة)
    $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $userPhone . "&text=" . urlencode($userMessage);
    
    // محاولة إرسال عبر Telegram إذا كان المستخدم لديه حساب
    try {
        sendTelegramMessage($botToken, $userPhone, $userMessage);
    } catch (Exception $e) {
        // إذا فشل الإرسال عبر Telegram، لا مشكلة
    }
    
    // إرسال استجابة نجاح
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'تم التسجيل بنجاح! سيتم التواصل معك قريباً.',
        'user_data' => [
            'name' => $fullName,
            'phone' => $phone,
            'city' => $city,
            'time' => $timeFormatted,
            'confirmation_message' => $userMessage
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // تسجيل الخطأ في ملف
    error_log("Registration Error: " . $e->getMessage() . " - " . date('Y-m-d H:i:s'));
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ في التسجيل: ' . $e->getMessage(),
        'error_code' => 'REGISTRATION_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}
?>