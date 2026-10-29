# Product Management — Laravel Admin Dashboard & REST API

A product management system built for the Laravel Developer Assessment Test. It provides an
admin dashboard for managing products and categories, and a Sanctum-authenticated RESTful API
for programmatic access, with Swagger/OpenAPI documentation and Excel export.

## Features

**Admin Dashboard** (`/admin`)
- Product CRUD (name, category, description, price, stock, enabled)
- Product list with search, filtering by category/status, and pagination
- Soft delete for products, plus bulk delete (multi-select)
- **Trash**: restore soft-deleted products or purge them forever
- Category CRUD (deletion is blocked while the category still has products)
- Excel export of products (`.xlsx`) that respects the active filters
- Session authentication with a dedicated `admin` guard — only `is_admin` users can log in
- Login is rate limited (5 attempts / minute / IP)

**RESTful API** (`/api`)
- Token auth via Laravel Sanctum (`POST /api/auth/login`) — **admin accounts only** (403 otherwise)
- Every authenticated route re-checks `is_admin` via middleware, not just token issuance
- Login is rate limited (5 attempts / minute / IP)
- Product create / list / detail / update / delete
- List filtering by `category_id` and `status`, with pagination metadata
- Bulk delete (`POST /api/products/bulk-delete`)
- Excel export (`GET /api/products/export`)
- Category listing used by the product forms
- JSON responses include `category_id` **and** nested `category` (`id`, `name`)

**Advanced**
- Validation through Laravel Form Requests (`StoreProductRequest`, `UpdateProductRequest`, `StoreCategoryRequest`)
- PHPUnit unit + feature tests (46 tests, 200+ assertions)
- OpenAPI/Swagger docs generated with `darkaonline/l5-swagger` (attributes on controllers)
- Excel export via **SpartnerNL/Laravel-Excel** (`maatwebsite/excel` package)

## Tech Stack

- Laravel 13 (PHP 8.3+)
- MySQL (production/local; tests run on in-memory SQLite)
- Laravel Sanctum (API tokens)
- maatwebsite/excel (SpartnerNL/Laravel-Excel)
- l5-swagger / swagger-php (OpenAPI 3 documentation)
- PHPUnit 12

## Setup Instructions

```bash
# 1. Install PHP dependencies
composer install

# 2. Create your environment file (MySQL is the default connection)
cp .env.example .env
php artisan key:generate

# 3. Create the MySQL database (default name from .env)
mysql -u root -e "CREATE DATABASE product_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Run migrations and seed the default admin user
php artisan migrate --seed

# 5. (Optional) Build front-end assets
npm install
npm run build

# 6. Start the application
php artisan serve
```

The seeder creates the default admin account:

| Field    | Value              |
|----------|--------------------|
| Email    | `admin@example.com` |
| Password | `password`          |

- Admin dashboard: `http://localhost:8000/admin/login`
- Swagger UI: `http://localhost:8000/api/documentation`

Regenerate the OpenAPI spec after changing annotations:

```bash
php artisan l5-swagger:generate
```

## Docker (bonus)

The repository ships with Docker support — PHP 8.4-FPM, Nginx, and MySQL 8:

```bash
docker compose up --build -d
# app runs migrations + seeds admin on first boot
open http://localhost:8080
```

- `docker/entrypoint.sh` copies `.env` if missing, generates `APP_KEY`, fixes storage
  permissions, and runs `php artisan migrate --seed` on every start (idempotent).
- MySQL is only reachable from inside the compose network; nothing is published to the
  host.
- Rebuild after changing `composer.json`: `docker compose up --build`.

## Running Tests

```bash
php artisan test          # or: php vendor/bin/phpunit
```

Tests use `sqlite :memory:` (set in `phpunit.xml` with `APP_ENV=testing`), so MySQL is not
required to run them.

## API Endpoints Documentation

All `/api` endpoints except login require a `Bearer` token obtained from
`POST /api/auth/login`.

### Authentication

| Method | Endpoint             | Description                    | Auth  |
|--------|----------------------|--------------------------------|-------|
| POST   | `/api/auth/login`    | Get a Sanctum token            | No    |
| GET    | `/api/auth/user`     | Current authenticated user     | Token |
| POST   | `/api/auth/logout`   | Revoke the current token       | Token |

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Response: `{ "message": "...", "token": "...", "user": {...} }`

### Products

| Method | Endpoint                        | Description                                   | Query params            |
|--------|---------------------------------|-----------------------------------------------|-------------------------|
| GET    | `/api/products`                 | List products (paginated, 15 per page)        | `category_id`, `status` |
| GET    | `/api/products/{product}`       | Product detail                                | —                       |
| POST   | `/api/products`                 | Create a product                              | —                       |
| PUT    | `/api/products/{product}`       | Update a product                              | —                       |
| DELETE | `/api/products/{product}`       | Soft delete a product                         | —                       |
| POST   | `/api/products/bulk-delete`     | Bulk delete products (`{"ids": [1, 2, 3]}`)   | —                       |
| GET    | `/api/products/export`          | Download products as `.xlsx`                  | `category_id`, `status` |

