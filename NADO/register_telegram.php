<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON data');
    }
    
    if (empty($input['fullName']) || empty($input['phone']) || empty($input['city'])) {
        throw new Exception('Missing required fields');
    }
    
    $fullName = trim($input['fullName']);
    $phone = trim($input['phone']);
    $city = trim($input['city']);
    $customCity = !empty($input['customCity']) ? trim($input['customCity']) : null;
    
    if ($city === 'other' && !empty($customCity)) {
        $city = $customCity;
    }
    
    // تحديد نوع الشبكة
    $network = "";
    if (substr($phone, 0, 2) === "05") $network = "🟠 Ooredoo";
    elseif (substr($phone, 0, 2) === "06") $network = "🔵 Mobilis";
    elseif (substr($phone, 0, 2) === "07") $network = "🟡 Djezzy";
    else $network = "📱 غير محدد";
    
    // تنسيق الوقت
    $currentTime = new DateTime('now', new DateTimeZone('Africa/Algiers'));
    $timeFormatted = $currentTime->format('d/m/Y - H:i');
    
    // إنشاء الرسالة الاحترافية
    $message = "🎯 <b>تسجيل جديد في دورة الأستاذة نادية</b>\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $message .= "👤 <b>الاسم الكامل:</b>\n";
    $message .= "     ▫️ <code>" . htmlspecialchars($fullName) . "</code>\n\n";
    
    $message .= "📱 <b>رقم الهاتف:</b>\n";
    $message .= "     ▫️ <code>" . $phone . "</code>\n";
    $message .= "     ▫️ " . $network . "\n\n";
    
    $message .= "🏙️ <b>البلدية:</b>\n";
    $message .= "     ▫️ " . $city . "\n\n";
    
    $message .= "⏰ <b>وقت التسجيل:</b>\n";
    $message .= "     ▫️ " . $timeFormatted . "\n\n";
    
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "💡 <i>يمكنك الآن التواصل مع المتدربة</i>";
    
    // إنشاء الأزرار التفاعلية
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📞 اتصال مباشر', 'url' => 'tel:+213' . substr($phone, 1)],
                ['text' => '💬 رسالة واتساب', 'url' => 'https://wa.me/213' . substr($phone, 1) . '?text=مرحباً ' . urlencode($fullName) . '، أهلاً بك في دورة الحلويات مع الأستاذة نادية']
            ],
            [
                ['text' => '✅ تم التواصل', 'callback_data' => 'contacted_' . time()],
                ['text' => '📋 حفظ البيانات', 'callback_data' => 'save_' . time()]
            ],
            [
                ['text' => '📊 إحصائيات اليوم', 'callback_data' => 'stats_today'],
                ['text' => '👥 جميع المسجلين', 'callback_data' => 'all_registrations']
            ]
        ]
    ];
    
    // إرسال الرسالة مع الأزرار
    $result = sendTelegramMessage($botToken, $chatId, $message, $keyboard);
    
    if ($result === FALSE) {
        throw new Exception('Failed to send message to Telegram');
    }
    
    // إرسال رسالة إضافية بالملخص السريع
    $summaryMessage = "📈 <b>ملخص سريع:</b>\n";
    $summaryMessage .= "🆔 المسجل رقم: <code>#" . time() . "</code>\n";
    $summaryMessage .= "📍 من: <b>" . $city . "</b>\n";
    $summaryMessage .= "🕐 الساعة: <b>" . $currentTime->format('H:i') . "</b>";
    
    sendTelegramMessage($botToken, $chatId, $summaryMessage);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>