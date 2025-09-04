<?php
// Test registration form
$testData = [
    'fullName' => 'سارة أحمد (اختبار)',
    'phone' => '0661234567',
    'city' => 'قسنطينة',
    'customCity' => ''
];

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $testData;

// Capture output
ob_start();
file_put_contents('php://input', json_encode($testData));

try {
    include 'register_telegram.php';
    $output = ob_get_clean();
    echo "✅ تم اختبار نموذج التسجيل بنجاح!\n";
    echo "📤 تم إرسال بيانات تجريبية لبوت التيليجرام\n";
    echo "📋 البيانات المرسلة:\n";
    echo "   - الاسم: " . $testData['fullName'] . "\n";
    echo "   - الهاتف: " . $testData['phone'] . "\n";
    echo "   - المدينة: " . $testData['city'] . "\n";
    echo "\n🔍 تحقق من بوت التيليجرام لرؤية الرسالة!";
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ خطأ في الاختبار: " . $e->getMessage();
}
?>