# حل مشكلة خطأ 419 (Page Expired)

## المشكلة
عند محاولة تسجيل الدخول، يظهر خطأ 419 (Page Expired)

## الحلول

### الحل 1: مسح الكاش والجلسات
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan session:clear  # إذا كان متوفراً
```

### الحل 2: التحقق من إعدادات الجلسات
تأكد من أن ملف `.env` يحتوي على:
```env
SESSION_DRIVER=file
# أو
SESSION_DRIVER=database
```

إذا كنت تستخدم `database`، تأكد من تشغيل migrations:
```bash
php artisan migrate
```

### الحل 3: التحقق من الصلاحيات
تأكد من أن مجلد `storage/framework/sessions` قابل للكتابة:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### الحل 4: إعادة بناء الأصول
```bash
npm run build
# أو
npm run dev
```

### الحل 5: التحقق من APP_KEY
تأكد من وجود `APP_KEY` في ملف `.env`:
```bash
php artisan key:generate
```

### الحل 6: استخدام متصفح مختلف أو وضع التصفح الخاص
أحياناً المشكلة تكون من المتصفح نفسه أو الإضافات.

### الحل 7: التحقق من الوقت
تأكد من أن وقت الخادم صحيح، لأن CSRF token له صلاحية زمنية.

## التحقق من الحل
بعد تطبيق الحلول:
1. امسح كاش المتصفح (Ctrl+Shift+Delete)
2. أعد تحميل الصفحة
3. جرب تسجيل الدخول مرة أخرى

## بيانات الدخول الافتراضية
- **البريد:** teacher@lms.com
- **كلمة المرور:** teacher@lms.com

