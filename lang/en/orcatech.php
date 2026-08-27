<?php

declare(strict_types=1);

return [

    'brand' => [
        'name' => 'OrcaTech CRM',
        'company' => 'OrcaTech',
    ],

    'currency' => 'EGP',

    'crm' => [
        'lead' => ['singular' => 'Lead', 'plural' => 'Leads'],
        'contact' => ['singular' => 'Contact', 'plural' => 'Contacts'],
        'company' => ['singular' => 'Company', 'plural' => 'Companies'],
        'deal' => ['singular' => 'Deal', 'plural' => 'Deals'],
        'opportunity' => ['singular' => 'Opportunity', 'plural' => 'Opportunities'],
        'task' => ['singular' => 'Task', 'plural' => 'Tasks'],
        'note' => ['singular' => 'Note', 'plural' => 'Notes'],
    ],

    'packages' => [
        'starter' => [
            'name' => 'Starter',
            'tagline' => 'Ready CRM, 9,999 EGP',
        ],
        'business' => [
            'name' => 'Business',
            'tagline' => 'Customized to your process, 17,999 EGP',
        ],
    ],

    'language' => [
        'change' => 'Change language',
    ],

    'topbar' => [
        'controls' => 'Workspace controls',
    ],

    'switcher' => [
        'title' => 'Demo Package',
        'demo' => 'Demo',
        'hint' => 'Switching keeps the same data and session.',
        'activated_business' => 'Business demo activated — all advanced features are unlocked.',
        'activated_starter' => 'Starter demo activated — Business features are locked again.',
    ],

    'features' => [
        'crm_core' => [
            'name' => 'CRM Core',
            'description' => 'Leads, contacts, companies, deals, tasks and activities in one place.',
        ],
        'visual_pipeline' => [
            'name' => 'Visual Sales Pipeline',
            'description' => 'Drag deals through your pipeline stages and keep momentum on every opportunity.',
        ],
        'basic_reports' => [
            'name' => 'Core Reports & Dashboard',
            'description' => 'Essential sales metrics, pipeline value and conversion insights.',
        ],
        'advanced_reports' => [
            'name' => 'Advanced Reports',
            'description' => 'Get deeper insights into your sales performance with advanced reporting and analytics: custom report builder, lead quality scoring and campaign performance.',
        ],
        'workflow_automation' => [
            'name' => 'Workflow Automation',
            'description' => 'Automate repetitive work with rules that assign leads, send follow-ups and move deals forward automatically.',
        ],
        'data_import' => [
            'name' => 'Data Import',
            'description' => 'Import your existing leads, contacts and companies from spreadsheets in minutes.',
        ],
        'integrations' => [
            'name' => 'Integrations',
            'description' => 'Connect telephony, WhatsApp numbers, e-mail marketing and more with simple, guided integrations.',
        ],
        'marketing_suite' => [
            'name' => 'Marketing Suite',
            'description' => 'Campaigns, e-mail templates, landing pages, web forms and social publishing built into your CRM.',
        ],
        'advertising' => [
            'name' => 'Advertising Analytics',
            'description' => 'Track Google, Facebook, LinkedIn and Instagram ad accounts next to your sales data.',
        ],
        'territories' => [
            'name' => 'Sales Territories',
            'description' => 'Organize reps by region and route the right leads to the right team automatically.',
        ],
        'security_suite' => [
            'name' => 'Security & SSO Suite',
            'description' => 'Single sign-on (SAML / OIDC), OAuth applications and webhook delivery management.',
        ],
    ],

    'locked' => [
        'badge' => 'Locked',
        'available_in_business' => 'Available in Business',
    ],

    'leads' => [
        'statuses' => [
            'new' => 'New',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'lost' => 'Lost',
        ],
        'sources' => [
            'website' => 'Website',
            'referral' => 'Referral',
            'social_media' => 'Social media',
            'direct' => 'Direct',
            'other' => 'Other',
        ],
    ],

    'deals' => [
        'stages' => [
            'prospect' => 'Prospecting',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'won' => 'Won',
            'lost' => 'Lost',
            'closed' => 'Closed',
        ],
    ],

    'upgrade' => [
        'title' => 'Upgrade preview',
        'eyebrow' => 'Business feature',
        'default_feature' => 'This feature',
        'default_description' => 'Unlock this capability by switching the demo to the Business package.',
        'available_in' => 'Available in',
        'cta_button' => 'View Business Demo',
        'cta' => 'View :package Demo — :price :currency',
        'cta_hint' => 'No payment needed — this demo instantly switches packages.',
        'benefits_label' => 'A clearer view of your business',
        'benefits_title' => 'Everything your team needs to move faster',
        'ready_title' => 'See the full Business experience',
        'ready_description' => 'Switch the demo in one click and explore advanced reporting, automation, integrations and more.',
        'unlocked_message' => 'Business demo activated — all advanced features are unlocked.',
    ],

    'dashboard' => [
        'title' => 'CRM Dashboard',
        'total_leads' => 'Total Leads',
        'new_leads' => 'New Leads',
        'qualified_leads' => 'Qualified Leads',
        'active_deals' => 'Active Deals',
        'won_deals' => 'Won Deals',
        'lost_deals' => 'Lost Deals',
        'pipeline_value' => 'Pipeline Value',
        'conversion_rate' => 'Conversion Rate',
        'open_tasks' => 'Open Tasks',
        'overdue_tasks' => 'Overdue Tasks',
        'recent_activities' => 'Recent Activities',
        'upcoming_tasks' => 'Upcoming Tasks',
        'advanced_analytics' => 'Advanced Sales Analytics',
        'analytics_locked' => 'Advanced Sales Analytics is part of the Business package.',
        'view_all' => 'View all',
        'no_activities' => 'No activities yet.',
        'no_tasks' => 'Nothing due soon.',
        'business_insights' => 'Business insights unlocked',
        'stage_breakdown' => 'Deals per stage',
        'lead_sources' => 'Leads per source',
        'win_rate' => 'Win rate',
        'avg_deal_size' => 'Avg. deal size',
        'status' => 'Status',
        'assigned_to' => 'Assigned to',
        'due_date' => 'Due date',
        'date' => 'Date',
        'activity_type' => 'Type',
    ],

    'tasks' => [
        'statuses' => [
            'pending' => 'Pending',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
        ],
    ],

    'activities' => [
        'types' => [
            'call' => 'Call',
            'meeting' => 'Meeting',
            'email' => 'Email',
            'note' => 'Note',
            'created' => 'Created',
            'updated' => 'Updated',
        ],
    ],

    'addons_page' => [
        'title' => 'Add-ons',
        'subtitle' => 'Extend OrcaTech CRM when you need more.',
        'available_as_addon' => 'Available as an Add-on',
        'addon_note' => 'Add-ons are quoted separately and configured by the OrcaTech team during onboarding.',
        'enterprise_note' => 'Need something bespoke? Enterprise customers get custom modules, WhatsApp automation, multi-branch support and industry-specific functionality.',
    ],

    'addons' => [
        'whatsapp_integration' => [
            'name' => 'WhatsApp Integration',
            'description' => 'Chat with leads and customers over WhatsApp Business directly from the CRM timeline.',
        ],
        'custom_module' => [
            'name' => 'Custom Module',
            'description' => 'A module tailored to your exact business process, built and maintained by OrcaTech.',
        ],
        'ai_assistant' => [
            'name' => 'AI Assistant',
            'description' => 'Summaries, suggested replies and next-best-action hints powered by AI.',
        ],
        'payment_integration' => [
            'name' => 'Payment Integration',
            'description' => 'Collect payments and reconcile them against deals automatically.',
        ],
        'api_integration' => [
            'name' => 'API Integration',
            'description' => 'Connect OrcaTech CRM with your ERP, website or internal systems.',
        ],
        'custom_reports' => [
            'name' => 'Custom Reports',
            'description' => 'Reports designed around your KPIs, delivered on a schedule you choose.',
        ],
        'mobile_app' => [
            'name' => 'Mobile App',
            'description' => 'A branded mobile app for your sales team on iOS and Android.',
        ],
    ],

    'demo_banner' => [
        'welcome_starter' => 'Welcome to the OrcaTech CRM Starter demo. Explore the full CRM — :locked Business-only features are marked 🔒 and can be previewed any time.',
        'welcome_business' => 'You are viewing the Business demo — all advanced capabilities are unlocked.',
    ],

    'login' => [
        'heading' => 'Sign in to OrcaTech CRM',
        'subheading' => 'OrcaTech CRM demo environment',
        'credentials_title' => 'Demo credentials',
        'email_label' => 'E-mail',
        'password_label' => 'Password',
    ],
];
