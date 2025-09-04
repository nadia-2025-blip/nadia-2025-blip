# موقع دورة الحلويات - الأستاذة نادية

## رفع الموقع على GitHub Pages

### الخطوات:

1. **إنشاء Repository جديد:**
   - اذهب إلى GitHub.com
   - اضغط "New Repository"
   - اسم المشروع: `nado-sweets-course`
   - اجعله Public

2. **رفع الملفات:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/USERNAME/nado-sweets-course.git
   git push -u origin main
   ```

3. **تفعيل GitHub Pages:**
   - اذهب إلى Settings > Pages
   - اختر Source: GitHub Actions
   - سيتم النشر تلقائياً

### إعداد البوت:

1. **تحديث معرف البوت:**
   - افتح `register_github.js`
   - غيّر `YOUR_WEBHOOK_ID` و `YOUR_WEBHOOK_TOKEN`

2. **إنشاء Discord Webhook (اختياري):**
   - اذهب إلى Discord Server Settings
   - Integrations > Webhooks > New Webhook
   - انسخ الرابط واستبدله في الكود

### الرابط النهائي:
`https://USERNAME.github.io/nado-sweets-course/`

### الميزات:
- ✅ يعمل بدون خادم PHP
- ✅ إرسال للتيليجرام مباشرة
- ✅ حفظ محلي كنسخة احتياطية
- ✅ مجاني 100%