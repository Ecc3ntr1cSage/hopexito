# Hopexito

Print-on-demand marketplace demo built with Laravel 11. Users upload artwork, generate garment mockups, publish public designs, and buy products from the catalog through a mocked checkout flow.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-green)](https://livewire.laravel.com)
[![Tailwind](https://img.shields.shields.io/badge/Tailwind-3.1-blue)](https://tailwindcss.com)

---

## Tech Stack

- **PHP 8.2+** / **Laravel 11**
- **Livewire 3** — interactive cart, search, wallet, product management panels
- **Jetstream / Fortify** — authentication, profiles, teams
- **Intervention Image (GD)** — server-side mockup compositing
- **Vite** + **Tailwind CSS 3.1** + **Alpine.js** — frontend tooling
- **Laravel Socialite** — Google OAuth login

---

## Core Features

### User Features
- **Fixed catalog product creation** — upload front artwork, optionally upload back artwork, choose Shirt (RM35), Sweatshirt (RM50), or Hoodie (RM70), and generate white, black, and gray variants
- **Public/private visibility** — publish products to profiles and discovery or keep them private for owner-only purchasing
- **Sales dashboard** — view total items sold, commission earnings, and individual order lines
- **Wallet** — track commission income, manage bank details, and request withdrawals
- **Profile customization** — bio and social links (Facebook, Twitter, Instagram, Dribbble, Behance, Pinterest, DeviantArt, TikTok, website)
- **Order management** — mark orders as received, view full order history

- **Product discovery** — explore feed, filtered Shirt/Sweatshirt/Hoodie shops, and searchable catalog
- **User profiles** — view a user's bio, social links, and public product catalog
- **Cart** — add to cart with size/color variants, adjust quantities, apply discounts
- **Guest checkout** — fill in delivery information without an account
- **Mocked payment flow** — immediately creates a paid order, order lines, wallet commissions, and clears the cart
- **Wishlist** — save favorite products

### System Features
- **Unified account model** — every account can create, publish, purchase, and sell products
- **Commission tracking** — external purchases credit 15% of the fixed product price to the creator wallet; owner purchases create no earnings
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
│   │   ├── StorefrontController.php
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
│   ├── Profile.php
│   ├── Cart.php
│   ├── Inventory.php
│   ├── Order.php
│   ├── Product.php
│   ├── ProductCollection.php
│   ├── ProductOrder.php
│   ├── ProductVariant.php
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
`App\Services\MockupGenerator` composites uploaded designs onto fixed catalog garment mockups using the Intervention Image GD driver. It supports front and optional back designs with catalog-defined positioning.
- Location: `app/Services/MockupGenerator.php`
- Entry point: `ProductsController::store`

### Session-Based Cart for Guests
`App\Services\SessionCart` backed by `App\Facades\SessionCart` provides a full cart lifecycle (add, update, remove, subtotal, destroy) for unauthenticated users via Laravel session storage. Authenticated users persist through the `carts` database table.

### Commission & Wallet Flow
On every successful external payment, a `WalletTransaction` credits 15% of the fixed product price to the product owner's wallet. Owner purchases increment sold quantity but generate no creator earnings.

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
Creates the unified demo user and runs `DemoSeeder`.

### DemoSeeder
Generates the complete demo environment:
- Demo user (`user@demo.com`)
- Shirt, sweatshirt, and hoodie catalog products
- **3 demo products with six color/side variants each**
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

| Account | Email | Password |
|---|---|---|
| User | `user@demo.com` | `password` |

---

## Demo Flow

1. Open `http://localhost:8000/`
2. Browse products or search from the top bar
3. Click a product, select size and color, and click **Add to Cart** or **Buy Now**
4. If buying now as a guest, fill in delivery details on the checkout page
5. Click ** Pay Now** — the mocked payment creates a paid order, wallet commissions, and clears the cart
6. Log in as the demo user to manage products, view sales, and track wallet earnings

---

## Notes

- The **Billplz** payment routes are preserved for legacy link compatibility, but the system uses a local demo payment controller that completes orders instantly.
- The `products` table stores fixed catalog products; `product_variants` stores the generated color and front/back mockups.
- This project is a **portfolio/demo piece** and is not configured for production deployment.

---

## Coded by [SuPatee](https://github.com/supatee)
