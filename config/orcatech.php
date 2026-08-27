<?php

declare(strict_types=1);

return [

    'branding' => [
        'name' => env('ORCATECH_BRAND_NAME', 'OrcaTech CRM'),
        'company' => 'OrcaTech',
        'tagline' => 'The CRM built around your business.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo packages
    |--------------------------------------------------------------------------
    |
    | The commercial packages the demo sells. "starter" is the default demo
    | package; "business" is its superset. Prices are in EGP and shown in
    | upgrade / switcher UI.
    */

    'packages' => [
        'starter' => [
            'key' => 'starter',
            'name' => 'Starter',
            'price' => 9999,
            'currency' => 'EGP',
            'color' => 'emerald',
            'level' => 10,
        ],
        'business' => [
            'key' => 'business',
            'name' => 'Business',
            'price' => 17999,
            'currency' => 'EGP',
            'color' => 'sky',
            'level' => 20,
        ],
    ],

    'default_package' => 'starter',

    /*
    |--------------------------------------------------------------------------
    | Feature catalog
    |--------------------------------------------------------------------------
    |
    | Every gated capability has a minimum package level required to unlock
    | it. CORE features (level 0) are always available. The key is used by
    | the gate middleware, navigation, dashboard widgets and tests.
    |
    | `route_slugs` lists Filament resource/page slugs protected by the
    | feature so direct URL access is redirected to the upgrade page.
    */

    'features' => [
        // Starter-level (available from the first package tier).
        'crm_core' => [
            'level' => 0,
            'icon' => 'heroicon-o-users',
            'route_slugs' => [],
        ],
        'visual_pipeline' => [
            'level' => 0,
            'icon' => 'heroicon-o-rectangle-stack',
            'route_slugs' => ['visual-pipeline'],
        ],
        'basic_reports' => [
            'level' => 0,
            'icon' => 'heroicon-o-chart-bar',
            'route_slugs' => [],
        ],

        // Business-only.
        'advanced_reports' => [
            'level' => 20,
            'icon' => 'heroicon-o-chart-pie',
            'group' => 'Analytics',
            'route_slugs' => ['report-page', 'report-customizer'],
        ],
        'workflow_automation' => [
            'level' => 20,
            'icon' => 'heroicon-o-cpu-chip',
            'group' => 'Automation',
            'route_slugs' => ['workflows'],
        ],
        'data_import' => [
            'level' => 20,
            'icon' => 'heroicon-o-arrow-down-tray',
            'group' => 'Administration',
            'route_slugs' => [],
        ],
        'integrations' => [
            'level' => 20,
            'icon' => 'heroicon-o-link',
            'group' => 'Communications',
            'route_slugs' => ['twilio-integration', 'twilio-settings', 'mailchimp-integration', 'whats-app-numbers', 'call-settings'],
        ],
        'marketing_suite' => [
            'level' => 20,
            'icon' => 'heroicon-o-megaphone',
            'group' => 'Marketing',
            'route_slugs' => ['marketing-campaigns', 'email-templates', 'social-media-posts', 'landing-pages', 'form-builders', 'mailchimp-campaigns'],
        ],
        'advertising' => [
            'level' => 20,
            'icon' => 'heroicon-o-magnifying-glass-circle',
            'group' => 'Analytics',
            'route_slugs' => ['advertising-accounts', 'campaigns', 'ad-sets', 'ads'],
        ],
        'territories' => [
            'level' => 20,
            'icon' => 'heroicon-o-map-pin',
            'group' => 'Sales',
            'route_slugs' => ['territories'],
        ],
        'security_suite' => [
            'level' => 20,
            'icon' => 'heroicon-o-shield-check',
            'group' => 'Administration',
            'route_slugs' => ['sso', 'saml', 'o-auth-configurations', 'webhook-deliveries'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Add-ons & Enterprise positioning
    |--------------------------------------------------------------------------
    |
    | Shown on the Add-ons page as "Available as an add-on" cards. Nothing
    | here is implemented; they exist for commercial positioning only.
    */

    'addons' => [
        'whatsapp_integration' => ['icon' => 'heroicon-o-chat-bubble-left-right'],
        'custom_module' => ['icon' => 'heroicon-o-puzzle-piece'],
        'ai_assistant' => ['icon' => 'heroicon-o-sparkles'],
        'payment_integration' => ['icon' => 'heroicon-o-credit-card'],
        'api_integration' => ['icon' => 'heroicon-o-code-bracket'],
        'custom_reports' => ['icon' => 'heroicon-o-chart-bar'],
        'mobile_app' => ['icon' => 'heroicon-o-device-phone-mobile'],
    ],

    'enterprise_note' => 'Custom modules, WhatsApp, advanced automation, multi-branch and industry-specific functionality are available through Enterprise / custom development.',
];