Product payload:

```json
{
  "name": "Wireless Mouse",
  "category_id": 1,
  "description": "A nice mouse",
  "price": 29.99,
  "stock": 100,
  "enabled": true
}
```

Product JSON response (category id + name are included):

```json
{
  "data": {
    "id": 1,
    "name": "Wireless Mouse",
    "category_id": 1,
    "category": { "id": 1, "name": "Electronics" },
    "description": "A nice mouse",
    "price": "29.99",
    "stock": 100,
    "enabled": true
  },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 }
}
```

### Categories

| Method | Endpoint          | Description       | Auth  |
|--------|-------------------|-------------------|-------|
| GET    | `/api/categories` | List all categories | Token |

Interactive documentation (Swagger UI) is served at `/api/documentation`; the generated
OpenAPI JSON lives in `storage/api-docs/api-docs.json`.

## Assumptions & Design Choices

- **Latest stable Laravel** was used (Laravel 13, PHP 8.3+). Because Laravel 13 uses the
  streamlined `bootstrap/app.php` structure, there is no `app/Http/Kernel.php`; middleware is
  configured centrally in `bootstrap/app.php`.
- **Two interfaces, one model layer.** The admin dashboard uses session auth (the `admin`
  guard + `is_admin` flag + `auth:admin` middleware), while the API uses stateless Sanctum
  tokens (`auth:sanctum`). Only authenticated admins receive tokens.
- **Soft deletes** are used for products (`SoftDeletes` trait) so deletions are recoverable;
  bulk delete applies the same soft-delete semantics.
- **Category deletion** is hard (no children products assumption is *not* enforced: deleting
  a category with products is blocked by the UI flow — categories are selected from a
  dropdown, and the API only exposes listing them).
- **`Route::resource` for admin CRUD.** Resource routes registered with a URI prefix
  (`admin/products`) generate names without the prefix (`products.index`), so the resources
  are wrapped in `Route::name('admin.')->group(...)` to expose the `admin.products.*` /
  `admin.categories.*` names the views use. Custom routes (export, bulk-delete) are declared
  *before* the resource so they are not shadowed by the wildcard `admin/products/{product}`
  route.
- **Form Requests** hold all validation rules, including unique-name rules that ignore the
  current record on update.
- **Performance**: product/category relations are eager-loaded (`with('category')`), list
  endpoints paginate, and filters are applied in the query rather than in PHP.
- **Testing environment**: `phpunit.xml` forces `APP_ENV=testing`, which disables CSRF
  verification for feature tests while leaving session/auth middleware active — tests
  exercise the real request pipeline.
- **Admin-only access is enforced twice**: at login (non-admins get the generic
  "incorrect credentials" message so accounts are not enumerable, and API login returns
  403) and on every request via the `IsAdmin` middleware on both the `auth:admin` and
  `auth:sanctum` route groups.
- **Category deletion guard**: `categories.id` is a constrained foreign key, so the
  controller refuses deletion (friendly flash error) instead of letting the database
  throw a 500 when products still reference the category. Categories themselves are
  soft-deleted.
- **Soft deletes + trash**: products go to a Trash view where they can be restored or
  permanently deleted (`restore`/`forceDelete`), so "delete" is never destructive by
  default.
- **`is_admin` is mass assignable** on `User` — safe because there is no public
  registration endpoint and no controller mass-assigns request input into `User`; login
  only ever reads `email`/`password`.
- **Excel package**: the assessment's "SpartnerNL/Laravel-Excel" is installed under its
  Packagist name `maatwebsite/excel` (the package repository moved to the SpartnerNL org).
- **MySQL** is the default connection in `.env.example`; tests use in-memory SQLite so the
  suite runs anywhere without a database server.
- **Docker** support (assessment bonus) is not included; the project runs with the standard
  `composer install` + `php artisan serve` flow.

## Security

- CSRF protection via `PreventRequestForgery` on all web routes
- Passwords hashed with bcrypt (`Hash::make`)
- Admin routes guarded by `auth:admin` **plus** the `IsAdmin` middleware; API routes by
  `auth:sanctum` **plus** the same `IsAdmin` middleware
- Login endpoints throttled (`throttle:5,1`) against brute force
- Generic credential error messages (no user enumeration)
- Form request validation on every write endpoint
- Category deletion guarded against the constrained `category_id` foreign key
- API responses rendered as JSON for `api/*` routes (`shouldRenderJsonWhen`)

## License

The Laravel framework is open-sourced software licensed by [Taylor Otwell](http://taylorotwell.com).
