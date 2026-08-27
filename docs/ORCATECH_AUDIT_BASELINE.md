# OrcaTech CRM — Baseline Audit

## User requirements

The attached brief requires a single Laravel CRM application with Starter (default) and Business demo modes, centralized package gating, backend protection, visible locked Business features, Arabic/English localization with native RTL/LTR, realistic Nile Properties demo data, commercial SaaS UI polish, responsive layouts, and functional verification.

## Current implementation observed

The project is a large Laravel/Filament CRM inherited from `liberusoftware/crm-laravel`, with many CRM resources, Livewire components, modules, database migrations, and existing OrcaTech-specific files. It already contains a package catalog in `config/orcatech.php`, an `App\Support\OrcaTech\Feature` service that stores the selected package in session, an `OrcaTechFeatureGate` middleware, an upgrade page, a package switcher, OrcaTech dashboard widgets, English/Arabic translation catalogs, and a Filament panel provider that registers some locked navigation items.

The package catalog currently defines Starter at 9,999 EGP and Business at 17,999 EGP. Core, visual pipeline, and basic reports are Starter-level; advanced reports, workflow automation, data import, integrations, marketing, advertising, territories, and security are Business-level. Backend route protection currently derives a protected slug from Filament route names and redirects locked slugs to the upgrade page.

The localization catalogs cover OrcaTech labels, package copy, dashboard metrics, lead/deal statuses, tasks, activities, add-ons, login copy, and the demo banner in both English and Arabic. Arabic copy exists, but the overall application still needs a complete native RTL/LTR and visual QA pass.

## Baseline blockers

The sandbox does not currently have `php` or `composer`, so Laravel tests and Pint/PHPStan cannot run yet. The archived `node_modules` is broken: `npm run build` fails because the Vite binary points to a missing `node_modules/dist/node/cli.js`. Frontend dependencies must be restored with the project lockfile before build verification.

The current project has a very broad upstream-style Filament surface. The OrcaTech additions appear concentrated in package gating, dashboard widgets, upgrade/add-on pages, and translations; the requested commercial redesign still needs to be applied consistently across the actual CRM screens rather than only the demo banner and topbar.

## Initial priorities

1. Restore the local toolchain and get a reliable baseline build/test signal.
2. Audit the current gate coverage against all Business-only resources/pages and ensure direct URL protection is centralized and testable.
3. Establish a consistent OrcaTech design layer and dashboard/pipeline/list/detail experience without deleting working upstream CRM functionality.
4. Verify Starter/Business switching, Arabic/English direction handling, and responsive behavior.
