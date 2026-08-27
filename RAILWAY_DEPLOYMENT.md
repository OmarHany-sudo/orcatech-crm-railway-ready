# OrcaTech CRM — Railway Demo Deployment

هذا الدليل مخصص للنسخة الحالية من OrcaTech CRM. الهدف هو تشغيل **عرض عام خفيف** على Railway باستخدام خدمة Web واحدة وقاعدة MySQL واحدة، مع إبقاء الوظائف الحالية والباقات Starter/Business وتجربة العربية والإنجليزية دون إنشاء codebase بديل.

> **حالة التحقق:** تجهيز ملفات Railway وDocker وفحوصات المصدر تم التحقق منهما محليًا. النشر الحقيقي على مشروع Railway لم يُنفذ من هذه البيئة لعدم توفر بيانات دخول Railway، لذلك يجب اعتبار `Railway deployment: NOT VERIFIED` إلى أن تنفذ خطوات smoke test الأخيرة على مشروعك.

## 1. البنية الحالية والقرار التقني

المشروع يستخدم PHP 8.5 أو أحدث، Laravel 13، Filament 5، Livewire 4، وحزمًا اختيارية لـ Octane وHorizon وReverb. صورة Railway الجديدة تستخدم PHP-FPM عبر Apache في حاوية إنتاجية واحدة، ولا تعتمد على `php artisan serve` أو Octane أو Horizon أو Reverb.

تم اختيار **MySQL** لقاعدة Railway بدل فرض PostgreSQL. المشروع مصمم أصلًا على MySQL/MariaDB، وتظهر في التدقيق استعلامات `DB::raw` عامة، واستعلام backfill يعتمد على subquery، ومهاجرة Pulse تحتوي expressions خاصة بـ MySQL/MariaDB، كما أن إعداد tenancy الحالي يختار MySQL database manager. لم يتم إعلان PostgreSQL متوافقًا دون تشغيل migrations وseeders وكل مسارات CRM عليه. Railway يوفر قالب MySQL رسميًا ومتغيرات `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, و`MYSQL_URL` [1].

## 2. المتطلبات

يلزم حساب Railway، مستودع GitHub أو نسخة محلية للمشروع، وتوفر PHP 8.5 داخل الصورة فقط؛ لا تحتاج إلى تثبيت PHP محليًا إذا كنت ستبني عبر Railway. يلزم أيضًا إضافة خدمة MySQL من Railway وربطها بخدمة التطبيق. Railway يحدد حد الاستماع عبر متغير `PORT`، ولذلك لا تستخدم منفذًا ثابتًا في إعدادات الخدمة [2].

الملف `Dockerfile` يستخدم build متعدد المراحل: مرحلة Node تبني Vite، ومرحلة Composer تثبت الاعتماديات production، ومرحلة runtime تحتوي Apache وPHP extensions المطلوبة فقط. لا ترفع `.env` أو `vendor/` أو `node_modules/` أو مفاتيح التكاملات إلى GitHub.

## 3. إنشاء مشروع Railway

أنشئ مشروعًا جديدًا من لوحة Railway، ثم أضف خدمة MySQL من قائمة **New** أو من قالب MySQL الرسمي. أضف بعد ذلك خدمة التطبيق من GitHub repo الذي يحتوي هذه النسخة، أو ارفع المشروع عبر Railway CLI. Railway يوثق طريقتي GitHub وCLI لتطبيقات Laravel [3].

عند اتصال الخدمة بالمستودع، سيكتشف Railway ملف `railway.json` و`Dockerfile`. الملف يثبت استخدام Dockerfile، ويضبط healthcheck على `/health/live`، ويستخدم restart policy من نوع `ON_FAILURE`.

## 4. متغيرات Railway

أضف المتغيرات التالية إلى **Variables** في خدمة التطبيق. لا تنسخ قيمة `APP_KEY` من هذا الدليل؛ ولّد مفتاحًا حقيقيًا مرة واحدة باستخدام PHP 8.5 أو شغّل `php artisan key:generate --show` في بيئة آمنة ثم ألصق القيمة في Railway.

| المتغير | القيمة أو المصدر |
|---|---|
| `APP_NAME` | `OrcaTech CRM` |
| `APP_ENV` | `production` |
| `APP_KEY` | مفتاح Laravel حقيقي، لا تتركه فارغًا |
| `APP_DEBUG` | `false` |
| `APP_URL` | رابط Railway العام بعد إنشاء domain، مثل `https://your-app.up.railway.app` |
| `ASSET_URL` | اتركه فارغًا ما لم تستخدم CDN؛ الأصول النسبية تعمل افتراضيًا |
| `APP_LOCALE` | `en` أو `ar` حسب لغة البداية |
| `APP_FALLBACK_LOCALE` | `en` |
| `APP_TIMEZONE` | `UTC` أو المنطقة المطلوبة |
| `DB_CONNECTION` | `mysql` |
| `DB_URL` | `${{MySQL.MYSQL_URL}}` إذا كان اسم خدمة قاعدة البيانات `MySQL` |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `CACHE_STORE` | `file` |
| `SESSION_DRIVER` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `local` للعرض الخفيف، أو `public` مع volume للرفع الدائم |
| `LOG_CHANNEL` | `stderr` |
| `LOG_STDERR_FORMATTER` | `Monolog\\Formatter\\JsonFormatter` |
| `LOG_LEVEL` | `warning` |
| `DEMO_MODE` | `true` |
| `DEMO_USER_EMAIL` | `demo@orcatech.test` أو بريد العرض الذي تختاره |
| `DEMO_USER_PASSWORD` | كلمة مرور عرض جديدة إن رغبت، ولا تستخدم كلمة مرور حقيقية |
| `SANCTUM_STATEFUL_DOMAINS` | hostname الخاص بالتطبيق دون `https://`، مثل `your-app.up.railway.app` |
| `SESSION_SECURE_COOKIE` | `true` |
| `MAIL_MAILER` | `log` للعرض العام |
| `STRIPE_SUBSCRIPTIONS_ENABLED` | `false` |
| `TELESCOPE_ENABLED` | `false` |

