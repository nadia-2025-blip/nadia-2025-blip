-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS sweets_course CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sweets_course;

-- إنشاء جدول التسجيلات
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL COMMENT 'الاسم الكامل',
    phone VARCHAR(20) NOT NULL UNIQUE COMMENT 'رقم الهاتف',
    email VARCHAR(255) NULL COMMENT 'البريد الإلكتروني',
    city VARCHAR(100) NOT NULL COMMENT 'البلدية',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ التسجيل',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاريخ التحديث',
    
    INDEX idx_phone (phone),
    INDEX idx_city (city),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول تسجيلات الطلاب';

-- إدراج بيانات تجريبية (اختيارية)
INSERT INTO registrations (full_name, phone, email, city) VALUES
('سارة أحمد محمد', '0661234567', 'sara@example.com', 'قسنطينة'),
('فاطمة علي حسن', '0771234568', 'fatima@example.com', 'الخروب'),
('أمينة محمود عبدالله', '0551234569', 'amina@example.com', 'عين عبيد'),
('خديجة يوسف إبراهيم', '0661234570', NULL, 'ابن زياد'),
('زهرة عبدالرحمن محمد', '0771234571', 'zahra@example.com', 'الديدوش مراد');

-- عرض البيانات للتأكد
SELECT * FROM registrations ORDER BY created_at DESC;