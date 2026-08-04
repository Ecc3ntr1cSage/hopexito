# Hopexito — Agent Guide

Updated 2026-08-03 from the repository and all nine Hopexito notes in the Dev Obsidian vault. The repository is authoritative when a note describes removed or historical code.

## Project

Hopexito is a Laravel 11 print-on-demand marketplace demo. Users can create garment designs, publish public or private products, browse creators, add variants to a cart, complete a locally simulated payment, receive orders, sell designs, and earn creator commissions.

This is a portfolio/demo application, not a production payment, fulfillment, or payout platform.

## Stack and active boundaries

- PHP 8.2+; Docker builds with PHP 8.3 Apache.
- Laravel 11, Livewire 3.5, Blade, Alpine.js, Tailwind CSS 3.1, Vite 3.
- Laravel Fortify and Jetstream for session authentication, profile/password settings, browser sessions, and two-factor authentication.
- Laravel Sanctum exposes only the authenticated `GET /api/user` endpoint; Jetstream API-token management, teams, and account deletion are disabled and their unused UI/tests have been removed.
- Intervention Image 2.7 with GD is the normal mockup backend. `MockupGenerator` has an FFmpeg fallback when GD is unavailable; FFmpeg is not a Composer dependency.
- MySQL is the documented default. PHPUnit uses in-memory SQLite and array sessions.
- There is no SPA, repository/domain package, queue worker, payment-provider integration, Socialite/Google OAuth flow, or full JSON API.
- Application-level broadcasting is not configured; the old no-op broadcast events/provider/channel file were removed.

