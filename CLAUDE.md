# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 12 + Vue 3 SaaS starter with full authentication (including 2FA) via Laravel Fortify, Inertia.js for server-driven Vue pages, Tailwind CSS 4, and TypeScript.

## Commands

### Development

```bash
composer setup          # Initial setup: install deps, migrate, build assets
composer dev            # Concurrent dev: PHP server + queue + Vite HMR
composer dev:ssr        # Same, but with SSR server
npm run dev             # Frontend only (Vite HMR)
```

### Building

```bash
npm run build           # Production frontend build
npm run build:ssr       # Production SSR build
```

### Testing

```bash
composer test           # Run all Pest PHP tests
./vendor/bin/pest       # Run Pest directly
./vendor/bin/pest --filter="test name"   # Run a single test
```

### Linting & Formatting

```bash
composer lint           # Auto-fix PHP code style (Pint)
composer test:lint      # Check PHP style without fixing
npm run lint            # ESLint auto-fix
npm run format          # Prettier auto-format
npm run format:check    # Check formatting without changes
```

## Architecture

### Backend (Laravel 12)

**Routing:** `routes/web.php` (home, dashboard) and `routes/settings.php` (profile, password, 2FA). Routes use Laravel Wayfinder for type-safe URL generation on the frontend.

**Authentication:** Handled entirely by Laravel Fortify (`app/Providers/FortifyServiceProvider.php`). Custom auth actions live in `app/Actions/Fortify/` (e.g., `CreateNewUser.php`). Shared validation rules are extracted into traits in `app/Concerns/`.

**Inertia middleware** (`app/Http/Middleware/HandleInertiaRequests.php`) shares `auth.user` and `sidebarOpen` state with all Vue pages. **Appearance middleware** (`HandleAppearance.php`) injects the theme cookie into responses.

### Frontend (Vue 3 + TypeScript)

**Entry point:** `resources/js/app.ts` (client) and `resources/js/ssr.ts` (server). Root Blade template: `resources/views/app.blade.php`.

**Pages** live in `resources/js/pages/` — Inertia maps Laravel controller responses directly to these Vue SFC files.

**Components** in `resources/js/components/` follow a Shadcn/Reka UI pattern:
- `ui/` — primitive headless components (Button, Card, Input, etc.)
- `AppShell.vue`, `AppSidebar.vue` — layout wrappers
- Feature components (e.g., `DeleteUser.vue`, `TwoFactor.vue`) colocated at the component root

**Path alias:** `@/*` maps to `resources/js/*` (configured in `tsconfig.json` and `vite.config.ts`).

### Database

SQLite by default (`database/database.sqlite`). Sessions, queues, and cache are all database-backed. Key migrations add 2FA columns (`two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`) to the users table.

### SSR

SSR is enabled and runs on `http://127.0.0.1:13714` (see `config/inertia.php`). Use `composer dev:ssr` or `npm run build:ssr` when working with SSR-sensitive features.

## Key Conventions

- **Form requests** for settings validation live in `app/Http/Requests/Settings/`.
- **Settings controllers** are namespaced under `App\Http\Controllers\Settings\` and each handles a single concern (Profile, Password, TwoFactor).
- Frontend uses Vue 3 Composition API (`<script setup>`) throughout — no Options API.
- Tailwind classes are sorted automatically by Prettier (`prettier-plugin-tailwindcss`).
