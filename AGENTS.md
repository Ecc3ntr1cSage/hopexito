# Hopexito — Agent Guide

## Project summary

Hopexito is a Laravel 11 print-on-demand marketplace demo. Users can create garment designs, publish public or private products, browse creators, add products to a cart, complete a simulated payment, receive orders, sell designs, and earn creator commissions.

This is a portfolio/demo application, not a production commerce or payment platform.

## Stack

- PHP 8.2+; Docker uses PHP 8.3.
- Laravel 11.
- Livewire 3.5 with Blade.
- Laravel Jetstream/Fortify for authentication, profiles, email verification, and two-factor authentication.
- Laravel Sanctum with only a minimal `/api/user` endpoint.
- Laravel Socialite for Google OAuth.
- Intervention Image 2.7 with GD for mockup generation; FFmpeg fallback code exists.
- Vite 3, Tailwind CSS 3.1, Alpine.js, Axios.
- MySQL is the documented default; PHPUnit uses in-memory SQLite.

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

Validation:

```bash
php artisan test
npm run build
vendor/bin/pint
```

Fresh demo data:

```bash
php artisan migrate:fresh --seed
```

The demo seeder creates:

- `user@demo.com` / `password`
- `user@test.com` / `password`

## Important source-of-truth files

- `config/catalog.php`: garment types, prices, colors, sizes, commission rate, and mockup geometry.
- `config/shipping.php`: Malaysia delivery rates and authenticated-cart weight tiers.
- `routes/web.php`: application route surface.
- `app/Http/Controllers/ProductsController.php`: product studio validation and creation.
- `app/Services/MockupGenerator.php`: front/back artwork compositing and PNG generation.
- `app/Models/Product.php`: product ownership, visibility scopes, derived image attributes, and generated-media cleanup.
- `app/Http/Controllers/CartController.php`: add-to-cart validation and server-side price calculation.
- `app/Http/Controllers/PaymentController.php`: authenticated demo order completion, sales, and wallet commissions.
- `app/Services/SessionCart.php`: guest session cart.
- `app/Listeners/MergeGuestCartIntoUserCart.php`: guest cart merge on login.
- `database/seeders/DatabaseSeeder.php`: deterministic demo database.
- `database/migrations/`: authoritative schema history.

## Architecture

This is a conventional Laravel monolith:

- Controllers coordinate page requests and multi-model mutations.
- Livewire components implement interactive cart, delivery, product management, order, wallet, and search UI.
- Blade renders page/layout/component HTML.
- Eloquent models persist users, products, variants, carts, orders, order lines, wallets, wallet transactions, searches, and temporary files.
- `SessionCart` handles unauthenticated cart contents in the Laravel session.
- `MockupGenerator` handles image composition.
- Events/listeners handle login cart merging and declared purchase/cart hooks.

There is no separate SPA, repository layer, domain package, queue worker, or full JSON API.

## Product/catalog rules

Configured catalog:

| Type | Price | Colors |
|---|---:|---|
| Shirt (`shirt`) | RM35 | White, Black, Gray |
| Sweatshirt (`sweat`) | RM50 | White, Black, Gray |
| Hoodie (`hoodie`) | RM70 | Black, Gray, Navy, Green |

Sizes are `XS`, `S`, `M`, `L`, `XL`, and `2XL`. Product creation generates one `product_variants` row per configured color. Artwork can be front-only, back-only, or both sides; the missing side uses the plain shared mockup.

Product visibility is `public` or `private`. Product statuses are conventionally:

- `1`: active
- `2`: archived
- `3`: pinned

`Product::available()` means public and not archived. Always enforce ownership/visibility on the server with existing model methods and controller guards.

## Pricing and checkout rules

- External buyers pay the fixed product price.
- Product owners pay 85% of the fixed price for their own products.
- Owner purchases increment sold quantity but create no creator commission.
- External purchases credit `product.price * product.commission_rate * quantity`, normally 15%, to the creator wallet.
- Authenticated cart items are persisted in `carts`; guest cart items live in session key `cart`.
- Checkout and payment routes currently require authentication, despite legacy guest-flow names/code.
- Payment is simulated locally. Billplz route names remain only for compatibility.
- Never trust a client-submitted price or total.

## Media rules

