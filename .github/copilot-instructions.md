<!-- .github/copilot-instructions.md — project-specific guidance for AI coding agents -->
# Student System — Copilot Instructions

Purpose: Help AI coding agents be productive quickly in this Laravel-based repo.

1) Big picture
- This is a Laravel 11 application (PHP ^8.2) with a small CRUD surface for students.
- Key runtime pieces:
  - Backend: `app/` (Models, Http/Controllers, Providers)
  - Routes: `routes/web.php` (HTTP endpoints, maps to controllers)
  - Views: `resources/views/` (Blade templates)
  - Frontend tooling: Vite + Tailwind (`package.json`, `vite.config.js`, `resources/js`, `resources/css`)
  - DB migrations & seeds: `database/migrations/`, `database/seeders/`, `database/factories/`

2) Where to look for canonical examples
- Routing & controllers: `routes/web.php` and `app/Http/Controllers/StudentController.php` (CRUD handlers).
- Domain model example: `app/Models/Student.php` — non-standard Eloquent config (see "Model quirks").
- Views: `resources/views/students/` (student listing/forms) and the app root `resources/views/welcome.blade.php`.
- Tests bootstrapping: `phpunit.xml` and `tests/` — use these when running or adding tests.

3) Project-specific conventions & gotchas (important for automated edits)
- Student model deviates from Laravel defaults:
  - `protected $table = 'student'` (singular table name)
  - `protected $primaryKey = 'sid'` (primary key is `sid`, not `id`)
  - `public $timestamps = false` (no automatic timestamps)
  - `fillable` fields: `fname`, `lname`, `birthplace`, `birthDate`
  => Any database/ORM operations must reference these names (and `sid` as the key).

- Routes: `routes/web.php` registers CRUD endpoints with `sid` as the route parameter. Example:
  - `Route::put('/students/{sid}', [StudentController::class, 'update']);`
  => When generating controller/tests, use `sid` not `id`.

- Duplicate root route: `routes/web.php` contains two `Route::get('/', ...)` entries (a closure and a controller mapping). The later route will override the earlier; prefer the controller mapping for changes.

4) Build / run / test commands (how developers actually run the app)
- Install PHP deps: `composer install`
- Install JS deps: `npm install`
- Local dev (both backend and frontend):
  - `composer run dev` — project `composer` script runs `php artisan serve`, queue, pail, and `npm run dev` concurrently (requires `npx concurrently` available).
  - Or run pieces individually:
    - `php artisan serve` — start backend server
    - `npm run dev` — start Vite dev server

- Database / migrations:
  - `php artisan migrate` (or `php artisan migrate --graceful` as used in composer hooks)
  - For a fresh DB during development/tests: `php artisan migrate:fresh --seed`

- Tests & formatting:
  - Run tests: `./vendor/bin/phpunit` (on Windows PowerShell use `vendor\\bin\\phpunit.bat`)
  - Code style: `./vendor/bin/pint` (or `vendor\\bin\\pint.bat` on Windows)

5) Frontend notes
- Vite + Tailwind are configured — `package.json` scripts:
  - `npm run dev` (vite)
  - `npm run build` (production build)
- Blade views check for `public/build/manifest.json` and use `@vite([...])` when present.

6) Tests & CI cues
- `phpunit.xml` sets testing environment variables (uses `array`/`sync` drivers, DB lines commented out). When adding CI tests, ensure DB configuration matches the runner (SQLite memory is an available option — uncomment the `DB_CONNECTION` and `DB_DATABASE` lines when appropriate).

7) Integration points & external dependencies
- The app expects `.env` for DB and app config (copy `.env.example` if missing). Composer scripts create `database/database.sqlite` if not present.
- Third-party packages are managed via Composer (backend) and npm (frontend). Useful vendor binaries are available under `vendor/bin/` (Windows `.bat` wrappers exist in the repo's vendor/bin folder).

8) Editing guidance for AI agents
- Small, focused changes only — match existing naming (e.g., `sid`, `student` table). Avoid renaming primary key or table without updating routes/controllers/tests/migrations together.
- When adding model assertions or factories, use `database/factories/` and `database/seeders/` conventions.
- Prefer altering `app/Http/Controllers/StudentController.php` for HTTP behavior; update `routes/web.php` only when adding endpoints.

9) Useful file references (quick links for tooling)
- `app/Models/Student.php` — model quirks and fillable fields
- `routes/web.php` — route defs and `sid` usage
- `app/Http/Controllers/StudentController.php` — main controller (CRUD)
- `database/migrations/` and `database/seeders/` — schema and seeds
- `phpunit.xml`, `tests/` — test configuration and existing tests
- `composer.json`, `package.json` — build/dev scripts

If anything above is unclear or you want a section expanded (example tests, controller templates, or suggested PR checklist for changes), tell me which part to iterate on and I will update this file.
