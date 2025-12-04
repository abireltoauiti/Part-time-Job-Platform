## Purpose
Short, actionable guidance to help AI coding agents be productive in this Symfony project.

## Big picture (what this app is)
- **Type:** Symfony 6 PHP web app using Doctrine ORM and Twig templates.
- **Auth & roles:** Custom login/registration flow in `src/Security/LoginFormAuthenticator.php`, controllers `src/Controller/RegisterController.php` and `src/Controller/SecurityController.php`. Dashboard views under `templates/dashboard/` are role-scoped (`admin`, `candidat`, `entreprise`).
- **Domain:** Job platform with entities `src/Entity/{User,Job,Candidature,CategorieJob}.php`. Business logic lives mostly in controllers and repositories in `src/Repository/`.

## Key directories and files (read these first)
- `src/Controller/` — primary HTTP endpoints and route handlers (e.g., `DashboardController.php`).
- `src/Security/LoginFormAuthenticator.php` — authentication flow and guards.
- `src/Form/RegistrationFormType.php` — example of form building and validation.
- `src/Entity/` and `src/Repository/` — domain models and query logic.
- `templates/` — Twig templates; follow existing folder structure (e.g., `templates/register/index.html.twig`).
- `config/packages/` and `config/routes/` — service config and routing. `config/services.yaml` is where custom services are wired.
- `migrations/` — Doctrine migration files (VersionYYYYMMDD...php).
- `assets/` — front-end entry (`app.js`), Stimulus controllers under `assets/controllers/` (importmap-based setup).

## Project-specific conventions and patterns
- Controllers map to template paths by convention; follow existing naming and file locations when adding views.
- Repositories contain query logic. Avoid placing complex DB queries in controllers — add repository methods instead (see `JobRepository.php`).
- Forms use Symfony Form Types (see `RegistrationFormType.php`) and validation constraints configured in entities.
- Migrations follow Doctrine's `Version<timestamp>.php` naming — use `doctrine:migrations:diff` and `doctrine:migrations:migrate` to create/apply.

## How to run common workflows on Windows (project uses XAMPP PHP)
- Run Symfony console commands with the XAMPP PHP binary used in this environment: e.g.
  - `C:\\xampp\\php\\php.exe bin/console doctrine:migrations:status`
  - `C:\\xampp\\php\\php.exe bin/console doctrine:migrations:migrate` (use with care)
- Run tests (project includes `bin/phpunit`):
  - `C:\\xampp\\php\\php.exe bin/phpunit` or `bin/phpunit` from PowerShell if PHP is on PATH.
- Start a local webserver if you don't use Symfony CLI: `C:\\xampp\\php\\php.exe -S 127.0.0.1:8000 -t public` (use XAMPP/Apache for production-like environment).

## Examples of common edits (concrete pointers)
- To add a new page + route:
  1. Create `src/Controller/NewController.php` with a public action returning `render('new/template.html.twig')`.
  2. Add a Twig file under `templates/new/template.html.twig`.
  3. Add route in `config/routes.yaml` or `config/routes/your_routes.yaml` following existing structure.
- To add a DB field:
  1. Update the Entity class in `src/Entity/` and add migration: `C:\\xampp\\php\\php.exe bin/console doctrine:migrations:diff` then `...migrate`.
- To modify authentication/roles:
  - Inspect `src/Security/LoginFormAuthenticator.php`, `config/packages/security.yaml`, and the controller usage in `src/Controller/SecurityController.php`.

## Integration points & external dependencies
- Doctrine ORM (DB config in `.env` and `config/packages/doctrine.yaml`).
- Symfony Security (see `config/packages/security.yaml`).
- Stimulus + importmap in `assets/` (Hotwire controllers under `assets/controllers/` and `assets/vendor/@hotwired`).
- Mailer / Messenger settings exist in config; inspect `config/packages/mailer.yaml` / `messenger.yaml` when touching async/email logic.

## What agents should do and avoid
- Do: Read the referenced files above before making changes. Use repository conventions for file placement and naming.
- Do: Use repository `bin/console` and `bin/phpunit` tooling with the XAMPP PHP binary when running commands locally on Windows.
- Avoid: Making sweeping changes to `config/packages/*` without tests — config is environment-sensitive and can break runtime boot.

## Quick checklist when editing code
- Update or add tests reachable by `bin/phpunit` for behavior changes.
- If DB schema changed, add a Doctrine migration in `migrations/` and verify `doctrine:migrations:status`.
- Update Twig templates under `templates/` and keep namespaced paths consistent with controllers.

---
If anything here is unclear or you'd like more examples (e.g., controller scaffolding, typical repository patterns), tell me which area to expand and I will iterate.
