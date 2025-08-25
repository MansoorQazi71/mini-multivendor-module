Mini Multi-Vendor Product Management (Laravel 11)

A compact, production-readable starter that demonstrates a multi-vendor product workflow with admin approval, a clean service layer, custom helper, events + queued notifications, and role-based authorization.

Features

Roles: admin, vendor

Vendors: add / edit / delete / list their own products

Admins: review pending products, approve/reject

Service layer: App\Services\ProductService

Helper: generateProductCode() → PRD-YYYY-XXXX

Events & Notifications:

on product create → notify all admins

on approve/reject → notify product’s vendor
(stored in DB; optional email)

Queue: database driver (jobs processed asynchronously)

Seeders: 1 admin, 1 vendor, 2 sample products

Minimal UI: Blade + Bootstrap CDN

Tech Stack

PHP 8.2+, Laravel 11.x

MySQL/MariaDB (or PostgreSQL)

Composer

Prerequisites

PHP 8.2+, Composer

A database (MySQL/MariaDB recommended)

Node is not required for this minimal Blade UI

Getting Started
1) Fork or Clone
# Fork on GitHub, then:
git clone https://github.com/<your-username>/mini-multivendor-laravel.git
# Or clone directly if you have access:
git clone https://github.com/<owner>/mini-multivendor-laravel.git
cd mini-multivendor-laravel

2) Install dependencies
composer install

3) Configure environment
cp .env.example .env
php artisan key:generate


Edit .env to point to your database:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_multivendor_module
DB_USERNAME=root
DB_PASSWORD=

4) Register the helper (autoload)

In composer.json under "autoload" add:

"files": ["app/Helpers/helpers.php"]


Then:

composer dump-autoload

5) Register middleware alias & event provider (Laravel 11)

Open bootstrap/app.php and ensure:

use App\Http\Middleware\EnsureRole;
use App\Providers\EventServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
    })
    ->withProviders([
        EventServiceProvider::class,
    ])
    ->create();

6) Migrate & seed
php artisan migrate:fresh --seed

7) Queue setup (for notifications)

In .env:

QUEUE_CONNECTION=database
MAIL_MAILER=log   # or configure SMTP if you want real emails


Create queue tables if needed:

php artisan queue:table
php artisan queue:failed-table
php artisan migrate


Run a worker (in another terminal):

php artisan queue:work

8) Run the app
php artisan serve


Visit: http://127.0.0.1:8000

Accounts (seeded)

Admin: admin@example.com / password

Vendor: vendor@example.com / password

Key URLs

Login: GET /login

Dashboard (role-aware redirect): GET /dashboard

Vendor products: GET /products

Add product: GET /products/create

Admin pending: GET /admin/products/pending

Notifications: GET /notifications
(Navbar shows unread badge; clicking a notification marks it read and redirects:
Admin → pending list, Vendor → products page.)

How It Works

ProductService encapsulates all product logic (create/update/delete/approve/reject).
On create: sets pending, generates code, dispatches ProductCreated.

Event → Listener → Notification (queued)

ProductCreated → SendProductPendingApproval → notifies all admins

On approve/reject → notifies product’s vendor

Project Structure (high level)
app/
  Events/ProductCreated.php
  Helpers/helpers.php
  Http/Controllers/{ProductController, Admin/ProductApprovalController}.php
  Http/Middleware/EnsureRole.php
  Models/{User, Product}.php
  Notifications/{NewProductPendingApproval, ProductApproved, ProductRejected}.php
  Providers/EventServiceProvider.php
  Services/ProductService.php
database/
  migrations/* (users, products, notifications, jobs, failed_jobs)
  seeders/{UserSeeder, ProductSeeder}.php
resources/views/
  layouts/app.blade.php
  auth/login.blade.php
  products/{index,create,edit}.blade.php
  admin/products/pending.blade.php
  notifications/index.blade.php
routes/web.php
bootstrap/app.php  (middleware alias + providers)

Contributing (Fork & PR)

Fork the repository to your GitHub.

Create a branch: git checkout -b feature/your-change

Commit: git commit -m "Add your change"

Push: git push origin feature/your-change

Open a Pull Request to main.

Troubleshooting

“Call to undefined function generateProductCode()”
Ensure "files": ["app/Helpers/helpers.php"] is in composer.json and run composer dump-autoload.

Foreign key errors on products.user_id
Run php artisan migrate:fresh making sure users migration runs before products. Both should use InnoDB.

Notifications not appearing
Confirm notifications table exists, QUEUE_CONNECTION=database, a worker is running, and you created/approved/rejected a product.

Emails not received
With MAIL_MAILER=log, check storage/logs/laravel.log. Configure SMTP for real emails.