- Shared garment templates are committed under `public/mockups/{shirt|sweat|hoodie}`.
- Generated images are written to the public filesystem disk under `products/{product_id}/...` and exposed through `public/storage`.
- Paths beginning with `mockups/` refer to shared assets and must not be deleted when removing a product.
- `Product::deleteGeneratedMedia()` removes generated variant files and the product directory.
- GD is the normal image backend. FFmpeg fallback requires an installed `ffmpeg` executable.

## Change guidance

### Catalog or product studio changes

1. Update `config/catalog.php`.
2. Check/add matching files under `public/mockups`.
3. Inspect `ProductsController`, `MockupGenerator`, `MockupAssets`, `MockupGeometry`, the editor Blade view, and `mockup-studio.js` together.
4. Preserve server-side upload, rights, preview-side, and transform validation.
5. Update `DatabaseSeeder` and feature tests when the catalog contract changes.

### Pricing or commission changes

Trace all of:

- `CartController`
- `CartComponent`
- `PaymentController`
- `MergeGuestCartIntoUserCart`
- `DatabaseSeeder`
- product/order/wallet feature tests

Keep cart display, stored cart price, order line price, owner discount, sold count, and wallet ledger behavior consistent.

### Cart changes

Support both database and session paths. Check quantity updates, removal, login merge, variant validation, payment-time revalidation, and cart counter behavior.

### Schema changes

Add a new migration; do not edit old migrations for an existing database. Update the model fillable/casts/relationships, seed data, and tests. The migration history contains destructive legacy cleanup and is not perfectly rollback-safe.

## Security and correctness rules

Do not weaken:

- CSRF protection.
- Authentication and email verification requirements.
- Product ownership and visibility checks.
- Server-side upload validation and transform bounds.
- Server-side price recalculation.
- Storage cleanup boundaries.

Known areas needing care:

- Payment is not provider-verified or idempotent.
- Monetary schema columns originate as floating point.
- Carts/orders use email instead of stable user foreign keys.
- Wallet withdrawal currently needs stronger ownership, amount, locking, and transaction checks.
- Search history is retained through `StorefrontController` and `Search`; the unused legacy searchbar Livewire component was removed.
- Legacy empty `Route::resource('cart', ...)` methods and unused guest payment code remain.

## Testing baseline

The full current test run has one known baseline failure in the stale `tests/Feature/ExampleTest.php`: it requests `/` without `RefreshDatabase`, so `StorefrontController` queries missing in-memory SQLite tables. The suite otherwise passes its core catalog, checkout, authentication, profile, security, and management tests; five Jetstream tests are skipped because API tokens/account deletion are disabled.

When changing code:

1. Run the smallest relevant feature test.
2. Run `php artisan test`.
3. Run `npm run build` after JS/CSS/Vite changes.
4. Report baseline failures explicitly.

## Removed legacy code

The current application no longer uses the following old components, so they have been deleted rather than kept as misleading extension points:

- Legacy `Searchbar` Livewire component and its unused Jetstream searchbar view.
- Wishlist Livewire component/view/model and old wishlist model support.
- Inventory model without an active inventory table/workflow.
- Temporary upload controller/model/view support; product studio uploads directly through `ProductsController`.
- Empty `OrderController`; order history is routed directly to `ManageOrder`.
- Empty `PostPolicy` and unused `UserHasCheckout` event.
- Empty pagination Livewire component/view.
- The unused generated `Route::resource('cart', ...)` methods were removed; only the active `cart.index` and `cart.store` routes remain.
- Empty custom preview view and obsolete standard-tee/oversized shop views.
- Unused order-confirmation mailable/view and duplicate custom email notification class.

Historical migrations are intentionally retained. The `2026_08_02_215648_drop_temporary_files_table` migration removes the old temporary upload table for upgraded databases.

## Documentation

The comprehensive project notes live in the Dev Obsidian vault:

- `Projects/Hopexito/00 Project Overview`
- `Projects/Hopexito/01 Architecture`
- `Projects/Hopexito/02 Data Model`
- `Projects/Hopexito/03 Product and Catalog Workflows`
- `Projects/Hopexito/04 Backend Reference`
- `Projects/Hopexito/05 Frontend Reference`
- `Projects/Hopexito/06 Operations and Development`
- `Projects/Hopexito/07 Testing and Known Issues`
- `Projects/Hopexito/08 Repository Map and Working Conventions`
