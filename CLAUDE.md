# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Escribe.io** — SaaS platform for creating personal blogs. Built with Laravel 12 + Vue 3 + Inertia.js + Tailwind CSS 4 + TypeScript (VILT stack).

Each user gets their own space: a personal landing page that showcases their blogs by topic, plus full blog management (create, edit, delete). The rich editor supports images, highlighted quotes, paragraph breaks, horizontal rules, and more.

Authentication (login, register, forgot password, 2FA) is fully implemented via Laravel Fortify. A dashboard is already available.

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

**Routing:** `routes/web.php` (home, dashboard) and `routes/settings.php` (profile, password, 2FA). Module routes are added in `routes/web.php` or extracted into their own file (e.g., `routes/blogs.php`). Routes use Laravel Wayfinder for type-safe URL generation on the frontend.

**Authentication:** Handled entirely by Laravel Fortify (`app/Providers/FortifyServiceProvider.php`). Custom auth actions live in `app/Actions/Fortify/`. Shared validation rules are extracted into traits in `app/Concerns/`.

**Inertia middleware** (`app/Http/Middleware/HandleInertiaRequests.php`) shares `auth.user` and `sidebarOpen` state with all Vue pages. **Appearance middleware** (`HandleAppearance.php`) injects the theme cookie into responses.

### Frontend (Vue 3 + TypeScript)

**Entry point:** `resources/js/app.ts` (client) and `resources/js/ssr.ts` (server). Root Blade template: `resources/views/app.blade.php`.

**Pages** live in `resources/js/pages/` — Inertia maps Laravel controller responses directly to these Vue SFC files. Module pages are grouped in subdirectories (e.g., `resources/js/pages/blogs/`).

**Components** in `resources/js/components/` follow a Shadcn/Reka UI pattern:
- `ui/` — primitive headless components (Button, Card, Input, Badge, Dialog, DropdownMenu, Sheet, Sidebar, etc.)
- `AppShell.vue`, `AppSidebar.vue`, `AppHeader.vue` — layout wrappers
- Feature components (e.g., `DeleteUser.vue`, `TwoFactor.vue`) colocated at the component root
- Always reuse existing components before creating new ones

**Path alias:** `@/*` maps to `resources/js/*` (configured in `tsconfig.json` and `vite.config.ts`).

### Database

SQLite by default (`database/database.sqlite`). Sessions, queues, and cache are all database-backed. Key migrations add 2FA columns to the users table.

### SSR

SSR is enabled and runs on `http://127.0.0.1:13714` (see `config/inertia.php`). Use `composer dev:ssr` or `npm run build:ssr` when working with SSR-sensitive features.

## Naming Conventions (MUST follow)

### PHP / Backend

| Element | Convention | Example |
|---|---|---|
| Classes, Controllers, Actions | PascalCase | `BlogController`, `CreateBlogAction` |
| Methods | camelCase | `store()`, `getPublishedBlogs()` |
| Variables & properties | snake_case | `$blog_title`, `$user_id` |
| Database columns | snake_case | `is_published`, `cover_image_path` |
| Route names | dot.notation | `blogs.index`, `blogs.store` |
| Form Requests | PascalCase + `Request` suffix | `StoreBlogRequest` |

### Actions Pattern

Business logic is **never placed directly in controllers**. Extract it into `app/Actions/{Module}/` classes.

```php
// app/Actions/Blogs/CreateBlogAction.php
final class CreateBlogAction
{
    public function execute(User $user, array $data): Blog
    {
        // logic here
    }
}

// Called from controller like:
$blog = (new CreateBlogAction)->execute(user: $user, data: $validated);
```

Actions are `final class`, have a single public `execute()` method, and are called with named arguments.

### Frontend / Vue

- Components: PascalCase filenames, `<script setup>` Composition API only — no Options API
- Props/emits: camelCase
- Variables in `<script setup>`: camelCase
- Tailwind classes sorted automatically by Prettier

## Styling & Theming

All modules **must support dark and light mode**. Use only the CSS variables already configured — never hardcode colors:

```
bg-background / text-foreground        (page base)
bg-card / text-card-foreground         (cards)
bg-muted / text-muted-foreground       (subtle sections)
bg-primary / text-primary-foreground   (CTA, active states)
bg-secondary / text-secondary-foreground
text-destructive                        (errors, delete)
border-border                          (borders)
bg-accent / text-accent-foreground     (hover, selected)
```

Use `dark:` variant via the custom `@custom-variant dark (&:is(.dark *))` already configured.

## Project Modules

### Already Implemented
- **Auth** — login, register, forgot password, reset password, email verification, 2FA
- **Dashboard** — authenticated landing page
- **Settings** — profile, password, appearance, 2FA management

### To Be Implemented
- **Blogs** — CRUD for blogs with rich content editor (images, quotes, paragraphs, hr, etc.)
- **Personal Landing Page** — public profile page listing a user's blogs by topic (`/@username`)
- **Topics/Categories** — organize blogs by topic
- **Media** — image uploads for blog covers and inline content

## Key File Locations

```
app/
  Actions/              # Business logic actions (grouped by module)
    Fortify/            # Auth actions
    Blogs/              # Blog actions (to be created)
  Http/
    Controllers/
      Settings/         # Settings controllers (Profile, Password, TwoFactor)
    Requests/
      Settings/         # Form requests for settings
  Models/               # Eloquent models

resources/js/
  pages/
    auth/               # Auth pages (Login, Register, etc.)
    settings/           # Settings pages
    Dashboard.vue       # Dashboard
    Welcome.vue         # Public home
  components/
    ui/                 # Primitive UI components (reuse always)
    AppShell.vue        # Main layout wrapper
    AppSidebar.vue      # Sidebar navigation

routes/
  web.php               # Main routes
  settings.php          # Settings routes
```
