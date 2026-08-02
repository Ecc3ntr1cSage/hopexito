# Hopexito

Hopexito is a Laravel 11 print-on-demand marketplace demo. Users create garment designs, generate color variants and mockups, publish products, discover other creators, buy products through a simulated checkout, manage orders, and earn commissions from external sales.

> **Demo scope:** payment completion is simulated locally. This project is suitable for portfolio/demo use and is not a production-ready payment, fulfillment, or payout system.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-green)](https://livewire.laravel.com)
[![Tailwind](https://img.shields.io/badge/Tailwind-3.1-blue)](https://tailwindcss.com)

## Features

- Product studio for Shirt, Sweatshirt, and Hoodie designs.
- Front-only, back-only, or two-sided artwork uploads.
- Server-side mockup generation with Intervention Image/GD and an FFmpeg fallback path.
- Fixed catalog prices and color variants.
- Public or private products.
- Product discovery, type filters, creator pages, and search history.
- Guest session cart and authenticated database cart.
- Login-time guest-cart merge.
- Size/color selection, quantity management, and owner purchase discount.
- Simulated successful/failed payment flow.
- Order history and received-order status.
- Creator sales dashboard and wallet commission ledger.
- Google OAuth through Laravel Socialite.
- Jetstream profile, email verification, password, browser-session, and two-factor flows.
- Docker deployment image with Apache, PHP 8.3, Composer, and a production Vite build.

## Catalog

Defined in [`config/catalog.php`](config/catalog.php):

| Product | Price | Colors |
|---|---:|---|
| Shirt | RM35.00 | White, Black, Gray |
| Sweatshirt | RM50.00 | White, Black, Gray |
| Hoodie | RM70.00 | Black, Gray, Navy, Green |

Available sizes are `XS`, `S`, `M`, `L`, `XL`, and `2XL`.

Business rules:

- External purchases use the fixed catalog price.
- Owners pay 85% of the fixed price for their own products.
- External purchases credit the creator with the configured commission rate, normally 15%.
- Owner purchases increase sold quantity but do not create creator earnings.
- Products use status `1` active, `2` archived, and `3` pinned.

## Requirements

- PHP 8.2+.
- Composer.
- Node.js and npm.
- PHP GD extension for normal mockup generation.
- MySQL for the documented local configuration, or SQLite for tests/local alternatives.
- FFmpeg only when using the mockup generator fallback without GD.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```

Configure the database and other services in `.env`. The example configuration targets MySQL and uses database-backed sessions.

Start the application:

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000).

For frontend development with Vite hot reload, use a second terminal:

```bash
npm run dev
```

## Demo accounts

The seeded demo database creates:

| Account | Email | Password |
|---|---|---|
| Demo user | `user@demo.com` | `password` |
| Test user | `user@test.com` | `password` |

Reset the demo database with:

```bash
php artisan migrate:fresh --seed
```

## Typical demo flow

1. Open the home page or `/discover`.
2. Search for a product or browse a creator page.
3. Open a product, choose a size and color, then select **Add to bag** or **Buy now**.
4. Review the cart and complete delivery information.
5. On the payment page, choose the simulated success or failure result.
6. A successful demo payment creates an order, order lines, sold-count updates, and creator commission records, then clears the authenticated cart.
7. Log in as a creator to open the product workbench, sales page, or wallet.

To create a product:

1. Log in with a verified account.
2. Open `/product/create` or one of the compatibility routes under `/mockup`.
3. Select a garment type and preview color.
4. Upload front and/or back artwork.
5. Position, scale, and rotate the artwork in the studio.
6. Accept the rights confirmation and submit.
7. Hopexito creates one variant per configured garment color and redirects to the product page.

## Application structure

```text
app/
├── Http/Controllers/       Page requests and commerce mutations
├── Http/Livewire/          Cart, checkout, product, order, wallet, search UI
├── Models/                 Users, products, variants, carts, orders, wallets
├── Services/               SessionCart and MockupGenerator
├── Support/                Mockup asset and geometry helpers
├── Events/                 Cart and purchase domain events
└── Listeners/              Login guest-cart merge

config/
├── catalog.php             Product types, prices, colors, sizes, geometry
└── shipping.php            Malaysia delivery rates

database/
├── migrations/             Legacy-to-unified schema history
└── seeders/DatabaseSeeder.php

public/mockups/             Shared garment templates
resources/views/             Blade layouts, pages, Livewire, auth, vendor UI
resources/js/                App and mockup-studio JavaScript
resources/css/               Tailwind/application styles
routes/web.php              Main web routes (active cart index/store routes only)
routes/api.php              Minimal Sanctum user endpoint
tests/                      PHPUnit feature and unit tests
Dockerfile                  Multi-stage production image
AGENTS.md                   Project-specific coding-agent guide
```

## Architecture notes

Hopexito is a server-rendered Laravel monolith:

- Blade renders pages and layouts.
- Livewire provides interactive server-backed components.
- Controllers coordinate request validation and domain workflows.
- Eloquent models persist application state.
- `SessionCart` stores unauthenticated cart contents in the session.
- `MockupGenerator` composites artwork onto shared garment templates.
- Authenticated cart lines, orders, order lines, wallets, and wallet transactions are database records.

The primary application is a web UI. The API currently contains only the Sanctum-authenticated `/api/user` route. Obsolete wishlist, inventory, legacy upload, old searchbar, empty order-controller, and unused view/component files have been removed; see the project notes for the cleanup record.

## Storage and media

- Shared mockups are committed under `public/mockups/{shirt|sweat|hoodie}`.
- Generated product images are stored on Laravel's public disk under `products/{product_id}/...`.
- Run `php artisan storage:link` so generated images are available through `public/storage`.
- Product deletion removes generated files but preserves shared `public/mockups` templates.

## Docker

Build and run the production image with your platform's database environment configured:

```bash
docker build -t hopexito .
docker run --env-file .env -p 10000:10000 hopexito
```

The image:

- Uses PHP 8.3 Apache with the document root set to `public`.
- Installs GD, bcmath, DOM, intl, PDO MySQL/PostgreSQL, and zip.
- Builds frontend assets in a Node 22 stage.
- Runs migrations, caches config/routes/views, creates the storage link, and starts Apache from `docker/entrypoint.sh`.
- Expects the database to be reachable during container startup.

The container does not run the demo seeder.

## Tests and build

Run the test suite:

```bash
php artisan test
```

Build production frontend assets:

```bash
npm run build
```

Current repository baseline: the core feature tests pass, but the untouched generated `tests/Feature/ExampleTest.php` fails because it requests the database-backed home page without using `RefreshDatabase`. Five Jetstream tests are skipped because API tokens and account deletion are disabled in `config/jetstream.php`. See [`AGENTS.md`](AGENTS.md) and the project notes for details.

## Configuration reference

| File | Responsibility |
|---|---|
| `config/catalog.php` | Garment types, fixed prices, colors, sizes, commission, geometry. |
| `config/shipping.php` | Malaysian state rates and weight rules. |
| `config/auth.php` | Web session guard and Eloquent user provider. |
| `config/jetstream.php` | Livewire stack and enabled Jetstream features. |
| `config/services.php` | Google OAuth and other external service settings. |
| `.env` | Environment, database, mail, session, filesystem, and credentials. |

## Legacy compatibility

Billplz-named routes remain for old links, but current order completion is local and simulated. Several old migration/model/view files remain because the project was consolidated from an earlier artist/custom-product design. The 2026 migrations unify creator identity and catalog products around `users`, `products`, and `product_variants`.

## Documentation

A comprehensive codebase analysis is stored in the **Dev** Obsidian vault under `Projects/Hopexito`:

- `00 Project Overview`
- `01 Architecture`
- `02 Data Model`
- `03 Product and Catalog Workflows`
- `04 Backend Reference`
- `05 Frontend Reference`
- `06 Operations and Development`
- `07 Testing and Known Issues`
- `08 Repository Map and Working Conventions`

Start with [`AGENTS.md`](AGENTS.md) when making code changes.

## License and attribution

This project is licensed under the MIT license. Original project attribution: [SuPatee](https://github.com/supatee).
