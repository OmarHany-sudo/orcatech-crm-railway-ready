# OrcaTech CRM — Railway Audit Report

## النتيجة التنفيذية

تم أخذ آخر نسخة معدلة من `orcatech-crm-byethost-ready.zip` كأساس، ثم تجهيزها للنشر على Railway دون إعادة بناء CRM أو تغيير واجهته أو إنشاء codebase ثانٍ. أضيفت صورة Docker إنتاجية PHP 8.5/Apache متعددة المراحل، إعداد Railway، endpoint للصحة، تشغيل ديناميكي على `PORT`، أمر تهيئة بيانات العرض، وإرشادات نشر خاصة بالمشروع.

> **Railway deployment: NOT VERIFIED.** لم تتوفر بيانات وصول Railway أو مشروع مؤقت داخل البيئة، لذلك لم يتم الادعاء بنشر حقيقي. تم تنفيذ تدقيق المصدر وفحوصات static محلية فقط.

## القرار المعماري

| البند | القرار | الحالة |
|---|---|---|
| Web Service | خدمة Railway واحدة تبني `Dockerfile` وتشغل Apache/PHP | جاهز للتطبيق |
| Database | Railway MySQL الرسمي، وليس PostgreSQL | القرار الآمن |
| Queue | `sync` | لا يحتاج Worker دائم |
| Cache/Session | `file` | مناسب للعرض الخفيف |
| Redis | غير مطلوب | اختياري فقط |
| Horizon | غير مطلوب | غير مفعّل في مسار العرض |
| Reverb/WebSockets | غير مطلوب | HTTP/Livewire الأساسي لا يعتمد عليه |
| Octane | غير مطلوب | runtime يستخدم Apache/PHP-FPM |
| Scheduler | لا توجد خدمة إضافية افتراضيًا | تُضاف فقط عند الحاجة |
| Upload persistence | ephemeral افتراضيًا؛ volume اختياري | موثق في `RAILWAY_DEPLOYMENT.md` |

## قرار قاعدة البيانات

تم رفض التحويل القسري إلى PostgreSQL. تدقيق الكود وجد 51 ملف migration، و21 استخدامًا للاستعلامات الخام أو ما شابه، من بينها backfill يستخدم subquery داخل `DB::raw`، إضافة إلى فروع Pulse ذات expressions خاصة بـ MySQL/MariaDB وإعداد tenancy يعتمد MySQL manager. توجد استعلامات JSON و`DATE()` وعمليات إحصائية يمكن أن تكون قابلة للنقل، لكن ذلك لا يكفي لإثبات سلامة جميع migrations ومسارات Liberu CRM على PostgreSQL. Railway يوفر قالب MySQL رسميًا بمتغيرات `MYSQL_URL` و`MYSQLHOST` و`MYSQLPORT` و`MYSQLUSER` و`MYSQLPASSWORD` و`MYSQLDATABASE`؛ لذلك MySQL هو الخيار الأقل مخاطرة.

لم يتم تشغيل `migrate:fresh` أو `db:seed` على PostgreSQL، ولذلك لم يُعلن PostgreSQL متوافقًا. الصورة تحتوي `pdo_mysql` كما تحتوي `pdo_pgsql` لدعم أدوات التشخيص المستقبلية، لكن الإعداد الافتراضي هو MySQL.

## PHP وDocker

قيد Composer الفعلي هو PHP `^8.5` مع Laravel 13 وFilament 5 وLivewire 4. لم يتم تخفيض القيد. Dockerfile الجديد يستخدم مراحل Node 22 لبناء `npm ci` و`npm run build`، وComposer لتثبيت production dependencies، ثم runtime مبنيًا على PHP 8.5 Apache. لا يستخدم `php artisan serve`، ولا Octane، ولا Supervisor، ولا RoadRunner.

يقرأ `railway/start.sh` قيمة `PORT`، يتحقق من كونها رقمًا، يضبط Apache وVirtualHost عليها، ثم يشغل `apache2-foreground`. الصورة لا تحتوي `node_modules` في runtime، ولا ملفات الاختبار أو Git metadata أو `.env` حقيقيًا.

## التغييرات الأساسية

| الملف | التغيير |
|---|---|
| `Dockerfile` | استبدال صورة Octane القديمة بصورة PHP 8.5/Apache متعددة المراحل |
| `railway.json` | تثبيت Dockerfile، healthcheck `/health/live`، وrestart policy |
| `railway/start.sh` | PORT ديناميكي، storage setup، cache، وmigrations/seeding اختياريان فقط |
| `railway/apache-vhost.conf` | document root إلى `/var/www/html/public` وlogs إلى stdout/stderr |
| `railway/php-production.ini` | OPcache وإعدادات PHP production |
| `railway/seed-demo.sh` | تهيئة migrations وبيانات Nile Properties يدويًا مرة واحدة |
| `.env.example` | متغيرات Railway MySQL و`LOG_CHANNEL=stderr` دون أسرار |
| `config/database.php` | دعم `DB_URL` و`MYSQL_URL` ومتغيرات Railway الفردية |
| `README.md` | إضافة قسم Demo Deployment |
| `RAILWAY_DEPLOYMENT.md` | دليل نشر كامل مخصص للمشروع |
| `RAILWAY_AUDIT_REPORT.md` | هذا التقرير |

