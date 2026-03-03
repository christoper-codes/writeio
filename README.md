# Write.io

A SaaS platform for creating and sharing personal blogs. Each user gets their own space: a public landing page showcasing their blogs organized by topic, plus a full-featured editor to write and publish content.

**Stack:** Laravel 12 · Vue 3 · Inertia.js · Tailwind CSS 4 · TypeScript (VILT)

---

## Features

- Personal landing page (`/@username`) listing all your blogs by topic
- Rich blog editor — images, highlighted quotes, paragraph breaks, horizontal rules, and more
- Full authentication — login, register, forgot/reset password, email verification, 2FA
- Dark / light mode support across all modules
- Responsive, modern design

---

## Modules

### Auth (implemented)
- Login, Register, Forgot Password, Reset Password
- Email Verification
- Two-Factor Authentication (2FA) via authenticator app

### Dashboard (implemented)
- Authenticated home screen after login
- Quick access to blogs and stats

### Settings (implemented)
- Profile — name, email, avatar
- Password — change password
- Appearance — light/dark/system theme
- Two-Factor — enable/disable 2FA, view recovery codes

### Blogs (in progress)
- List all your blogs (index)
- Create a new blog with rich content editor
- Edit / update an existing blog
- Delete a blog
- Publish / unpublish toggle
- Cover image upload
- Inline images within content
- Highlighted quote blocks
- Paragraph formatting, horizontal rules, headings

### Topics / Categories (planned)
- Create and manage topics
- Assign blogs to one or more topics
- Filter blogs by topic on the personal landing page

### Personal Landing Page (planned)
- Public profile at `/@username`
- Bio, avatar, and social links
- Blog list organized by topic
- SEO-friendly metadata per page

### Media (planned)
- Image uploads for blog covers and inline content
- Stored in `storage/app/public/`
- Served via Laravel's file storage

---

## Getting Started

### Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- SQLite (configured by default)

### Installation

```bash
# Clone the repository
git clone <repo-url>
cd escribeio

# Install all dependencies, run migrations, and build assets
composer setup

# Start the development server
composer dev
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

### Development Commands

```bash
composer dev         # PHP server + queue worker + Vite HMR (all at once)
composer dev:ssr     # Same, but includes the SSR server
npm run dev          # Vite HMR only (frontend)
```

### Building for Production

```bash
npm run build        # Frontend assets
npm run build:ssr    # Frontend assets + SSR bundle
```

### Testing

```bash
composer test                                    # All tests
./vendor/bin/pest --filter="test name"           # Single test
```

### Linting & Formatting

```bash
composer lint        # Auto-fix PHP style (Pint)
npm run lint         # ESLint auto-fix
npm run format       # Prettier auto-format
```

---

## Project Structure

```
app/
├── Actions/               # Business logic (one action per operation)
│   ├── Fortify/           # Auth-related actions
│   └── Blogs/             # Blog CRUD actions
├── Http/
│   ├── Controllers/
│   │   └── Settings/      # Profile, Password, TwoFactor controllers
│   └── Requests/
│       └── Settings/      # Form validation requests
├── Models/                # Eloquent models
└── Providers/

resources/js/
├── pages/
│   ├── auth/              # Login, Register, etc.
│   ├── settings/          # Profile, Password, Appearance, 2FA
│   ├── blogs/             # Blog CRUD pages (to be created)
│   ├── Dashboard.vue
│   └── Welcome.vue
├── components/
│   ├── ui/                # Primitive UI components (Button, Card, Input…)
│   ├── AppShell.vue       # Main layout
│   └── AppSidebar.vue     # Sidebar navigation

routes/
├── web.php                # Main routes
└── settings.php           # Settings routes
```

---

## Code Conventions

- **Controllers / Models / Actions:** PascalCase
- **Methods:** camelCase
- **Variables & DB columns:** snake_case
- **Actions:** `final class`, single `execute()` method, called with named arguments
- **Frontend:** Vue 3 `<script setup>` Composition API only — no Options API
- **Styling:** Use CSS variables (`bg-background`, `text-foreground`, etc.) — never hardcode colors

---

## License

MIT
