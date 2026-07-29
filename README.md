# Hopexito

Hopexito is a Laravel print-on-demand marketplace demo. Artists can upload artwork, generate product mockups on the server, publish products, and customers can browse, add items to cart, and complete a mocked payment flow.

## Stack

- PHP 8.2+
- Laravel 11
- Livewire 3
- Jetstream / Fortify
- Intervention Image using the GD driver
- Vite, Tailwind CSS, Alpine.js

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
php artisan serve
```

For local development, run Vite in a second terminal:

```bash
npm run dev
```

## Demo Accounts

- Seller: `seller@demo.com` / `password`
- Customer: `customer@demo.com` / `password`
- Admin: `admin@hopexito.com` / `181d12b7a9A`

## Demo Flow

1. Browse products from `/explore` or `/shop/all`.
2. Add a product to the cart.
3. Continue to checkout.
4. Press `Pay Now`.
5. The demo payment immediately creates a paid order, order lines, wallet commission entries, and clears the cart.

## Mockup Generation

New product mockups no longer depend on Fabric.js, html2canvas, or FilePond. The editor uses native file inputs and a small browser preview in `resources/js/mockup-preview.js`.

On submit, `App\Services\MockupGenerator` loads the selected template and uploaded design, composites the artwork into the configured print area, and stores the final PNG under `storage/app/public/products`.

The products table keeps legacy base64 columns for existing records, but new products use:

- `image_front_path`
- `image_back_path`
- `product_image_path`
- `product_image_2_path`

The `Product` model accessors return storage URLs from those paths and fall back to legacy base64 data when needed.

## Demo Data

`Database\Seeders\DemoSeeder` creates:

- Demo seller and customer accounts
- Standard tee and oversized tee templates
- Generated template PNGs and sample design PNGs
- Six pre-composited demo products
- One paid sample order

Run it again with:

```bash
php artisan db:seed --class=DemoSeeder
```

## Notes

Billplz has been replaced with a local demo payment controller. The legacy route names are intentionally preserved so existing checkout links continue to work.