## Essential commands

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve
npm run dev
```

On PowerShell, use `Copy-Item .env.example .env` for the copy step. Use `php artisan migrate:fresh --seed` for a clean demo database.

Validation:

```bash
php artisan route:list
php artisan migrate:status
php artisan test
npm run build
vendor/bin/pint
```

The deterministic seeder creates `user@demo.com` and `user@test.com`, both with password `password`.

## Source of truth

- `routes/web.php` and `routes/api.php`: the active route surface.
- `config/catalog.php`: garment types, prices, colors, sizes, commission rate, canvas, and print geometry.
- `config/shipping.php`: Malaysia state base delivery prices. Weight surcharges are implemented in `PaymentController::delivery()`.
- `app/Http/Controllers/ProductsController.php`: product-studio validation and creation.
- `app/Services/MockupGenerator.php`: artwork compositing and PNG generation.
- `app/Support/MockupAssets.php` and `app/Support/MockupGeometry.php`: template paths and normalized editor geometry.
- `app/Models/Product.php` and `app/Models/ProductVariant.php`: visibility, route binding, image URLs, and generated-media cleanup.
- `app/Http/Controllers/CartController.php`: add-to-cart validation and server-derived prices.
- `app/Services/SessionCart.php`: guest session-cart storage.
- `app/Listeners/MergeGuestCartIntoUserCart.php`: login cart merge.
- `app/Http/Controllers/PaymentController.php`: authenticated demo checkout, orders, sold counts, and creator wallets.
- `database/migrations/`: authoritative schema history; do not edit old migrations for an existing database.
- `database/seeders/DatabaseSeeder.php`: complete deterministic demo state.

## Architecture and routes

This is a conventional Laravel monolith:

- Controllers coordinate page requests and multi-model mutations.
- Livewire components provide interactive cart, delivery, product management, order/sales, and wallet UI.
- Blade renders layouts, pages, and Livewire views.
- Eloquent models persist users, products, variants, carts, orders, order lines, wallets, wallet transactions, and searches.
- `SessionCart` handles unauthenticated cart contents; authenticated cart lines are database rows.
- `MockupGenerator` writes generated images to the public filesystem disk.
- The only registered application listener is `Login` → `MergeGuestCartIntoUserCart`; event discovery is disabled.

Current application routes:

- Public: `/`, `/product/{product}`, `GET|POST /cart`, `/shop`, `/discover`, `/explore` redirect, and the final catch-all creator route `/{shopname}`.
- Authenticated: `/dashboard`, the three `/mockup/*` compatibility redirects, `/product/manage`, `/product/create`, product create/update/delete routes, `/checkout`, `/billplz`, and `/billplz-redirect`.
- `/order/index` is backed directly by the `ManageOrder` Livewire component and has no explicit route middleware; the component supports authenticated history and the session’s last order.
- Billplz names are compatibility names only. Payment is completed locally by `PaymentController`.
- `/api/user` is protected by `auth:sanctum` and is the only application API route.

The catch-all `/{shopname}` route must remain last.

## Catalog and pricing rules

| Key | Label | Price | Colors |
|---|---|---:|---|
| `shirt` | Shirt | RM35 | White, Black, Gray |
| `sweat` | Sweatshirt | RM50 | White, Black, Gray |
| `hoodie` | Hoodie | RM70 | Black, Gray, Navy, Green |

Sizes are `XS`, `S`, `M`, `L`, `XL`, and `2XL`. The configured default commission is 15%. The editor canvas is 850×900. Each created product gets one variant per configured color.

- External buyers pay the product’s fixed catalog price.
- Owners pay 85% of the fixed price for their own products.
- Owner purchases increment `products.sold`, set `is_owner_purchase`, and create no wallet income.
- External purchases increment `products.sold` and credit `product.price × product.commission_rate × quantity` to the creator wallet.
- Prices and totals must always be recalculated server-side.
- Product visibility is `public` or `private`; status `1` is active, `2` archived, and `3` pinned.
- `Product::available()` means public and not archived. `canBeViewedBy()` allows any public product or the owner; callers still need an explicit archived-product guard when purchasing.

## Product studio and media

`GET /product/create` renders the studio. `POST /product` validates the type, title, tags, visibility, preview color/side, rights acceptance, image uploads, and per-side transforms. At least one valid front/back image is required. Transform bounds are:

- `x`, `y`: 0–100
- `scale`: 0.25–2
- `rotation`: -180–180

Creation runs in a database transaction, creates the product, generates artwork variants for every catalog color, and falls back to the plain shared template for an unsubmitted side. Generated paths are `products/{product_id}/{color}-front.png` and `...-back.png` on the `public` disk.

- Shared templates are committed under `public/mockups/{shirt|sweat|hoodie}`.
- Paths beginning with `mockups/` are shared assets and must never be deleted during product cleanup.
- Generated files are under `storage/app/public/products/{product_id}` and require `php artisan storage:link`.
- The product deleting hook removes generated files and the product directory, but media written during a failed transaction is not automatically compensated.
- Preserve server-side upload validation even if editor JavaScript changes.

## Cart, delivery, and checkout

`CartController::store()` validates product id, real variant color, size, and quantity 1–99. It ignores client prices. Authenticated rows are stored in `carts`; guests use session key `cart` through `SessionCart`. Repeated adds create separate lines. On login, the listener skips invalid/inaccessible/archived products, recalculates owner pricing, creates database lines, and clears the session cart.

The active checkout is authenticated despite the legacy `guest.checkout` and `billplz-*` route names:

1. `/checkout` stores delivery information in the session; authenticated users are prefilled from their profile.
2. `/billplz` recomputes cart lines, subtotal, delivery, and total.
3. `POST /billplz` accepts only `success` or `failed`.
4. A success transaction creates the order and order lines, revalidates products, increments sold quantities, records external commissions, deletes cart lines, and stores `last_order_id`.

Base delivery rates are RM7 for most Malaysian states and RM16 for Sabah, Sarawak, and Labuan. Authenticated checkout adds RM3 above 1000g, RM6 above 1500g, and RM8 above 8000g; each item contributes 500g. There is no active guest order-completion path.

## Schema and migration state

The final logical model is `users`, `products`, `product_variants`, `carts`, `orders`, `product_orders`, `wallets`, `wallet_transactions`, `searches`, and `sessions`. Carts and orders identify users by email rather than stable foreign keys; order lines and carts also retain denormalized snapshots.

The migration history intentionally preserves the older artist/catalog design and later destructive cleanup. The current tree includes `2026_08_03_000001_remove_google_and_mail_columns.php`, which removes legacy `google_id`, `email_verified_at`, and `password_resets`; in the inspected local database this migration is pending. Run migrations before relying on the final schema.

Do not edit historical migrations. For schema changes, add a migration and update the model fillable/casts/relationships, seeder, and feature tests. Fresh and upgraded databases can differ until every migration is applied. Legacy `down()` methods are not uniformly rollback-safe.

## Security and correctness boundaries

Do not weaken CSRF protection, authentication, product ownership/visibility checks, upload/transform validation, storage boundaries, or server-side pricing.

Known risks to preserve in documentation and address deliberately if requested:

- Payment is not provider-verified, idempotent, refundable, or reconciled.
- Money and delivery arithmetic still uses floating-point/legacy numeric columns.
- Email-based cart/order ownership is weaker than stable user foreign keys.
- Wallet update/withdrawal Livewire actions trust caller-supplied user ids, do not lock the wallet, and do not enforce requested withdrawal ≤ balance.
- Cart Livewire mutation methods load rows by id without an explicit authenticated-email ownership condition.
- Product deletion does not reconcile stale cart/order rows.
- Generated media can remain after a later variant fails inside a database transaction.
- The public creator route is catch-all and route order is security/correctness sensitive.
- `trustProxies(at: '*')` assumes a known TLS-terminating deployment proxy; review it for a different topology.

## Change guidance

### Catalog, studio, or media

Trace `config/catalog.php`, `ProductsController`, `MockupGenerator`, `MockupAssets`, `MockupGeometry`, `resources/views/mockup/editor.blade.php`, `resources/js/mockup-studio.js`, the committed mockups, the seeder, and `UnifiedCatalogTest` together.

### Pricing, checkout, or cart

Trace `CartController`, `CartComponent`, `CartCounter`, `PaymentController`, `SessionCart`, `MergeGuestCartIntoUserCart`, `DeliveryInformation`, the relevant Blade views, the seeder, and checkout/catalog tests. Check both guest session and authenticated database paths.

### Auth/profile/security

Use Fortify/Jetstream configuration and actions as the source of truth. Current product-management routes use `auth`, not `verified`. Preserve rate limiting, CSRF, two-factor behavior, and server-side ownership checks.

### Frontend

Blade and Livewire are the UI source of truth. Update the PHP component and its Blade view together. Run `npm run build` after CSS, JS, Vite, Tailwind, or Blade changes.

Avoid new abstractions, packages, or compatibility shims unless the existing code and request require them. Reuse the current services and model scopes.

## Testing baseline (verified 2026-08-03)

`php artisan test` currently reports 34 passed (175 assertions). The only output warning is that `phpunit.xml` uses a deprecated XML schema.

`npm run build` passes. Vite warns that `caniuse-lite` is outdated. Report these baseline results explicitly; do not hide or “fix” them while making an unrelated change.

## Removed legacy code

The current tree intentionally does not contain the old Searchbar, Wishlist, Inventory, TemporaryFile/upload endpoint, OrderController, PostPolicy, UserHasCheckout event, custom Pagination component, old standard-tee/oversized shop views, custom preview view, order-confirmation mail, duplicate custom email notification, API-token/account-deletion UI and tests, no-op broadcast provider/events/channels, empty auth provider, Scout config, or generated example tests. Historical migrations remain because they are upgrade history. Do not document or revive removed code as active extension points.

## Obsidian project notes

The detailed notes are in the Dev vault under `Projects/Hopexito`:

- `00 Project Overview`
- `01 Architecture`
- `02 Data Model`
- `03 Product and Catalog Workflows`
- `04 Backend Reference`
- `05 Frontend Reference`
- `06 Operations and Development`
- `07 Testing and Known Issues`
- `08 Repository Map and Working Conventions`

Some notes retain historical observations. When they disagree with `routes/`, the current class/file tree, migrations, or tests, update documentation from the repository state.
