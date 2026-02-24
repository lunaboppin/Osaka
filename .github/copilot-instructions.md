# Copilot Instructions for Osaka

## Architecture & Conventions

- **Framework:** Laravel with Blade templates, Tailwind CSS, and Alpine.js
- **Auth:** OAuth via Authentik/Socialite. No passwords — users authenticate externally
- **Permissions:** Role-based via `HasRoles` trait. Roles have a JSON `permissions` array. Check with `$user->hasPermission('scope.action')`
- **UI patterns:** `<x-app-layout>` wrapper, `card`/`card-body` containers, `btn-primary`/`btn-secondary`/`btn-danger` buttons, `form-input-osaka` inputs, custom color tokens (`osaka-cream`, `osaka-charcoal`, `osaka-red`, `osaka-gold`)
- **Database:** Anonymous migration classes with `$table->id()`, `$table->timestamps()`, `foreignId()->constrained()`. Use `Schema::hasColumn` guards for idempotency where needed
- **Models:** Use `$fillable`, not `$guarded`. Apply `use Auditable` trait on all models

## Feature Development Reminders

### Permissions

- When creating a new feature that could have granular role permissions, **always add the permission** to `Role::availablePermissions()` in `app/Models/Role.php`
- Assign the new permission to the appropriate default roles in `database/seeders/RoleSeeder.php` (admin has wildcard `*`, so only moderator/member need explicit additions)
- Gate routes with `->middleware('permission:scope.action')` and check in Blade with `Auth::user()->hasPermission('scope.action')`
- Prefer granular permissions (`feature.create`, `feature.edit`, `feature.delete`) over broad ones — allow for fine-grained role control

### Audit Logging

- **Every state-changing action must be logged in the audit log**
- Models using `use Auditable` (in `app/Traits/Auditable.php`) automatically log `created`, `updated`, and `deleted` events with old/new value diffs
- For actions not captured by model events (login, logout, bulk operations, pivot table changes), call `AuditLog::log()` manually in the controller
- Pivot table changes (e.g. role assignments) are logged explicitly in `HasRoles` trait methods — follow the same pattern for any new pivot relationships
- Sensitive attributes can be excluded from audit diffs by setting `protected array $auditExclude = [...]` on the model

### Admin Section

- Admin routes live under `Route::prefix('admin')->middleware('permission:admin.access')` in `routes/web.php`
- Admin views go in `resources/views/admin/{feature}/`
- Add navigation links to `resources/views/layouts/navigation.blade.php` in both the desktop dropdown and mobile menu, gated by the appropriate permission
- Follow the existing card-based layout pattern (see `admin/roles/index.blade.php` for reference)

### General

- Validate inline with `$request->validate()` in controllers (only use Form Request classes for complex validation)
- Flash messages use `session('success')` and `session('error')` — display with the standard Alpine.js dismissible banner pattern
- Use `Str::plural()` for count labels
- **Do NOT run any terminal commands** (e.g. `php artisan migrate`, `php artisan db:seed`, `npm`, `composer`). The app is not hosted locally — commands will fail. Only make file changes.
