<?php
// اختبار بسيط للتسجيل
header('Content-Type: application/json; charset=utf-8');

// بيانات تجريبية
$testData = [
    'fullName' => 'اختبار التسجيل',
    'phone' => '0780707946',
    'city' => 'قسنطينة'
];

// محاكاة الطلب
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $testData;

// تشغيل ملف التسجيل
ob_start();
include 'register_telegram.php';
$output = ob_get_clean();

echo $output;
?>