يمكن استخدام `MYSQL_URL` مباشرة بدل `DB_URL`؛ تمت إضافة fallback لذلك في `config/database.php`. لا تعتمد على أسماء خدمة مختلفة دون تعديل مرجع `${{Service.Variable}}` في لوحة Railway.

## 5. Docker deployment

لا تستخدم Docker Compose داخل Railway؛ Railway يذكر أن Docker Compose غير مدعوم كطريقة نشر للخدمة [3]. يكفي رفع المشروع مع `Dockerfile` و`railway.json`.

البناء يحدث في Docker بهذه المراحل: `npm ci` ثم `npm run build`، ثم `composer install --no-dev`، ثم نسخ التطبيق والأصول إلى صورة PHP 8.5/Apache. صورة runtime لا تحتوي `node_modules`، ولا ملفات الاختبار، ولا Git metadata، ولا تعتمد على Octane أو Supervisor.

لا توجد قيمة production ثابتة للمنفذ. سكربت `railway/start.sh` يقرأ `PORT` الذي تحقنه Railway، ويعيد ضبط Apache على هذا المنفذ، ثم يبدأ `apache2-foreground`. هذا يحقق شرط Railway بأن يستمع web server إلى `PORT` [2].

## 6. Build وStart configuration

عادة لا تحتاج إلى إدخال Build Command أو Start Command يدويًا لأن `railway.json` وDockerfile يقدمانهما. إذا كانت لوحة Railway تطلب Start Command صراحة، استخدم:

```text
/usr/local/bin/railway-start
```

لا تستخدم:

```text
php artisan serve
```

يحتوي Dockerfile على `HEALTHCHECK` داخلي، ويحتوي `railway.json` على healthcheck خارجي. إعدادات Railway في `railway.json` ليست بديلًا عن Variables في لوحة الخدمة؛ قيم البيئة السرية تبقى في Railway.

## 7. قاعدة البيانات والهجرة

بعد أن تصبح خدمة MySQL متاحة، نفّذ migrations مرة واحدة من **Shell** الخاص بخدمة التطبيق أو عبر طريقة Railway CLI المرتبطة بالخدمة:

```bash
php artisan migrate --force
```

يجب عدم ضبط `RUN_MIGRATIONS=true` كإعداد دائم إذا كنت لا تريد تشغيل migration في كل restart. سكربت `railway/start.sh` يتركه `false` افتراضيًا، ولذلك لا يحدث أي تغيير تلقائي تدميري عند إعادة تشغيل الحاوية. لا يوجد `migrate:fresh` في مسار startup.

## 8. تهيئة بيانات العرض

بعد نجاح migrations على قاعدة جديدة، نفّذ مرة واحدة:

```bash
sh railway/seed-demo.sh
```

أو نفّذ الأوامر يدويًا:

```bash
php artisan db:seed --force
```

`DatabaseSeeder` يستدعي `MenuSeeder` و`NilePropertiesDemoSeeder`. بيانات Nile Properties خيالية ومحددة deterministic وتشمل شركات وجهات اتصال وLeads وDeals وPipeline وStages وTasks وActivities وNotes ومندوبي مبيعات. لا تستخدم بيانات عملاء حقيقية.

لا تترك `RUN_DEMO_SEED=true` في خدمة عامة؛ seeder العرض يعيد تجهيز بعض البيانات المقصودة للعرض، ويجب أن يُستخدم في قاعدة جديدة أو عندما تقصد إعادة تهيئة بيانات العرض.

