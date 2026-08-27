<?php

declare(strict_types=1);

return [

    'brand' => [
        'name' => 'OrcaTech CRM',
        'company' => 'أوركاتِك',
    ],

    'currency' => 'ج.م',

    'crm' => [
        'lead' => ['singular' => 'عميل محتمل', 'plural' => 'العملاء المحتملون'],
        'contact' => ['singular' => 'جهة اتصال', 'plural' => 'جهات الاتصال'],
        'company' => ['singular' => 'شركة', 'plural' => 'الشركات'],
        'deal' => ['singular' => 'صفقة', 'plural' => 'الصفقات'],
        'opportunity' => ['singular' => 'فرصة', 'plural' => 'الفرص'],
        'task' => ['singular' => 'مهمة', 'plural' => 'المهام'],
        'note' => ['singular' => 'ملاحظة', 'plural' => 'الملاحظات'],
    ],

    'packages' => [
        'starter' => [
            'name' => 'ستارتر',
            'tagline' => 'نظام CRM جاهز، ٩٬٩٩٩ ج.م',
        ],
        'business' => [
            'name' => 'بيزنس',
            'tagline' => 'مخصص لعمليات شركتك، ١٧٬٩٩٩ ج.م',
        ],
    ],

    'language' => [
        'change' => 'تغيير اللغة',
    ],

    'topbar' => [
        'controls' => 'أدوات مساحة العمل',
    ],

    'switcher' => [
        'title' => 'باقة العرض التجريبي',
        'demo' => 'عرض',
        'hint' => 'التبديل يحافظ على نفس البيانات والجلسة.',
        'activated_business' => 'تم تنشيط عرض باقة بيزنس — جميع الميزات المتقدمة مفتوحة الآن.',
        'activated_starter' => 'تم تنشيط عرض باقة ستارتر — تم قفل ميزات بيزنس مجددًا.',
    ],

    'features' => [
        'crm_core' => [
            'name' => 'أساسيات CRM',
            'description' => 'العملاء المحتملون وجهات الاتصال والشركات والصفقات والمهام والأنشطة في مكان واحد.',
        ],
        'visual_pipeline' => [
            'name' => 'مسار المبيعات المرئي',
            'description' => 'اسحب الصفقات عبر مراحل المسار وحافظ على زخم العمل في كل فرصة.',
        ],
        'basic_reports' => [
            'name' => 'التقارير ولوحة التحكم الأساسية',
            'description' => 'مؤشرات المبيعات الأساسية وقيمة مسار البيع ومعدلات التحويل.',
        ],
        'advanced_reports' => [
            'name' => 'التقارير المتقدمة',
            'description' => 'احصل على رؤى أعمق لأداء مبيعاتك مع تقارير وتحليلات متقدمة: منشئ تقارير مخصص، تقييم جودة العملاء المحتملين وأداء الحملات.',
        ],
        'workflow_automation' => [
            'name' => 'أتمتة سير العمل',
            'description' => 'أتمت الأعمال المتكررة بقواعد تُوزّع العملاء المحتملين وترسل المتابعات وتحرّك الصفقات تلقائيًا.',
        ],
        'data_import' => [
            'name' => 'استيراد البيانات',
            'description' => 'استورد العملاء المحتملين وجهات الاتصال والشركات من ملفات الجداول خلال دقائق.',
        ],
        'integrations' => [
            'name' => 'التكاملات',
            'description' => 'اربط الهاتف وأرقام واتساب والتسويق عبر البريد والمزيد بتكاملات بسيطة وموجهة.',
        ],
        'marketing_suite' => [
            'name' => 'منصة التسويق',
            'description' => 'حملات وقوالب بريد وصفحات هبوط ونماذج ويب ونشر اجتماعي مدمجة في نظام CRM الخاص بك.',
        ],
        'advertising' => [
            'name' => 'تحليلات الإعلانات',
            'description' => 'تابع حسابات إعلانات Google وFacebook وLinkedIn وInstagram بجانب بيانات مبيعاتك.',
        ],
        'territories' => [
            'name' => 'مناطق المبيعات',
            'description' => 'نظّم فريق المبيعات حسب المنطقة ووجّه العملاء المناسبين للفريق المناسب تلقائيًا.',
        ],
        'security_suite' => [
            'name' => 'الحماية والدخول الموحد',
            'description' => 'الدخول الموحد (SAML / OIDC) وتطبيقات OAuth وإدارة Webhooks.',
        ],
    ],

    'locked' => [
        'badge' => '🔒 مقفل',
        'available_in_business' => 'متوفر في باقة بيزنس',
    ],

    'leads' => [
        'statuses' => [
            'new' => 'جديد',
            'contacted' => 'تم التواصل',
            'qualified' => 'مؤهل',
            'lost' => 'مفقود',
        ],
        'sources' => [
            'website' => 'الموقع الإلكتروني',
            'referral' => 'ترشيح',
            'social_media' => 'وسائل التواصل',
            'direct' => 'اتصال مباشر',
            'other' => 'أخرى',
        ],
    ],

    'deals' => [
        'stages' => [
            'prospect' => 'استكشاف',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'won' => 'ناجحة',
            'lost' => 'خاسرة',
            'closed' => 'مغلقة',
        ],
    ],

    'upgrade' => [
        'title' => 'معاينة الترقية',
        'eyebrow' => 'ميزة باقة بيزنس',
        'default_feature' => 'هذه الميزة',
        'default_description' => 'افتح هذه الإمكانية بتحويل العرض إلى باقة بيزنس.',
        'available_in' => 'متوفرة في',
        'cta_button' => 'تجربة عرض بيزنس',
        'cta' => 'تجربة عرض :package — :price :currency',
        'cta_hint' => 'لا حاجة للدفع — هذا عرض تجريبي يبدّل الباقة فورًا.',
        'benefits_label' => 'رؤية أوضح لأعمالك',
        'benefits_title' => 'كل ما يحتاجه فريقك للعمل بوتيرة أسرع',
        'ready_title' => 'استكشف تجربة بيزنس الكاملة',
        'ready_description' => 'بدّل العرض التجريبي بنقرة واحدة واستكشف التقارير المتقدمة والأتمتة والتكاملات والمزيد.',
        'unlocked_message' => 'تم تنشيط عرض باقة بيزنس — جميع الميزات المتقدمة مفتوحة الآن.',
    ],

    'dashboard' => [
        'title' => 'لوحة تحكم CRM',
        'total_leads' => 'إجمالي العملاء المحتملين',
        'new_leads' => 'عملاء جدد',
        'qualified_leads' => 'عملاء مؤهلون',
        'active_deals' => 'صفقات نشطة',
        'won_deals' => 'صفقات ناجحة',
        'lost_deals' => 'صفقات خاسرة',
        'pipeline_value' => 'قيمة مسار البيع',
        'conversion_rate' => 'معدل التحويل',
        'open_tasks' => 'مهام مفتوحة',
        'overdue_tasks' => 'مهام متأخرة',
        'recent_activities' => 'الأنشطة الأخيرة',
        'upcoming_tasks' => 'المهام القادمة',
        'advanced_analytics' => 'تحليلات مبيعات متقدمة',
        'analytics_locked' => 'تحليلات المبيعات المتقدمة جزء من باقة بيزنس.',
        'view_all' => 'عرض الكل',
        'no_activities' => 'لا توجد أنشطة بعد.',
        'no_tasks' => 'لا مهام مستحقة قريبًا.',
        'business_insights' => 'رؤى بيزنس أصبحت مفتوحة',
        'stage_breakdown' => 'الصفقات حسب المرحلة',
        'lead_sources' => 'العملاء حسب المصدر',
        'win_rate' => 'معدل الفوز',
        'avg_deal_size' => 'متوسط حجم الصفقة',
        'status' => 'الحالة',
        'assigned_to' => 'مسؤول المهمة',
        'due_date' => 'تاريخ الاستحقاق',
        'date' => 'التاريخ',
        'activity_type' => 'النوع',
    ],

    'tasks' => [
        'statuses' => [
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتملة',
        ],
    ],

    'activities' => [
        'types' => [
            'call' => 'مكالمة',
            'meeting' => 'اجتماع',
            'email' => 'بريد إلكتروني',
            'note' => 'ملاحظة',
            'created' => 'تم الإنشاء',
            'updated' => 'تم التحديث',
        ],
    ],

    'addons_page' => [
        'title' => 'الإضافات',
        'subtitle' => 'وسّع OrcaTech CRM عندما تحتاج للمزيد.',
        'available_as_addon' => 'متوفرة كإضافة مدفوعة',
        'addon_note' => 'يتم تسعير الإضافات بشكل منفصل ويقوم فريق OrcaTech بإعدادها عند بدء التشغيل.',
        'enterprise_note' => 'تحتاج شيئًا مخصصًا؟ عملاء Enterprise يحصلون على وحدات مخصصة وأتمتة واتساب ودعم متعدد الفروع ووظائف خاصة بمجالك.',
    ],

    'addons' => [
        'whatsapp_integration' => [
            'name' => 'تكامل واتساب',
            'description' => 'تواصل مع العملاء عبر واتساب للأعمال مباشرة من سجل العميل في النظام.',
        ],
        'custom_module' => [
            'name' => 'وحدة مخصصة',
            'description' => 'وحدة مصممة خصيصًا لعمليات شركتك ويبنيها فريق OrcaTech ويطورها باستمرار.',
        ],
        'ai_assistant' => [
            'name' => 'مساعد ذكي',
            'description' => 'ملخصات وردود مقترحة وإرشادات للخطوة التالية مدعومة بالذكاء الاصطناعي.',
        ],
        'payment_integration' => [
            'name' => 'تكامل المدفوعات',
            'description' => 'استلم المدفعات وطابقها مع الصفقات تلقائيًا.',
        ],
        'api_integration' => [
            'name' => 'تكامل API',
            'description' => 'اربط OrcaTech CRM بنظام ERP أو موقعك الإلكتروني أو أنظمتك الداخلية.',
        ],
        'custom_reports' => [
            'name' => 'تقارير مخصصة',
            'description' => 'تقارير مصممة حول مؤشرات الأداء الخاصة بك وتُسلَّم حسب الجدول الذي تختاره.',
        ],
        'mobile_app' => [
            'name' => 'تطبيق جوال',
            'description' => 'تطبيق جوال بهوية علامتك لفريق المبيعات على iOS وAndroid.',
        ],
    ],

    'demo_banner' => [
        'welcome_starter' => 'مرحبًا بك في العرض التجريبي لباقة OrcaTech CRM ستارتر. استكشف نظام CRM الكامل — :locked ميزة من باقة بيزنس معلّمة بـ 🔒 ويمكن معاينتها في أي وقت.',
        'welcome_business' => 'أنت تشاهد عرض باقة بيزنس — جميع القدرات المتقدمة مفتوحة.',
    ],

    'login' => [
        'heading' => 'تسجيل الدخول إلى OrcaTech CRM',
        'subheading' => 'بيئة العرض التجريبي لـ OrcaTech CRM',
        'credentials_title' => 'بيانات الدخول التجريبية',
        'email_label' => 'البريد الإلكتروني',
        'password_label' => 'كلمة المرور',
    ],
];
