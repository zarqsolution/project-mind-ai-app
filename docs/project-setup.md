# Project Setup

This document records how the application was scaffolded, for AI assistant reference.

## Installation

Created via the Laravel installer:

```bash
laravel new project-mind-ai-app
```

### Installer Choices

| Prompt | Choice |
| --- | --- |
| Use a starter kit? | Yes |
| Frontend stack | React |
| Authentication provider | Laravel's built-in authentication |
| Teams support | No |
| Authentication features enabled | None |

### Setup Steps Performed by Installer

- Application installed and initialized (React starter kit)
- `composer lint` run
- Wayfinder resources generated: `php artisan wayfinder:generate --with-form --no-interaction`
- Database migrations run
- Pest installed and initialized (testing framework)
- Frontend dependencies installed with npm, assets built
- Laravel Boost installed and initialized (AI-assisted coding MCP server)

## Stack Summary

- Backend: Laravel 13 (PHP 8.5), Fortify (built-in auth), Wayfinder
- Frontend: React 19 + Inertia v3, Tailwind CSS v4, Vite
- Testing: Pest v5 / PHPUnit v13
- Tooling: Laravel Boost (MCP), Pint (formatting), Larastan, ESLint v9, Prettier v3, Pail, Sail

## Notes

- No teams support was installed (an initial attempt with teams was aborted and the directory recreated without it).
- No optional authentication features (e.g., email verification, 2FA) were enabled at install time.