## العرض والبيانات والصلاحيات

يستخدم العرض `NilePropertiesDemoSeeder` بيانات خيالية deterministic تشمل Leads وContacts وCompanies وDeals وOpportunities وPipeline وStages وTasks وActivities وNotes ومندوبي مبيعات. حساب العرض ليس `super_admin` بل مستخدم demo بدور `manager`، وتبقى القيود المركزية على الحذف وإدارة المستخدمين والأدوار والصلاحيات وتغيير الاعتمادات عند `DEMO_MODE=true`.

لم يتم تغيير بنية Starter/Business أو feature gating أو localization. يجب تنفيذ اختبار يدوي بعد النشر للتأكد من أن direct URLs لا تتجاوز قيود Starter، وأن العربية RTL والإنجليزية LTR ومبدل اللغة والواجهات والجداول ورسائل الميزات المقفلة تعمل على domain الحقيقي.

## الاختبارات

| الاختبار | الحالة | الملاحظة |
|---|---|---|
| تدقيق آخر ZIP فعليًا | **VERIFIED** | تم فك آخر ZIP والعمل عليه مباشرة |
| تدقيق MySQL/PostgreSQL الثابت | **VERIFIED** | تم فحص migrations وraw SQL وtenancy وPulse |
| `npm ci` و`npm run build` بعد تحويل Docker | **VERIFIED** | نجح البناء محليًا بعد توفير Filament 5.6.6 المطابق للإصدار المقفل؛ Docker stage الآن يثبت vendor قبل frontend |
| PHP syntax | **VERIFIED** | النسخة الأصلية المعدلة اجتازت 815 ملف PHP؛ ملفات Railway أُعيد فحصها نصيًا لاحقًا |
| `composer validate` | **VERIFIED** | `composer.json` و`composer.lock` صالحان؛ يوجد تحذير غير مانع لأن `twilio/sdk` يستخدم قيد الإصدار `*` |
| `php artisan test` | **NOT VERIFIED** | لا يوجد vendor runtime كامل في sandbox الحالية |
| Docker build محلي | **NOT VERIFIED** | يعتمد على توفر Docker daemon وبناء PHP 8.5 والصورة الخارجية |
| Docker run وsmoke test | **NOT VERIFIED** | لم تُشغّل صورة فعلية دون Docker وMySQL |
| migrations وseeders | **NOT VERIFIED** | لم تُشغّل على Railway MySQL فعلية |
| Railway deployment | **NOT VERIFIED** | لا توجد credentials أو temporary project متاح |

## خطوات النشر المختصرة

1. ارفع هذه النسخة إلى مستودع GitHub خاص أو عام حسب رغبتك.
2. أنشئ مشروعًا على Railway وأضف خدمة MySQL.
3. أضف خدمة التطبيق من GitHub repository.
4. أضف Variables من جدول `RAILWAY_DEPLOYMENT.md`، خصوصًا `APP_KEY` و`APP_URL` و`DB_CONNECTION=mysql` و`DB_URL=${{MySQL.MYSQL_URL}}`.
5. اترك `RUN_MIGRATIONS` و`RUN_DEMO_SEED` غير معرفين أو `false` أثناء الإقلاع الأول.
6. بعد نجاح deploy، افتح Shell لخدمة التطبيق ونفذ `php artisan migrate --force` ثم `sh railway/seed-demo.sh` أو `php artisan db:seed --force`.
7. اضبط Healthcheck Path على `/health/live`، ثم اختبر `/health/ready` و`/login` و`/dashboard` والوظائف الأساسية.

## العوائق المتبقية

العائق أمام إثبات الجاهزية التشغيلية الكاملة هو عدم توفر Railway credentials/temporary project وعدم توفر Docker daemon داخل sandbox. هذا يمنع الادعاء بأن Docker build أو migrations أو smoke test أو Railway deployment نجحت فعليًا. نجح اختبار `npm run build` واختبار `composer validate`، بينما يبقى Docker build والتشغيل مع MySQL اختبارين مطلوبين على جهاز أو خدمة تملك Docker وPHP 8.5/قاعدة MySQL فعلية.

## References

[1]: https://docs.railway.com/databases/mysql "Railway MySQL"
[2]: https://docs.railway.com/deployments/healthchecks "Railway Healthchecks"
[3]: https://docs.railway.com/guides/laravel "Railway Deploy a Laravel App"
[4]: https://docs.railway.com/volumes "Railway Using Volumes"
[5]: https://docs.railway.com/pricing/plans "Railway Pricing Plans"
