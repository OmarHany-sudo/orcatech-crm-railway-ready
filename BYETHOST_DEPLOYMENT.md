# نشر OrcaTech CRM على ByetHost

هذه الوثيقة مخصّصة لنسخة **عرض عامة** من OrcaTech CRM على استضافة Apache/LiteSpeed مشتركة. لا تعيد بناء التطبيق ولا تعتمد على Node.js أو Redis أو Supervisor أو Horizon أو WebSockets في الخادم.

> **ملاحظة توافق إلزامية:** المشروع الحالي يطلب PHP `^8.5` في `composer.json`، كما أن وحدات Liberu المحلية المضمّنة في `composer.lock` تطلب PHP `^8.5`. صفحة ByetHost الرسمية تعلن PHP 8.3 وMySQL 8/MariaDB 11.4 [1] [2]. لذلك لا يجوز إعلان المشروع متوافقًا مع حساب ByetHost ذي PHP 8.3 قبل ترقية PHP إلى 8.5 أو إجراء ترقية واعية ومختبرة للاعتماديات. لا تغيّر قيد PHP أو تخفّض Laravel عشوائيًا.

## 1. ملخص التدقيق الحالي

| البند | الحالة الفعلية في المشروع | أثره على ByetHost |
|---|---|---|
| الإطار | Laravel 13 مع Filament 5 وLivewire 4 | يتطلب PHP 8.5 وفق قيد المشروع الحالي |
| PHP | الحد الأدنى الفعلي `^8.5` | **مانع توافق** إذا كان الحساب يوفّر PHP 8.3 فقط |
| قاعدة البيانات | MySQL/MariaDB عبر `DB_CONNECTION=mysql` | مناسبة بعد إنشاء قاعدة MySQL وملء متغيراتها |
| Node/npm | مطلوبان للبناء فقط | لا يحتاجهما الخادم بعد رفع `public/build` |
| Redis | موجود كخيار في الإعدادات، لكنه ليس مطلوبًا للنسخة المشتركة | استخدم file cache وfile sessions وsync queue |
| Queue worker | توجد Jobs وتكاملات اختيارية | الوضع الافتراضي `sync`، فلا توجد عملية دائمة مطلوبة |
| Scheduler | نشر منشورات اجتماعية وتحديث تحليلات كل ساعة | اختياري للعرض؛ يحتاج Cron فقط عند تفعيل هذه الميزات |
| Horizon | مثبت ضمن Composer | غير مطلوب ولا ينبغي تشغيله على الاستضافة المشتركة |
| Reverb/WebSockets | مثبتان اختياريًا | غير مطلوبين؛ استخدم `BROADCAST_DRIVER=null` |
| Octane | مثبت كاعتماد اختياري | غير مطلوب؛ استخدم Apache/LiteSpeed/PHP التقليدي |
| Storage | local/public مع symlink تقليدي | توجد آلية fallback لمسار `/storage/{path}` عند تعذر symlink |
| العرض التجريبي | Nile Properties ببيانات خيالية حتمية | يزرع عبر `NilePropertiesDemoSeeder` |
| حساب العرض | حساب منفصل بدور `manager` | لا يستخدم `super_admin` ولا `admin/admin` |
| البريد | يضبط على `log` افتراضيًا | يمنع إرسال البريد الحقيقي بالخطأ |
| التكاملات الخارجية | OAuth وTwilio وMailchimp وWhatsApp وغيرها اختيارية | اترك بيانات الاعتماد فارغة في العرض العام |

## 2. المتطلبات

يحتاج الخادم إلى PHP 8.5 أو أحدث ضمن نطاق الاعتماديات الحالية، مع الامتدادات التي يستلزمها Laravel والحزم المفعّلة، وبالأخص `BCMath` و`Ctype` و`cURL` و`DOM/XML` و`Fileinfo` و`Filter` و`GD` و`Intl` و`JSON` و`Mbstring` و`OpenSSL` و`PDO` و`PDO_MySQL` و`Tokenizer` و`XMLReader` و`Zip` و`Zlib`. قد تحتاج الميزات الاختيارية مثل SAML إلى `DOM`, `XML`, `OpenSSL`, `cURL`, و`Zip`.

تحتاج النسخة إلى MySQL 8 أو MariaDB متوافق مع مخطط Laravel. صفحة ByetHost الرسمية تذكر MySQL 8 وMariaDB 11.4 وphpMyAdmin، كما تذكر مدير ملفات وFTP وCron [2]. تحقق من الامتدادات والإصدار من لوحة الحساب قبل الرفع؛ لا تعتمد على وجودها بالاسم فقط.

