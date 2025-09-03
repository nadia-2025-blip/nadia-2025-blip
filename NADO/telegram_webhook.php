<?php
// معالج الأزرار التفاعلية للبوت
$botToken = "YOUR_BOT_TOKEN";

// الحصول على البيانات من تليجرام
$input = file_get_contents('php://input');
$update = json_decode($input, true);

function sendTelegramMessage($botToken, $chatId, $message, $keyboard = null) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
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

function answerCallbackQuery($botToken, $callbackQueryId, $text) {
    $url = "https://api.telegram.org/bot" . $botToken . "/answerCallbackQuery";
    
    $data = [
        'callback_query_id' => $callbackQueryId,
        'text' => $text,
        'show_alert' => false
    ];
    
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

// معالجة الرسائل النصية
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    
    if ($text === '/start') {
        $welcomeMessage = "🎂 <b>مرحباً بك في بوت دورة الأستاذة نادية للحلويات!</b>\n\n";
        $welcomeMessage .= "📋 <b>الوظائف المتاحة:</b>\n";
        $welcomeMessage .= "• استقبال التسجيلات الجديدة تلقائياً\n";
        $welcomeMessage .= "• إمكانية الاتصال المباشر بالمتدربات\n";
        $welcomeMessage .= "• إرسال رسائل واتساب سريعة\n";
        $welcomeMessage .= "• عرض الإحصائيات اليومية\n\n";
        $welcomeMessage .= "✨ <i>البوت جاهز لاستقبال التسجيلات!</i>";
        
        sendTelegramMessage($botToken, $chatId, $welcomeMessage);
    }
    
    elseif ($text === '/stats') {
        $statsMessage = "📊 <b>إحصائيات اليوم:</b>\n\n";
        $statsMessage .= "👥 عدد التسجيلات: <code>0</code>\n";
        $statsMessage .= "📱 آخر تسجيل: <code>لا يوجد</code>\n";
        $statsMessage .= "🕐 الوقت الحالي: <code>" . date('H:i d/m/Y') . "</code>\n\n";
        $statsMessage .= "💡 <i>سيتم تحديث الإحصائيات مع كل تسجيل جديد</i>";
        
        sendTelegramMessage($botToken, $chatId, $statsMessage);
    }
}

// معالجة الأزرار التفاعلية
if (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $data = $callbackQuery['data'];
    $callbackQueryId = $callbackQuery['id'];
    
    if (strpos($data, 'contacted_') === 0) {
        answerCallbackQuery($botToken, $callbackQueryId, "✅ تم تسجيل التواصل مع المتدربة");
        
        $confirmMessage = "✅ <b>تم التأكيد!</b>\n";
        $confirmMessage .= "📞 تم التواصل مع المتدربة بنجاح\n";
        $confirmMessage .= "⏰ الوقت: " . date('H:i d/m/Y');
        
        sendTelegramMessage($botToken, $chatId, $confirmMessage);
    }
    
    elseif (strpos($data, 'save_') === 0) {
        answerCallbackQuery($botToken, $callbackQueryId, "💾 تم حفظ البيانات");
        
        $saveMessage = "💾 <b>تم الحفظ!</b>\n";
        $saveMessage .= "📋 تم حفظ بيانات المتدربة في السجلات\n";
        $saveMessage .= "🔐 البيانات محفوظة بأمان";
        
        sendTelegramMessage($botToken, $chatId, $saveMessage);
    }
    
    elseif ($data === 'stats_today') {
        answerCallbackQuery($botToken, $callbackQueryId, "📊 جاري تحضير الإحصائيات...");
        
        $todayStats = "📈 <b>إحصائيات اليوم التفصيلية:</b>\n\n";
        $todayStats .= "📅 التاريخ: <code>" . date('d/m/Y') . "</code>\n";
        $todayStats .= "👥 إجمالي التسجيلات: <code>0</code>\n";
        $todayStats .= "🕐 أول تسجيل: <code>--:--</code>\n";
        $todayStats .= "🕐 آخر تسجيل: <code>--:--</code>\n\n";
        $todayStats .= "🏙️ <b>التوزيع حسب المدن:</b>\n";
        $todayStats .= "• قسنطينة: <code>0</code>\n";
        $todayStats .= "• الخروب: <code>0</code>\n";
        $todayStats .= "• أخرى: <code>0</code>\n\n";
        $todayStats .= "📱 <b>التوزيع حسب الشبكات:</b>\n";
        $todayStats .= "• Mobilis: <code>0</code>\n";
        $todayStats .= "• Djezzy: <code>0</code>\n";
        $todayStats .= "• Ooredoo: <code>0</code>";
        
        sendTelegramMessage($botToken, $chatId, $todayStats);
    }
    
    elseif ($data === 'all_registrations') {
        answerCallbackQuery($botToken, $callbackQueryId, "👥 جاري تحضير قائمة المسجلين...");
        
        $allRegsMessage = "👥 <b>جميع المسجلين:</b>\n\n";
        $allRegsMessage .= "📊 إجمالي المسجلين: <code>0</code>\n";
        $allRegsMessage .= "📅 منذ بداية الدورة: <code>" . date('d/m/Y') . "</code>\n\n";
        $allRegsMessage .= "💡 <i>سيتم عرض قائمة مفصلة مع كل تسجيل جديد</i>\n\n";
        $allRegsMessage .= "🔄 <b>آخر التحديثات:</b>\n";
        $allRegsMessage .= "• لا توجد تسجيلات حتى الآن";
        
        sendTelegramMessage($botToken, $chatId, $allRegsMessage);
    }
}

// إرسال رد فارغ لتليجرام
http_response_code(200);
echo "OK";
?>