## 9. Health check

تم توفير endpointين:

```text
/health/live
/health/ready
```

الأول يعيد HTTP 200 وحالة `live` ولا يقرأ أسرارًا. الثاني يختبر اتصال قاعدة البيانات ويعيد `ready` أو HTTP 503 برسالة عامة لا تحتوي stack trace أو credentials. اضبط Railway Healthcheck Path على:

```text
/health/live
```

Railway ينتظر HTTP 200 من endpoint المهيأ قبل تحويل المرور إلى النسخة الجديدة، وhealthcheck ليس مراقبة مستمرة بعد نجاح النشر [2].

## 10. التخزين والملفات

نظام ملفات حاوية Railway ephemeral. ملفات العرض الثابتة مضمّنة داخل image، أما uploads التي تُكتب أثناء التشغيل فلن تكون دائمة مع `FILESYSTEM_DISK=local`. إذا احتاج العرض إلى الاحتفاظ بالملفات المرفوعة، أنشئ Volume للخدمة واربطه على:

```text
/var/www/html/storage/app/public
```

ثم اضبط:

```text
FILESYSTEM_DISK=public
```

Railway يوضح أن volume يُركّب عند runtime وليس أثناء build أو pre-deploy، وأن مسار mount يجب أن يتضمن `/app` إذا كان التطبيق يكتب إلى مسار نسبي تحت `/app` [4]. في هذه الصورة مسار التطبيق هو `/var/www/html`، لذلك استخدم المسار المذكور أعلاه بدل mount عشوائي. لا تحتاج إلى volume لبيانات MySQL نفسها؛ خدمة MySQL تدير تخزين قاعدة البيانات وفق إعدادها.

## 11. Logs

اضبط `LOG_CHANNEL=stderr` و`LOG_STDERR_FORMATTER=Monolog\\Formatter\\JsonFormatter` حتى تظهر الأخطاء في Railway Logs بدل الاعتماد على ملفات محلية ephemeral. هذا متوافق مع توصية دليل Laravel على Railway [3]. لا تسجل APP_KEY أو كلمات المرور أو tokens.

## 12. Starter وBusiness

لم يتم تغيير architecture المنتج. ما زالت هناك application واحدة وcodebase واحدة مع وضعي Starter وBusiness. يجب اختبار الوصول إلى صفحات الميزات المقفلة بحساب Starter، لأن direct URLs يجب ألا تتجاوز backend feature gating. حساب العرض العام يستخدم دور `manager` وليس `super_admin`، وتوجد حواجز مركزية تمنع حذف السجلات وإدارة المستخدمين والأدوار والصلاحيات وتغيير الاعتمادات للحساب التجريبي عند تفعيل `DEMO_MODE`.

## 13. العربية والإنجليزية

لم يتم حذف ملفات الترجمة أو تغيير اتجاه الواجهة. اختبر من لوحة التطبيق: تبديل اللغة، اتجاه RTL للعربية، اتجاه LTR للإنجليزية، التنقل، dashboard، نماذج CRM، الجداول، رسائل الميزات المقفلة، ومبدل الباقات. أي قيمة `APP_URL` أو `SANCTUM_STATEFUL_DOMAINS` يجب أن تستخدم hostname الحقيقي لتجنب مشاكل الجلسة وOAuth.

## 14. Queues وHorizon وReverb وOctane وScheduler

لأن الهدف public demo منخفض التكلفة، الإعداد الافتراضي هو `QUEUE_CONNECTION=sync` وfile cache وfile sessions. لذلك لا تحتاج إلى Worker دائم أو Redis أو Horizon. Reverb اختياري وغير مطلوب لتشغيل HTTP/Livewire الأساسي، وOctane ليس runtime للصورة الجديدة.

تم تدقيق scheduler؛ لا يتم إنشاء Cron service تلقائيًا. إذا احتجت مهمة دورية لاحقًا، أنشئ خدمة Railway منفصلة فقط عند وجود حاجة مثبتة، ولا تجعل الخدمة العامة تعتمد عليها للإقلاع. Railway يوثق معماريته متعددة الخدمات للـ app والـ cron والـ worker، لكن هذه الإضافات ليست ضرورية لهذا العرض الخفيف [3].

## 15. Domain وHTTPS

بعد نجاح النشر، افتح إعدادات خدمة التطبيق ثم **Networking → Generate Domain**. بعد ذلك حدّث `APP_URL` و`SANCTUM_STATEFUL_DOMAINS` بالقيمة الفعلية وأعد النشر. أضف custom domain من نفس القسم إذا كان متاحًا لحسابك. يجب أن تصل الزيارات عبر HTTPS، ولا تضع `http://localhost` في أي متغير production.

## 16. Smoke test بعد النشر