## 3. البناء المحلي

نفّذ الأوامر التالية على جهاز تطوير يملك PHP 8.5 وComposer وNode/npm:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

ينتج `npm run build` الأصول الثابتة في `public/build`. لا تشغّل Vite development server على ByetHost ولا ترفع `node_modules`.

لبناء حزمة الرفع المنظمة، استخدم:

```bash
bash scripts/package-byethost.sh /path/to/output/byethost
```

ينتج السكربت:

```text
byethost/
├── app/          # التطبيق الكامل خارج document root
├── public_html/  # محتويات document root
└── README.txt
```

يجب تنفيذ السكربت على بيئة PHP 8.5 متوافقة؛ لا يمكن اعتبار فشل Composer على PHP 8.3 مشكلة قابلة للتجاوز بأمان.

## 4. بنية الملفات وDocument Root

إذا كان ByetHost يسمح بتعيين document root للنطاق أو النطاق الفرعي، اجعله يشير إلى:

```text
.../public
```

أما إذا كان الحساب يفرض `public_html`، ارفع مجلد `app/` الناتج من السكربت خارج `public_html` قدر الإمكان، ثم ارفع **محتويات** `public_html/` إلى مجلد `public_html` الفعلي. لا ترفع مجلد المشروع الكامل إلى document root.

يستخدم `public_html/index.php` مسارًا آمنًا إلى `app/bootstrap/app.php` و`app/vendor/autoload.php`، ويضبط public path على `public_html`. لا تغيّر المسارات يدويًا داخل `app/bootstrap/app.php`.

لا يجب أن يكون أي من العناصر التالية قابلًا للوصول عبر الويب:

```text
app/.env
app/storage/
app/vendor/        # خارج public_html، حتى لو كان Composer autoload موجودًا
app/composer.json
app/composer.lock
app/tests/
app/.git/
public/installer.php
```

تم استبعاد `public/installer.php` من حزمة ByetHost لأنه سطح تشغيل إداري عبر المتصفح، وقد يشغّل Composer وArtisan ويعدّل `.env`. لا ترفعه إلى العرض العام.

## 5. إعداد `.env`

انسخ `app/.env.example` إلى `app/.env` خارج web root، ثم عدّل القيم التالية:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://demo.example.com
APP_KEY=base64:GENERATE_A_REAL_32_BYTE_KEY

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

CACHE_STORE=file
SESSION_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=null
MAIL_MAILER=log
```

وللعرض العام:

```dotenv
DEMO_MODE=true
DEMO_USER_NAME="OrcaTech Demo User"
DEMO_USER_EMAIL=demo@orcatech.test
DEMO_USER_PASSWORD="OrcaTech-Demo-2026!"
STRIPE_SUBSCRIPTIONS_ENABLED=false
TELESCOPE_ENABLED=false
SANCTUM_STATEFUL_DOMAINS=demo.example.com
```

غيّر `APP_URL` و`SANCTUM_STATEFUL_DOMAINS` إلى النطاق الحقيقي. لا تضع `localhost` في إعداد الإنتاج. لا تستخدم قيمة `APP_KEY` الموجودة في أي بيئة تطوير؛ أنشئ مفتاحًا جديدًا على جهاز موثوق أو عبر `php artisan key:generate` قبل رفع `.env`.

إذا لم يوجد SSH، أنشئ `.env` محليًا من المثال، ولّد المفتاح محليًا ببيئة PHP 8.5، ثم ارفعه عبر مدير الملفات مع التأكد من أن مكانه خارج `public_html`.

## 6. قاعدة البيانات وphpMyAdmin

أنشئ قاعدة MySQL من لوحة ByetHost، وسجّل اسم المضيف واسم القاعدة واسم المستخدم وكلمة المرور كما تعرضها اللوحة. لا تفترض أن `DB_HOST=127.0.0.1` أو أن اسم القاعدة هو اسم المشروع.

إذا توفر SSH/PHP، نفّذ:

```bash
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\NilePropertiesDemoSeeder --force
```

لنسخة عرض جديدة، يمكن استخدام:

```bash
php artisan migrate:fresh --seed --force
```

لكن لا تستخدم `migrate:fresh` على قاعدة فيها بيانات حقيقية.

إذا لم يتوفر CLI، نفّذ المهاجرات محليًا على MySQL متوافق ثم صدّر قاعدة البيانات الخالية من الأسرار:

```bash
mysqldump --single-transaction --no-tablespaces --skip-comments \\
  -u DB_USERNAME -p -h DB_HOST DB_DATABASE > database/demo.sql
