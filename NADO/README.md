# موقع دورة الحلويات التقليدية والعصرية مع الأستاذة نادية

## وصف المشروع
موقع إلكتروني عصري وأنيق لتسجيل المتدربين في دورة الحلويات، مع لوحة تحكم لإدارة التسجيلات.

## الملفات المطلوبة

### الملفات الأساسية:
- `index.html` - الصفحة الرئيسية
- `style.css` - ملف التصميم
- `script.js` - ملف JavaScript
- `register.php` - معالج التسجيل
- `database.sql` - إعداد قاعدة البيانات

### لوحة التحكم:
- `admin/index.php` - لوحة التحكم الرئيسية
- `admin/login.php` - صفحة تسجيل الدخول
- `admin/logout.php` - تسجيل الخروج

### مجلد الصور:
- `images/` - ضع صور الحلويات والمدربة هنا

## خطوات التثبيت

### 1. رفع الملفات
- ارفع جميع الملفات إلى مجلد الموقع على الاستضافة
- تأكد من رفع مجلد `admin` و `images`

### 2. إعداد قاعدة البيانات
```sql
-- قم بتشغيل الأوامر التالية في phpMyAdmin أو MySQL
CREATE DATABASE sweets_course CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sweets_course;

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) NULL,
    city VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 3. إعداد ملف التكوين
```bash
cp config.example.php config.php
```
ثم قم بتحديث `config.php` بالبيانات الصحيحة:
```php
$servername = "localhost";
$username = "اسم_المستخدم_لقاعدة_البيانات";
$password = "كلمة_مرور_قاعدة_البيانات";
$dbname = "sweets_course";
$botToken = "توكن_البوت";
$chatId = "معرف_المحادثة";
```

### 4. تحديث بيانات تسجيل الدخول للوحة التحكم
في ملف `admin/login.php`، قم بتغيير:
```php
$admin_username = 'admin';
$admin_password = 'nadia2024';  // غيّر كلمة المرور
```

### 5. إضافة الصور
ضع الصور التالية في مجلد `images/`:
- `instructor.jpg` - صورة الأستاذة نادية
- `traditional1.jpg` - صورة الحلويات التقليدية
- `modern1.jpg` - صورة الحلويات العصرية
- `cakes.jpg` - صورة الكيك والتورتات
- `cookies.jpg` - صورة البسكويت والكوكيز

## الميزات

### الصفحة الرئيسية:
- ✅ تصميم عصري ومتجاوب
- ✅ قسم تعريفي بالمدربة
- ✅ معرض صور الحلويات
- ✅ نموذج تسجيل تفاعلي
- ✅ ألوان دافئة وحيوية
- ✅ رابط قصير لتيك توك

### لوحة التحكم:
- ✅ تسجيل دخول محمي
- ✅ عرض إحصائيات التسجيلات
- ✅ جدول المسجلين مع ترقيم الصفحات
- ✅ تصدير البيانات كملف CSV
- ✅ تصميم متجاوب

### الأمان:
- ✅ حماية من SQL Injection
- ✅ تشفير كلمات المرور
- ✅ التحقق من صحة البيانات
- ✅ منع التسجيل المكرر

## الوصول للوحة التحكم
- الرابط: `yourwebsite.com/admin/`
- اسم المستخدم: `admin`
- كلمة المرور: `nadia2024` (قم بتغييرها)

## الرابط القصير لتيك توك
استخدم الرابط: `bit.ly/nadia-sweets` في إعلانات تيك توك

## الدعم الفني
- تأكد من تفعيل PHP و MySQL على الاستضافة
- تأكد من صلاحيات الكتابة على المجلدات
- في حالة وجود مشاكل، تحقق من error logs

## تحسينات مستقبلية
- إضافة نظام إشعارات
- ربط مع واتساب API
- إضافة نظام دفع
- تحسين SEO