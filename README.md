# Hopexito

Hopexito is a Laravel 11 print-on-demand marketplace demo. Users create garment designs, generate color variants and mockups, publish products, discover creators, add products to a cart, complete a simulated checkout, manage orders, sell designs, and earn creator commissions.

> Demo scope: payment completion is local and simulated. This is a portfolio/demo application, not a production payment, fulfillment, or payout system.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com) [![Livewire](https://img.shields.io/badge/Livewire-3-green)](https://livewire.laravel.com) [![Tailwind](https://img.shields.io/badge/Tailwind-3.1-blue)](https://tailwindcss.com)

## Features

- Product studio for shirts, sweatshirts, and hoodies.
- Front-only, back-only, or two-sided artwork uploads.
- Server-side PNG mockup generation with Intervention Image/GD and an FFmpeg fallback.
- Fixed catalog prices, configured colors, sizes, and public/private visibility.
- Creator discovery, product-type filtering, creator pages, and search history.
- Guest session cart and authenticated database cart with login-time merge.
- Size/color selection, quantity changes, owner purchase discount, and simulated success/failure payment.
- Order history, received status, creator sales, wallet commissions, and withdrawal UI.
- Fortify/Jetstream registration, profile/password settings, browser sessions, and two-factor authentication.
- Docker image with PHP 8.3 Apache and a production Vite build.

There is no active Google OAuth flow, email verification middleware, full product/order API, wishlist, inventory, or live payment integration.

## Catalog and business rules

| Product | Key | Price | Colors |
|---|---|---:|---|
| Shirt | `shirt` | RM35.00 | White, Black, Gray |
| Sweatshirt | `sweat` | RM50.00 | White, Black, Gray |
| Hoodie | `hoodie` | RM70.00 | Black, Gray, Navy, Green |

Sizes are `XS`, `S`, `M`, `L`, `XL`, and `2XL`. The default creator commission is 15% and the editor canvas is 850×900.

- External buyers pay the fixed product price.
- Owners pay 85% for their own products.
- Owner purchases increase sold quantity but create no creator commission.
- External purchases credit `product.price × product.commission_rate × quantity` to the creator wallet.
- Product status `1` is active, `2` archived, and `3` pinned.

Catalog values live in [`config/catalog.php`](config/catalog.php). Malaysian base shipping rates live in [`config/shipping.php`]; authenticated checkout adds weight surcharges in `PaymentController`.

## Requirements

- PHP 8.2+ and Composer.
- Node.js and npm.
- PHP GD for normal mockup generation.
- MySQL for the documented default, or SQLite for tests/local alternatives.
- FFmpeg only when using the mockup fallback without GD.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```

On PowerShell, use `Copy-Item .env.example .env`. Configure the database and other environment values in `.env`. The example configuration uses MySQL and database-backed sessions.

Start the app:

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000). Run `npm run dev` in another terminal for Vite hot reload.

Reset the deterministic demo database with:

```bash
php artisan migrate:fresh --seed
```

## Demo accounts

| Account | Email | Password |
|---|---|---|
| Demo user | `user@demo.com` | `password` |
| Test user | `user@test.com` | `password` |

## Typical demo flow

1. Browse `/` or `/discover`, search `/shop`, or open a creator page.
2. Open a product, choose a size and color, then select **Add to bag** or **Buy now**.
3. Review the cart and complete `/checkout` delivery information.
4. On `/billplz`, choose the local simulated success or failure result.
5. A successful result creates the order and lines, increments sold counts, records external commissions, and clears the authenticated cart.
6. Open `/product/manage` for products, `/order/index` for orders/sales, or the dashboard wallet surface for commissions.

To create a product, log in, open `/product/create` (or a `/mockup/*` compatibility redirect), choose a garment and preview color, upload artwork, position/scale/rotate it, accept the rights confirmation, and submit. Hopexito generates one variant per configured color and redirects to the product page.

## Current route surface

- Public: `/`, `/product/{product}`, `GET|POST /cart`, `/shop`, `/discover`, `/explore`, and `/{shopname}`.
- Authenticated: `/dashboard`, `/mockup/standard-tee`, `/mockup/sweatshirt`, `/mockup/hoodie`, `/product/manage`, `/product/create`, product mutations, `/checkout`, `/billplz`, and `/billplz-redirect`.
- Orders: `/order/index` is a Livewire page supporting authenticated history and the session’s last order.
- API: authenticated `GET /api/user` only.

Billplz route names remain for compatibility; they do not call Billplz.

## Architecture

Hopexito is a server-rendered Laravel monolith:

- Controllers coordinate requests, validation, and commerce mutations.
- Livewire provides interactive cart, delivery, product-management, order/sales, and wallet surfaces.
- Blade renders layouts and pages.
- Eloquent stores users, products, variants, carts, orders, order lines, wallets, wallet transactions, and searches.
- `SessionCart` stores unauthenticated cart contents in the session.
- `MockupGenerator` composites artwork onto shared garment templates.
- `routes/web.php` and `routes/api.php` define the active application surface.

Important files:

| File | Responsibility |
|---|---|
| `config/catalog.php` | Catalog, pricing, colors, sizes, commission, geometry |
| `config/shipping.php` | Malaysian state shipping prices |
| `app/Http/Controllers/ProductsController.php` | Product studio and product mutations |
| `app/Services/MockupGenerator.php` | Image composition and generated PNGs |
| `app/Http/Controllers/CartController.php` | Cart input validation and price calculation |
| `app/Http/Controllers/PaymentController.php` | Demo orders, sold counts, and commissions |
| `app/Services/SessionCart.php` | Guest session cart |
| `app/Listeners/MergeGuestCartIntoUserCart.php` | Login cart merge |
| `app/Models/Product.php` | Visibility, image accessors, and media cleanup |
| `database/migrations/` | Schema history |
| `database/seeders/DatabaseSeeder.php` | Demo data |

## Storage and media

- Shared templates are committed under `public/mockups/{shirt|sweat|hoodie}`.
- Generated images are stored under `storage/app/public/products/{product_id}`.
- Run `php artisan storage:link` to expose generated images through `public/storage`.
- Product deletion removes generated files but preserves paths beginning with `mockups/`.

## Docker

```bash
docker build -t hopexito .
docker run --env-file .env -p 10000:10000 hopexito
```

The multi-stage image uses PHP 8.3 Apache, Node 22 for the Vite build, and a runtime entrypoint that creates the storage link, runs `php artisan migrate --force`, caches config/routes/views, and starts Apache. It expects database connectivity at startup and does not run the demo seeder.

## Tests and build

```bash
php artisan test
npm run build
vendor/bin/pint
```

Verified baseline on 2026-08-03:

- `npm run build` passes; Vite reports an outdated `caniuse-lite` database.
- `php artisan test` reports 34 passed, 2 failed, and 5 skipped/warned (41 tests, 159 assertions).
- The stale `tests/Feature/ExampleTest.php` hits the database-backed home page without `RefreshDatabase`.
- One `UnifiedCatalogTest` product-generation test fails because the fake public disk lacks the expected shared `mockups/shirt` directory during cleanup.
- Five Jetstream API-token/account-deletion tests are skipped because those features are disabled.
- PHPUnit warns that `phpunit.xml` uses a deprecated XML schema.

See [`AGENTS.md`](AGENTS.md) for the current working conventions, known security/correctness risks, and change recipes.

## Migration note

The migration history contains destructive cleanup of the old artist/catalog schema. The current tree includes a pending `2026_08_03_000001_remove_google_and_mail_columns.php` migration that removes legacy Google/email-verification/password-reset columns. Run migrations before using an existing database and do not edit historical migrations.

## Documentation

The full codebase analysis is in the Dev Obsidian vault under `Projects/Hopexito`:

- `00 Project Overview`
- `01 Architecture`
- `02 Data Model`
- `03 Product and Catalog Workflows`
- `04 Backend Reference`
- `05 Frontend Reference`
- `06 Operations and Development`
- `07 Testing and Known Issues`
- `08 Repository Map and Working Conventions`

When those notes disagree with the current route list, file tree, migrations, or tests, the repository is the source of truth.

## License

MIT. Original project attribution: [SuPatee](https://github.com/supatee).