```

استبدل أسماء الاتصال بقيم محلية مؤقتة، ثم استورد `database/demo.sql` من phpMyAdmin بعد إنشاء قاعدة ByetHost. لا تضع كلمات المرور أو مفاتيح API داخل SQL. بعد الاستيراد تحقق من وجود الجداول الأساسية والحساب `demo@orcatech.test`. لا تستخدم ملف SQLite الموجود للتشغيل الإنتاجي؛ هو أصل تطوير/اختبار وليس بديلًا عن MySQL.

## 7. بيانات العرض وتسجيل الدخول

يُنشئ `NilePropertiesDemoSeeder` بيانات خيالية حتمية لشركة Nile Properties، بما في ذلك الشركات والجهات والـ leads والصفقات ومراحل pipeline والمهام والأنشطة والملاحظات والمستخدمين. لا تستخدم هذه البيانات على أنها بيانات عملاء حقيقيين.

بيانات الدخول الافتراضية في هذه الحزمة هي:

| الحقل | القيمة |
|---|---|
| البريد | `demo@orcatech.test` |
| كلمة المرور | `OrcaTech-Demo-2026!` |
| الدور | `manager` ضمن فريق Nile Properties |
| لوحة الدخول | `/app/login` أو رابط Login الذي يولده التطبيق |

هذا حساب **عرض عام منفصل** وليس حساب إدارة. يمنع التطبيق في `DEMO_MODE` صلاحيات الحذف، والحذف الجماعي، والاستعادة، وإدارة المستخدمين والأدوار والصلاحيات، وتغيير بيانات الاعتماد. لا تمنح الحساب دور `super_admin`.

## 8. الباقات والميزات

يبدأ العرض على باقة `Starter`، ويمكن تبديل الحالة إلى `Business` من واجهة العرض لاختبار الميزات المقفلة والمفتوحة. منطق الباقات مركزي في `App\\Support\\OrcaTech\\Feature` ولا ينبغي استبداله أو تكراره في الواجهات.

اختبر أن الميزات المقفلة تعيد المستخدم إلى صفحة الترقية بدلًا من إرجاع خطأ أو كشف مسار داخلي. تظل التكاملات الخارجية في حالة add-on/demo ما لم تُملأ بيانات اعتماد حقيقية بقصد واضح.

## 9. التخزين والملفات المرفوعة

التخزين الخاص في `storage/app` لا يجب أن يكون داخل public root. التخزين العام في `storage/app/public` مخصص للصور والملفات التي يسمح التطبيق بعرضها.

إذا كان إنشاء symlink مسموحًا:

```bash
php artisan storage:link
```

إذا لم يكن مسموحًا، اترك `public_html/storage` غير موجود؛ سيستخدم التطبيق route fallback الآمن `/storage/{path}` الذي يقرأ من disk `public` فقط ويرفض المسارات التي تحتوي `..`. لا تجعل هذا المسار يقرأ من disk `local`.

اختبر رفع صورة avatar، وشعار، ومرفق مستند بالحجم والامتداد المسموحين. تذكر أن الصفحة الرسمية تذكر حد رفع 10 MB لحسابات الاستضافة المجانية [2]، بينما يفرض التطبيق حدودًا أقل لبعض الحقول؛ يجب أن تظل القيمة الأصغر هي الحاكمة.

## 10. الصلاحيات

يجب أن تكون المجلدات التالية قابلة للكتابة من مستخدم PHP فقط:

```text
app/storage/
app/bootstrap/cache/
```

ابدأ بأقل صلاحية يسمح بها الحساب، مثل `755` للمجلدات و`644` للملفات، أو الإعداد الذي تفرضه لوحة ByetHost. لا تستخدم `777` كحل افتراضي. إذا احتاج الخادم صلاحية group write، استخدم `775` للمجلدات المطلوبة فقط ثم اختبرها.

## 11. Cache وSession وQueue

إعدادات العرض المشتركة مقصودة لتجنب الخدمات الدائمة:

```dotenv
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=null
```

عدّل `config/cache.php` ليقبل `CACHE_STORE` مع توافق خلفي مع `CACHE_DRIVER`. لا تستخدم Redis إلا إذا كان مزود الاستضافة يقدمه فعليًا وتحققت من الاتصال.

مع وجود CLI، وبعد التأكد من اكتمال `.env`:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

إذا غيّرت أي قيمة في `.env` بعد ذلك، أعد `php artisan config:cache`. لا تستخدم cache قديمًا يحتوي عناوين أو مفاتيح بيئة أخرى.

## 12. Scheduler وCron

وظائف الجدولة الحالية هي نشر المنشورات المجدولة كل دقيقة وتحديث تحليلاتها كل ساعة. لا يحتاج CRM الأساسي إلى Scheduler كي يعمل، ولا ينبغي تشغيل هذه الوظائف في العرض العام دون تكاملات موثقة.

عند تفعيل الجدولة وتوفر Cron في اللوحة، أنشئ مهمة كل دقيقة بصيغة مشابهة، مع استبدال المسار الفعلي:

```cron
* * * * * /usr/local/bin/php /ABSOLUTE/PATH/TO/app/artisan schedule:run >> /ABSOLUTE/PATH/TO/app/storage/logs/scheduler.log 2>&1
```

قد يختلف مسار PHP في ByetHost. استخدم المسار الذي تعرضه اللوحة أو مدير الاستضافة. لا تضع المسار أعلاه حرفيًا قبل استبداله.

إذا لم يتوفر Cron، اترك `QUEUE_CONNECTION=sync` ولا تعتمد على النشر المؤجل أو التحليلات الدورية لسلامة CRM الأساسية.

## 13. Horizon وReverb وOctane وDocker

هذه الاعتماديات موجودة لتشغيل بيئات أخرى أو ميزات اختيارية، لكنها ليست متطلبات لحزمة ByetHost. لا تشغّل `horizon`, `reverb`, `octane`, Docker أو Kubernetes على الاستضافة المشتركة. استخدم Apache/LiteSpeed وPHP التقليديين.

## 14. التكاملات والبريد والمدفوعات

اترك بيانات Twilio وWhatsApp وGmail وOutlook وMailchimp وOAuth وStripe وZernio فارغة في العرض العام. لا تعتمد على أن قيمًا مثل `XXX` أو `x` ستفشل بطريقة آمنة دائمًا؛ الفراغ هو الحالة المقصودة، مع تعطيل المدفوعات والبريد الحقيقي.

اضبط:

```dotenv
MAIL_MAILER=log
STRIPE_SUBSCRIPTIONS_ENABLED=false
```

إذا أردت عرض تكامل حقيقي، استخدم حساب sandbox غير مرتبط بعملاء حقيقيين، وقيّد صلاحياته، واختبر callback URLs على النطاق النهائي، ثم راجع سجلاته بعد الاختبار. حالة التكامل غير المتاح يجب أن تبقى add-on/demo ولا تعرض نجاحًا زائفًا.

## 15. Apache و`.htaccess`

تحت `public` توجد قواعد لإعادة كتابة المسارات إلى `index.php`، وتعطيل directory listing، وحظر الملفات المخفية وامتدادات البيئة والسجلات والنسخ الاحتياطية وSQL. راجع أن LiteSpeed في الحساب يحترم `.htaccess`، وأن اختيار PHP 8.3 أو 8.5 في اللوحة لا يضيف handler متعارضًا.

بعد الرفع اختبر:

```text
https://demo.example.com/health/live
https://demo.example.com/health/ready
https://demo.example.com/app/login
https://demo.example.com/build/manifest.json
```

لا تنشئ endpoint ينفّذ Artisan أو أوامر نظام من المتصفح. استخدم SSH أو مدير الملفات أو phpMyAdmin فقط.

## 16. التحقق بعد النشر

نفّذ smoke test واقعيًا من المتصفح، لا تكتفِ بـ `php artisan serve`:

| الاختبار | النتيجة المتوقعة |
|---|---|
| `/health/live` | JSON بسيط بحالة `live` بلا أسرار |
| `/health/ready` | `ready` عند نجاح PDO، و503 آمن عند فشل القاعدة |
| تسجيل الدخول والخروج | يعملان مع session file وHTTPS |
| Dashboard | تظهر بيانات Nile Properties دون stack trace |
| Leads وContacts وCompanies | القائمة والنماذج تعمل |
| Deals وPipeline | تظهر المراحل والقيم الواقعية |
| Tasks وActivities وNotes | القراءة والإنشاء وفق صلاحيات الحساب |
| Starter/Business | التبديل لا يغير قاعدة البيانات ولا ينشئ tenant جديدًا |
| Locked features | تظهر حالة upgrade ولا تكشف معلومات داخلية |
| Arabic/English | يعمل التحويل مع RTL/LTR |
| CSS/JS/images/fonts | لا توجد روابط localhost ولا 404 للأصول |
| Uploads | public disk يعمل، والملفات الخاصة غير قابلة للتنزيل العام |
| الحذف | حساب العرض يحصل على منع صلاحية آمن |
| التكاملات | لا توجد اتصالات حقيقية عند خلو بيانات الاعتماد |

## 17. استكشاف الأخطاء

إذا ظهر `500`، افحص `storage/logs/laravel.log` محليًا أو من مدير الملفات دون عرضه للعامة، وتأكد أولًا من `APP_DEBUG=false` وPHP الصحيح وامتدادات PDO و`storage` و`bootstrap/cache`.

إذا ظهرت `Class not found`، فالحزمة لم تُبنَ على PHP/Composer الصحيح أو لم يُرفع `vendor/`. أعد `composer install --no-dev --optimize-autoloader` محليًا على PHP 8.5 وأعد رفع `app/vendor`.

إذا ظهرت صفحة بيضاء أو 404 للمسارات، تحقق من document root ومن وجود `public_html/index.php` و`.htaccess`. إذا ظهرت 404 للأصول، تحقق من `public/build/manifest.json` ومن `APP_URL` و`ASSET_URL` وعدم تشغيل Vite dev server.

إذا فشل login أو اختفت الجلسة، تحقق من `SESSION_DRIVER=file` وصلاحية `storage/framework/sessions` وHTTPS و`SESSION_SECURE_COOKIE=true`.

إذا فشل `/health/ready`، راجع قيم `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, و`DB_PASSWORD` التي أعطتها لوحة ByetHost، ثم اختبر الدخول من phpMyAdmin.

