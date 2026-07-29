# Hopexito

Print-on-demand marketplace demo built with Laravel 11. Artists upload artwork, generate real server-side product mockups with the GD driver, publish designs, and customers can browse, add items to cart, and complete a mocked checkout flow.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-green)](https://livewire.laravel.com)
[![Tailwind](https://img.shields.shields.io/badge/Tailwind-3.1-blue)](https://tailwindcss.com)

---

## Tech Stack

- **PHP 8.2+** / **Laravel 11**
- **Livewire 3** — interactive cart, search, wallet, product management panels
- **Jetstream / Fortify** — authentication, profiles, teams
- **Spatie Laravel Permission** — role-based access (`artist`, `customer`)
- **Intervention Image (GD)** — server-side mockup compositing
- **Vite** + **Tailwind CSS 3.1** + **Alpine.js** — frontend tooling
- **Laravel Socialite** — Google OAuth login

---

## Core Features

### For Artists
- **Product creation with live mockup preview** — upload a design, pick a template (standard tee or oversized), position it on the garment, select color, and the server composites a PNG mockup in real time
- **Template management** — create and manage garment templates with configurable min price, commission rate, and supported colors
- **Product collections** — group products into named collections with cover images
- **Sales dashboard** — view total items sold, commission earnings, and individual order lines
- **Wallet** — track commission income, manage bank details, and request withdrawals
- **Profile customization** — custom cover image, bio, and social links (Facebook, Twitter, Instagram, Dribbble, Behance, Pinterest, DeviantArt, TikTok, website)
- **Order management** — mark orders as received, view full order history

### For Customers
- **Product discovery** — explore feed, filtered shops (standard tee / oversized / all), and searchable catalog
- **Seller profiles** — view any artist's shop, see their bio, cover, and product catalog
- **Cart** — add to cart with size/color variants, adjust quantities, apply discounts
- **Guest checkout** — fill in delivery information without an account
- **Mocked payment flow** — immediately creates a paid order, order lines, wallet commissions, and clears the cart
- **Wishlist** — save favorite products

### System Features
- **Role-based access** — `artist` creates products and earns commissions, `customer` browses and purchases
- **Commission tracking** — each sale automatically creates wallet transactions and updates artist balances
- **Event listeners** — `PurchaseCompleted`, `AddedToCart`, `UserHasCheckout` dispatched on key actions
- **Search history** — logged-in users' searches are tracked and displayed in the search bar
- **Shipping cost calculation** — state-based rates from Malaysia with weight-based adjustments
- **Google OAuth** — single sign-on auto-links returning Google users or registers new ones

---

## Project Structure

```
app/
├── Actions/
├── Console/
├── Events/
│   ├── AddedToCart.php
│   ├── PurchaseCompleted.php
│   └── UserHasCheckout.php
├── Facades/
│   └── SessionCart.php
├── Http/
│   ├── Controllers/
│   │   ├── CartController.php
│   │   ├── ExploreController.php
│   │   ├── GoogleAuthController.php
│   │   ├── MockupController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   ├── ProductsController.php
│   │   └── UploadController.php
│   ├── Livewire/
│   │   ├── Cart/
│   │   ├── DeliveryInformation.php
│   │   ├── Manage/
│   │   ├── Searchbar.php
│   │   ├── Pagination.php
│   │   ├── SocialLinks.php
│   │   ├── Wallet.php
│   │   └── Wishlist.php
│   └── Middleware/
├── Models/
│   ├── Artist.php
│   ├── Cart.php
│   ├── CustomProduct.php
│   ├── Inventory.php
│   ├── Order.php
│   ├── Product.php
│   ├── ProductCollection.php
│   ├── ProductOrder.php
│   ├── ProductTemplate.php
│   ├── Search.php
│   ├── TemporaryFile.php
│   ├── User.php
│   ├── Wallet.php
│   ├── WalletTransaction.php
│   └── Wishlist.php
├── Providers/
├── Services/
│   ├── MockupGenerator.php
│   └── SessionCart.php
└── View/

database/
├── migrations/
├── seeders/
│   ├── DatabaseSeeder.php
│   └── DemoSeeder.php

resources/
├── css/app.css
├── js/
│   ├── app.js
│   └── mockup-preview.js
└── views/
    ├── layouts/
    ├── livewire/
    ├── cart/
    ├── custom/
    ├── mockup/
    ├── order/
    ├── product/
    ├── shop/
    ├── profile/
    └── ...

routes/
├── web.php
└── api.php
```

---

## Key Technical Highlights

### Mockup Generation Service
`App\Services\MockupGenerator` composites uploaded designs onto garment templates using the Intervention Image GD driver. It supports front and back designs with configurable positioning and garment color.
- Location: `app/Services/MockupGenerator.php`
- Entry points: `ProductsController::store`, `UploadController::uploadCustom`

### Session-Based Cart for Guests
`App\Services\SessionCart` backed by `App\Facades\SessionCart` provides a full cart lifecycle (add, update, remove, subtotal, destroy) for unauthenticated users via Laravel session storage. Authenticated users persist through the `carts` database table.

### Commission & Wallet Flow
On every successful payment, both authenticated and guest orders generate `WalletTransaction` records, update the artist's `Wallet` balance, and increment the product's `sold` count.

---

## Setup

### Prerequisites
- **PHP 8.2+**
- **Composer**
- **Node.js & npm**
- **MySQL** (or use the included SQLite database)

### Installation

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Create the storage symlink
php artisan storage:link

# Run migrations and see test data
php artisan migrate --seed
```

### Build Frontend Assets

```bash
# Development (with hot reload)
npm run dev

# Production build
npm run build
```

### Start the Development Server

```bash
php artisan serve
```

Then open `http://localhost:8000`.

---

## Running Tests

```bash
php artisan test
```

---

## Seeders

### DatabaseSeeder
Creates base admin/artist/customer accounts and runs `DemoSeeder`.

### DemoSeeder
Generates the complete demo environment:
- Demo seller (`seller@demo.com`) and customer (`customer@demo.com`) accounts
- Standard tee and oversized tee templates
- Template and design PNG assets (via Intervention Image or SVG fallbacks)
- **6 pre-composited demo products**
- 1 paid sample order with order lines and wallet entries

Run standalone:
```bash
php artisan db:seed --class=DemoSeeder
```

To reset everything fresh:
```bash
php artisan migrate:fresh --seed
```

---

## Configuration

| Key | Purpose |
|---|---|
| `config/shipping.php` | Malaysia state-based delivery rates and weight tiers |
| `config/services.php` | Google OAuth client credentials |
| `.env` | Database, mail, and session drivers |

---

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Artist | `seller@demo.com` | `password` |
| Customer | `customer@demo.com` | `password` |

---

## Demo Flow

1. Open `http://localhost:8000/explore`
2. Browse products or search from the top bar
3. Click a product, select size and color, and click **Add to Cart** or **Buy Now**
4. If buying now as a guest, fill in delivery details on the checkout page
5. Click ** Pay Now** — the mocked payment creates a paid order, wallet commissions, and clears the cart
6. Log in as the seller to manage products, view sales, and track wallet earnings

---

## Notes

- The **Billplz** payment routes are preserved for legacy link compatibility, but the system uses a local demo payment controller that completes orders instantly.
- The `products` table stores new mockups via `image_front_path` / `image_back_path` (storage file paths). Legacy base64 columns remain for backward compatibility with older records.
- This project is a **portfolio/demo piece** and is not configured for production deployment.

---

## Coded by [SuPatee](https://github.com/supatee)
