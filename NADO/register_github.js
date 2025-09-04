// نظام التسجيل للمواقع المستضافة على GitHub
class RegistrationSystem {
    constructor() {
        this.webhookUrl = 'https://discord.com/api/webhooks/YOUR_WEBHOOK_ID/YOUR_WEBHOOK_TOKEN';
        this.telegramBot = 'https://api.telegram.org/bot8168229607:AAG1vhgduwBlIRAghxAkDu8LOVp8uLsQvdo/sendMessage';
        this.chatId = '7220908910';
    }

    async sendToTelegram(data) {
        const message = `🎯 تسجيل جديد في دورة الأستاذة نادية

👤 الاسم: ${data.fullName}
📱 الهاتف: ${data.phone}
🏙️ البلدية: ${data.city}
⏰ التوقيت: ${new Date().toLocaleString('ar-DZ')}

💬 رابط واتساب: https://wa.me/213${data.phone.substring(1)}`;

        try {
            const response = await fetch(this.telegramBot, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: this.chatId,
                    text: message,
                    parse_mode: 'HTML'
                })
            });
            return response.ok;
        } catch (error) {
            console.error('Telegram error:', error);
            return false;
        }
    }

    async sendToDiscord(data) {
        const embed = {
            title: "🎯 تسجيل جديد في دورة الحلويات",
            color: 0x00ff00,
            fields: [
                { name: "👤 الاسم", value: data.fullName, inline: true },
                { name: "📱 الهاتف", value: data.phone, inline: true },
                { name: "🏙️ البلدية", value: data.city, inline: true },
                { name: "⏰ الوقت", value: new Date().toLocaleString('ar-DZ'), inline: false },
                { name: "💬 واتساب", value: `[اتصال مباشر](https://wa.me/213${data.phone.substring(1)})`, inline: false }
            ],
            timestamp: new Date().toISOString()
        };

        try {
            const response = await fetch(this.webhookUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ embeds: [embed] })
            });
            return response.ok;
        } catch (error) {
            console.error('Discord error:', error);
            return false;
        }
    }

    async sendToEmail(data) {
        // استخدام Formspree للإرسال المجاني للجيميل
        try {
            const response = await fetch('https://formspree.io/f/xdkogkpv', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: 'nourtb665@gmail.com',
                    subject: '🎯 تسجيل جديد في دورة الحلويات',
                    message: `تسجيل جديد في دورة الأستاذة نادية:

👤 الاسم: ${data.fullName}
📱 الهاتف: ${data.phone}
🏙️ البلدية: ${data.city}
⏰ الوقت: ${new Date().toLocaleString('ar-DZ')}

💬 رابط واتساب: https://wa.me/213${data.phone.substring(1)}

---
تم الإرسال تلقائياً من موقع دورة الحلويات`
                })
            });
            return response.ok;
        } catch (error) {
            console.error('Email error:', error);
            return false;
        }
    }

    async register(formData) {
        const results = {
            telegram: false,
            discord: false,
            email: false,
            localStorage: false
        };

        // محاولة الإرسال لجميع الخدمات
        try {
            results.telegram = await this.sendToTelegram(formData);
        } catch (e) {}

        try {
            results.discord = await this.sendToDiscord(formData);
        } catch (e) {}

        try {
            results.email = await this.sendToEmail(formData);
        } catch (e) {}

        // حفظ محلي كنسخة احتياطية
        try {
            const registrations = JSON.parse(localStorage.getItem('registrations') || '[]');
            registrations.push({
                ...formData,
                timestamp: new Date().toISOString(),
                id: Date.now()
            });
            localStorage.setItem('registrations', JSON.stringify(registrations));
            results.localStorage = true;
        } catch (e) {}

        return results;
    }
}

// تهيئة النظام
const registrationSystem = new RegistrationSystem();

// نظام مزامنة البيانات السحابي
class CloudSync {
    static async saveToCloud(data) {
        // إرسال لـ JSONBin (مجاني)
        const binId = '673a1b2ce41b4d34e4494567';
        const apiKey = '$2a$10$YOUR_API_KEY';
        
        try {
            const response = await fetch(`https://api.jsonbin.io/v3/b/${binId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Master-Key': apiKey
                },
                body: JSON.stringify({
                    registrations: [...await this.getFromCloud(), {
                        ...data,
                        timestamp: new Date().toISOString(),
                        id: Date.now()
                    }]
                })
            });
            return response.ok;
        } catch (error) {
            return false;
        }
    }
    
    static async getFromCloud() {
        const binId = '673a1b2ce41b4d34e4494567';
        const apiKey = '$2a$10$YOUR_API_KEY';
        
        try {
            const response = await fetch(`https://api.jsonbin.io/v3/b/${binId}/latest`, {
                headers: { 'X-Master-Key': apiKey }
            });
            const data = await response.json();
            return data.record?.registrations || [];
        } catch (error) {
            return [];
        }
    }
}