## 18. قائمة الأمان النهائية

- [ ] PHP 8.5 متاح فعليًا، أو تم توثيق أن حساب PHP 8.3 غير متوافق.
- [ ] الامتدادات المطلوبة مفعّلة.
- [ ] قاعدة MySQL/MariaDB أنشئت ووصلت إليها بيانات الاعتماد الصحيحة.
- [ ] `APP_KEY` جديد وغير فارغ.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL` هو HTTPS النهائي.
- [ ] لا يوجد `.env` داخل `public_html`.
- [ ] `public_html` يحتوي فقط على public files و`index.php` و`.htaccess` و`build`.
- [ ] `vendor` موجود خارج public root.
- [ ] `node_modules` و`.git` والاختبارات و`installer.php` غير موجودة في الحزمة.
- [ ] `storage` و`bootstrap/cache` قابلان للكتابة بأقل صلاحية.
- [ ] تم تشغيل migrations أو استيراد SQL آمن بلا أسرار.
- [ ] تم تشغيل seeder والتحقق من `demo@orcatech.test`.
- [ ] تم اختبار Starter وBusiness والميزات المقفلة.
- [ ] تم اختبار العربية RTL والإنجليزية LTR.
- [ ] `CACHE_STORE=file`, `SESSION_DRIVER=file`, `QUEUE_CONNECTION=sync`.
- [ ] `BROADCAST_DRIVER=null`، ولا يوجد اعتماد على Redis/Reverb/Horizon/Octane.
- [ ] `MAIL_MAILER=log`، والمدفوعات معطّلة، وبيانات التكاملات فارغة.
- [ ] تم اختبار `/health/live` و`/health/ready`.
- [ ] تم اختبار login وlogout وforms وuploads وassets.
- [ ] لا تظهر أسرار أو stack traces عند الخطأ.
- [ ] تم اختبار النطاق النهائي بعد تفعيل HTTPS.

## 19. القيود المعروفة

القيد الحاسم هو **PHP 8.5**. المواصفات الرسمية الظاهرة حاليًا لـ ByetHost تعلن PHP 8.3 في حسابات الاستضافة المشتركة [1] [2]، لذلك يجب الحصول على تأكيد من مزود الاستضافة بوجود PHP 8.5 قبل النشر. إن لم يكن متاحًا، فالخيار الآمن هو استخدام بيئة تستوفي PHP 8.5 أو تنفيذ مشروع ترقية واعية تشمل Laravel والاعتماديات والوحدات المحلية واختبارات كاملة؛ لا يكفي تعديل رقم PHP في Composer.

الجدولة والتكاملات الخارجية ليست جزءًا من سلامة CRM الأساسية، لكنها تحتاج Cron وبيانات اعتماد sandbox إذا أُريد اختبارها فعليًا. كما أن fallback الخاص بـ `/storage/{path}` يعرض فقط disk `public` ولا يحول الملفات الخاصة إلى ملفات عامة.

## المراجع

[1]: https://byet.host/ "ByetHost — المواصفات العامة المعلنة"
[2]: https://byet.host/free-hosting "ByetHost Free Hosting — PHP وMySQL وCron وphpMyAdmin"