نفّذ الاختبارات التالية على domain الحقيقي:

| المسار | النتيجة المتوقعة |
|---|---|
| `/health/live` | HTTP 200 وJSON عام |
| `/health/ready` | HTTP 200 بعد ربط MySQL وإنجاز migrations |
| `/` | صفحة البداية أو إعادة توجيه منطقية |
| `/login` | نموذج تسجيل الدخول |
| `/dashboard` | يتطلب authentication ولا يكشف لوحة عامة بلا حماية |
| حساب العرض | يدخل بحساب العرض ويشاهد بيانات Nile Properties |
| اللغة العربية | RTL وترجمة واجهة سليمة |
| اللغة الإنجليزية | LTR وترجمة واجهة سليمة |
| Starter/Business | الميزات المقفلة لا تُفتح عبر direct URL |
| CRUD CRM | اختبار Leads وContacts وCompanies وDeals وPipeline وTasks وActivities وReports |

لا تعلن نجاح هذا الجدول إلا بعد تنفيذه على deployment فعلي؛ الاختبارات المحلية وحدها لا تثبت Railway deployment.

## 17. Troubleshooting

إذا ظهر `Application key is missing` فأضف `APP_KEY` حقيقيًا إلى Variables ثم أعد النشر. إذا فشل الاتصال بقاعدة البيانات فتحقق من أن `DB_CONNECTION=mysql` وأن مراجع `${{MySQL.*}}` تشير إلى اسم الخدمة الصحيح، ثم نفّذ `php artisan migrate --force`.

إذا فشل healthcheck، تحقق من أن خدمة Apache تستمع إلى `PORT` وأن `APP_URL` لا يفرض hostname غير صحيح. إذا ظهرت الأصول مفقودة، تحقق من نجاح مرحلة `npm run build` ومن وجود `public/build/manifest.json` داخل image.

إذا اختفت uploads بعد restart، فهذا متوقع مع local ephemeral storage؛ أضف volume واضبط `FILESYSTEM_DISK=public` كما في قسم التخزين. إذا ظهرت أخطاء بسبب تكامل خارجي، اترك متغيراته فارغة في العرض العام؛ لا تضف OAuth أو SMTP أو Stripe أو API keys لمجرد إخفاء رسالة اختيارية.

## 18. التكلفة والقيود

خطة Railway Free الحالية هي $0 شهريًا مع $1 من الرصيد المجاني الشهري، وتعرض Railway حدودًا افتراضية لكل خدمة تصل إلى 0.5 GB RAM و1 vCPU و1 GB ephemeral storage و0.5 GB volume storage [5]. هذه الأرقام والاعتمادات قابلة للتغيير، ويجب مراجعة صفحة الأسعار في حسابك قبل اعتبار العرض مجانيًا دائمًا. وجود Web Service وقاعدة MySQL يعني استهلاك موارد خادمَين؛ أوقف المشروع أو راقب usage إذا تجاوز الرصيد.

قوالب قواعد البيانات في Railway unmanaged؛ النسخ الاحتياطي والمراقبة والصيانة مسؤولية صاحب المشروع [1] [5]. لا تستخدم هذا الإعداد كبديل عن بنية production عالية التوافر من دون إضافة backup وobservability مناسبين.

## 19. الملفات التي أضيفت أو عُدلت

| الملف | الغرض |
|---|---|
| `Dockerfile` | صورة Docker إنتاجية متعددة المراحل تعمل بـ Apache مع PHP 8.5 مع Composer وVite |
| `railway.json` | Docker builder وhealthcheck وrestart policy |
| `railway/start.sh` | قراءة `PORT`، تهيئة Apache، cache، وعمليات اختيارية غير تدميرية |
| `railway/apache-vhost.conf` | Apache document root إلى `public` وlogs إلى stdout/stderr |
| `railway/php-production.ini` | إعداد PHP production وOPcache |
| `railway/seed-demo.sh` | migration وseed يدويان لأول تهيئة فقط |
| `.env.example` | متغيرات Railway MySQL وstderr logging بدون أسرار |
| `config/database.php` | دعم `DB_URL` وRailway `MYSQL_URL` و`MYSQL*` variables |
| `README.md` | قسم Demo Deployment مختصر |
| `RAILWAY_AUDIT_REPORT.md` | التقرير التنفيذي ونتائج التحقق والقيود |

## المراجع

[1]: https://docs.railway.com/databases/mysql "Railway MySQL"
[2]: https://docs.railway.com/deployments/healthchecks "Railway Healthchecks"
[3]: https://docs.railway.com/guides/laravel "Railway Deploy a Laravel App"
[4]: https://docs.railway.com/volumes "Railway Using Volumes"
[5]: https://docs.railway.com/pricing/plans "Railway Pricing Plans"
