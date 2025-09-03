<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Load configuration
require_once 'config.php';

function sendTelegramMessage($botToken, $chatId, $message) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
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

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    if (empty($input['fullName']) || empty($input['phone']) || empty($input['city'])) {
        throw new Exception('Missing required fields');
    }
    
    // Sanitize input
    $fullName = trim($input['fullName']);
    $phone = trim($input['phone']);
    $email = !empty($input['email']) ? trim($input['email']) : null;
    $city = trim($input['city']);
    $customCity = !empty($input['customCity']) ? trim($input['customCity']) : null;
    
    // Use custom city if "other" was selected
    if ($city === 'other' && !empty($customCity)) {
        $city = $customCity;
    }
    
    // Validate phone number
    if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
        throw new Exception('Invalid phone number format');
    }
    
    // Check if phone number already exists
    $checkStmt = $conn->prepare("SELECT id FROM registrations WHERE phone = ?");
    $checkStmt->execute([$phone]);
    
    if ($checkStmt->rowCount() > 0) {
        throw new Exception('Phone number already registered');
    }
    
    // Insert new registration
    $stmt = $conn->prepare("INSERT INTO registrations (full_name, phone, email, city, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$fullName, $phone, $email, $city]);
    
    $registrationId = $conn->lastInsertId();
    
    // Send to Telegram
    $currentTime = new DateTime('now', new DateTimeZone('Africa/Algiers'));
    $timeFormatted = $currentTime->format('d/m/Y - H:i');
    
    $telegramMessage = "🎯 <b>تسجيل جديد في دورة الأستاذة نادية</b>\n";
    $telegramMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $telegramMessage .= "👤 <b>الاسم:</b> " . htmlspecialchars($fullName) . "\n";
    $telegramMessage .= "📱 <b>الهاتف:</b> " . $phone . "\n";
    if ($email) $telegramMessage .= "📧 <b>الإيميل:</b> " . $email . "\n";
    $telegramMessage .= "🏙️ <b>البلدية:</b> " . $city . "\n";
    $telegramMessage .= "⏰ <b>التوقيت:</b> " . $timeFormatted . "\n";
    $telegramMessage .= "🆔 <b>رقم التسجيل:</b> #" . $registrationId;
    
    sendTelegramMessage($botToken, $chatId, $telegramMessage);
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'id' => $registrationId
    ]);
    
} catch (Exception $e) {
    // Error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>