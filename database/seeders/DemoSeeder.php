<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoSeeder extends Seeder
{
    private const DEMO_USER_ID = 1;
    private const TEST_USER_ID = 2;
    private const DEMO_WALLET_ID = '62e105f7-fd6f-42d6-a6d5-4f5c10bb9dbc';
    private const TEST_WALLET_ID = '7ff433d2-0920-43a7-840d-ba646d737ba0';

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->clearDemoDatabase();
            $this->seedUsers();
            $this->seedProducts();
            $this->seedPurchases();
        });
    }

    private function clearDemoDatabase(): void
    {
        // The seeder describes the complete local demo database, so rerunning it
        // should restore that state instead of appending another demo checkout.
        foreach ([
            'product_orders',
            'orders',
            'carts',
            'wallet_transactions',
            'wallets',
            'product_variants',
            'products',
            'profiles',
            'searches',
            'temporary_files',
            'password_resets',
            'personal_access_tokens',
            'failed_jobs',
            'users',
        ] as $table) {
            DB::table($table)->delete();
        }
    }

    private function seedUsers(): void
    {
        $password = Hash::make('password');

        DB::table('users')->insert([
            [
                'id' => self::DEMO_USER_ID,
                'name' => 'DemoUser',
                'email' => 'user@demo.com',
                'email_verified_at' => '2026-08-01 14:51:23',
                'password' => $password,
                'google_id' => null,
                'phone' => '123456789',
                'remember_token' => null,
                'address' => '12 Demo Street',
                'postcode' => '50000',
                'state' => 'Selangor',
                'profile_photo_path' => null,
                'created_at' => '2026-08-01 14:47:28',
                'updated_at' => '2026-08-01 14:51:23',
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
            [
                'id' => self::TEST_USER_ID,
                'name' => 'TestUser',
                'email' => 'user@test.com',
                'email_verified_at' => '2026-08-01 14:51:23',
                'password' => $password,
                'google_id' => null,
                'phone' => '123456789',
                'remember_token' => null,
                'address' => '12 Test Street',
                'postcode' => '50000',
                'state' => 'Selangor',
                'profile_photo_path' => null,
                'created_at' => '2026-08-01 14:51:23',
                'updated_at' => '2026-08-01 14:51:23',
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ],
        ]);

        $profileRows = [
            [
                'id' => 1,
                'user_id' => self::DEMO_USER_ID,
                'bio' => 'A demo user.',
                'facebook' => null,
                'twitter' => null,
                'instagram' => null,
                'dribble' => null,
                'behance' => null,
                'pinterest' => null,
                'deviantart' => null,
                'tiktok' => null,
                'website' => null,
                'created_at' => '2026-08-01 14:47:28',
                'updated_at' => '2026-08-01 14:47:28',
            ],
            [
                'id' => 2,
                'user_id' => self::TEST_USER_ID,
                'bio' => 'A tester user.',
                'facebook' => null,
                'twitter' => null,
                'instagram' => null,
                'dribble' => null,
                'behance' => null,
                'pinterest' => null,
                'deviantart' => null,
                'tiktok' => null,
                'website' => null,
                'created_at' => '2026-08-01 14:51:23',
                'updated_at' => '2026-08-01 14:51:23',
            ],
        ];

        if (Schema::hasColumn('profiles', 'cover_image')) {
            $profileRows[0]['cover_image'] = null;
            $profileRows[1]['cover_image'] = null;
        }

        DB::table('profiles')->insert($profileRows);

        DB::table('wallets')->insert([
            [
                'id' => self::DEMO_WALLET_ID,
                'user_id' => self::DEMO_USER_ID,
                'name' => 'DemoUser',
                'bank_holder_name' => null,
                'bank_name' => null,
                'bank_account_number' => null,
                'commission' => 0,
                'balance' => 0,
                'status' => 1,
                'created_at' => '2026-08-01 14:47:28',
                'updated_at' => '2026-08-01 14:47:28',
            ],
            [
                'id' => self::TEST_WALLET_ID,
                'user_id' => self::TEST_USER_ID,
                'name' => 'TestUser',
                'bank_holder_name' => null,
                'bank_name' => null,
                'bank_account_number' => null,
                'commission' => 0,
                'balance' => 0,
                'status' => 1,
                'created_at' => '2026-08-01 14:51:23',
                'updated_at' => '2026-08-01 14:51:23',
            ],
        ]);
    }

    private function seedProducts(): void
    {
        $products = [
            [1, self::DEMO_USER_ID, 'Prod is down', 'rA49dsY9YNk656pb72IUn91OMYx4TQ', 'sweat', 'public', 'CS, IT, Devops', 50, 1, 0, 0, 'Gray', 'front', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [2, self::DEMO_USER_ID, 'To the moon', '95G8hppcndj8x1PcFCjZvF590Th72Z', 'hoodie', 'public', 'Crypto, Btc, Eth', 70, 1, 1, 0, 'Black', 'back', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [3, self::DEMO_USER_ID, 'It works on my machine', '8sGtjfwDPiCrIKL6NwEUzVuRv1UBC8', 'hoodie', 'public', 'CS, IT, Dev', 70, 3, 1, 0, 'Green', 'back', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [4, self::DEMO_USER_ID, '404 Brain not found', 'r7R7ku37vf8tLtUF6zjR0psuz0J3X9', 'sweat', 'public', 'CS, IT, Meme', 50, 1, 0, 0, 'Black', 'front', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [5, self::DEMO_USER_ID, 'HODL', 'pWrQgx6vbH58KSGEyuL65wiqeAwjDu', 'hoodie', 'public', 'Btc, Crypto, Fintech', 70, 1, 0, 0, 'Gray', 'front', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [6, self::DEMO_USER_ID, 'Deploy on Fri', 'dDuOAbssWc1dySTwLWB6KndH4mKxNE', 'sweat', 'public', 'CS, IT, Devops', 50, 1, 1, 0, 'White', 'back', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [7, self::DEMO_USER_ID, 'Shitcoin', 'bNJdVG51IefNHq1I6dFBpNC183Yqa8', 'hoodie', 'public', 'Crypto, Btc, Eth', 70, 1, 1, 0, 'Navy', 'back', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [8, self::DEMO_USER_ID, 'Blockchain decentralized', '83HwIu0Nis5qSDWq0gDfncsZPj551U', 'sweat', 'public', 'Crypto, Btc, Eth', 50, 1, 0, 0, 'White', 'front', '2026-08-01 14:47:28', '2026-08-01 14:47:28'],
            [9, self::TEST_USER_ID, 'AI Art I', 'nzMzQ2P09d1CL3DIBjb8PyEPxvNvGk', 'shirt', 'public', 'art, geometry', 35, 1, 0, 0, 'Black', 'front', '2026-08-01 15:51:35', '2026-08-01 15:51:35'],
            [10, self::TEST_USER_ID, 'AI Art II', 'uS9ahBBu9ySxLGcfvAb8OScsQtmWSw', 'shirt', 'public', 'art, skull', 35, 1, 0, 0, 'Black', 'front', '2026-08-01 16:21:18', '2026-08-01 16:21:18'],
            [11, self::TEST_USER_ID, 'AI Art III', 'ix7kEaBrA0vuW4kTVthiSwYD0oSGI0', 'hoodie', 'public', 'art, geometry', 70, 3, 0, 0, 'Navy', 'front', '2026-08-01 16:32:41', '2026-08-01 18:53:41'],
            [12, self::TEST_USER_ID, 'AI Art IV', 'DToeQTt4ghsaacgnsVxiKycubKPaz2', 'hoodie', 'public', 'art, hand', 70, 1, 0, 0, 'Green', 'front', '2026-08-01 17:34:12', '2026-08-01 17:34:12'],
        ];

        foreach ($products as $product) {
            [$id, $userId, $title, $slug, $type, $visibility, $tags, $price, $status, $preview, $sold, $previewColor, $designSide, $createdAt, $updatedAt] = $product;

            DB::table('products')->insert([
                'id' => $id,
                'user_id' => $userId,
                'title' => $title,
                'slug' => $slug,
                'product_type' => $type,
                'visibility' => $visibility,
                'tags' => $tags,
                'price' => $price,
                'commission_rate' => 0.15,
                'status' => $status,
                'sold' => $sold,
                'preview' => $preview,
                'preview_color' => $previewColor,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            $this->seedVariants($id, $type, $designSide, $createdAt, $updatedAt);
        }
    }

    private function seedVariants(int $productId, string $productType, string $designSide, string $createdAt, string $updatedAt): void
    {
        $colors = config('catalog.types.'.$productType.'.colors', []);
        $variantId = 29 + DB::table('product_variants')->count();

        foreach ($colors as $color) {
            $slug = strtolower($color);
            $front = $designSide === 'front'
                ? "products/{$productId}/{$slug}-front.png"
                : "mockups/{$productType}/{$slug}-{$productType}-front.png";
            $back = $designSide === 'back'
                ? "products/{$productId}/{$slug}-back.png"
                : "mockups/{$productType}/{$slug}-{$productType}-back.png";

            DB::table('product_variants')->insert([
                'id' => $variantId++,
                'product_id' => $productId,
                'color' => $color,
                'image_front_path' => $front,
                'image_back_path' => $back,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }
    }

    private function seedPurchases(): void
    {
        // DemoUser buys three TestUser products and two of their own products.
        $this->seedOrder(
            id: '11111111-1111-4111-8111-111111111111',
            buyerId: self::DEMO_USER_ID,
            itemIds: [9, 10, 11, 1, 2],
            productOrderIds: [
                'a1111111-1111-4111-8111-111111111111',
                'a1111111-1111-4111-8111-111111111112',
                'a1111111-1111-4111-8111-111111111113',
                'a1111111-1111-4111-8111-111111111114',
                'a1111111-1111-4111-8111-111111111115',
            ],
            colors: ['Black', 'Black', 'Navy', 'Gray', 'Black'],
            createdAt: '2026-08-01 19:00:00'
        );

        // TestUser buys five DemoUser products.
        $this->seedOrder(
            id: '22222222-2222-4222-8222-222222222222',
            buyerId: self::TEST_USER_ID,
            itemIds: [1, 2, 3, 4, 5],
            productOrderIds: [
                'b2222222-2222-4222-8222-222222222221',
                'b2222222-2222-4222-8222-222222222222',
                'b2222222-2222-4222-8222-222222222223',
                'b2222222-2222-4222-8222-222222222224',
                'b2222222-2222-4222-8222-222222222225',
            ],
            colors: ['Gray', 'Black', 'Green', 'Black', 'Gray'],
            createdAt: '2026-08-01 19:05:00'
        );
    }

    private function seedOrder(string $id, int $buyerId, array $itemIds, array $productOrderIds, array $colors, string $createdAt): void
    {
        $buyer = DB::table('users')->where('id', $buyerId)->first();
        $products = DB::table('products')->whereIn('id', $itemIds)->get()->keyBy('id');
        $subtotal = 0.0;

        foreach ($itemIds as $productId) {
            $product = $products[$productId];
            $ownerPurchase = (int) $product->user_id === $buyerId;
            $subtotal += (float) $product->price * ($ownerPurchase ? 0.85 : 1);
        }

        DB::table('orders')->insert([
            'id' => $id,
            'email' => $buyer->email,
            'name' => $buyer->name,
            'description' => 'Demo payment completed',
            'delivery' => 7.00,
            'status' => 1,
            'amount' => round($subtotal + 7.00, 2),
            'tracking_number' => null,
            'paid' => 'true',
            'paid_at' => $createdAt,
            'address' => $buyer->address,
            'postcode' => $buyer->postcode,
            'state' => $buyer->state,
            'phone' => $buyer->phone,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        foreach ($itemIds as $index => $productId) {
            $product = $products[$productId];
            $ownerPurchase = (int) $product->user_id === $buyerId;
            $price = round((float) $product->price * ($ownerPurchase ? 0.85 : 1), 2);

            DB::table('product_orders')->insert([
                'id' => $productOrderIds[$index],
                'billplz_id' => $id,
                'product_id' => $product->id,
                'title' => $product->title,
                'price' => $price,
                'quantity' => 1,
                'size' => 'M',
                'color' => $colors[$index],
                'is_owner_purchase' => $ownerPurchase,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            DB::table('products')->where('id', $product->id)->increment('sold');

            if (! $ownerPurchase) {
                $this->recordCommission($product, $createdAt);
            }
        }
    }

    private function recordCommission(object $product, string $createdAt): void
    {
        $walletId = (int) $product->user_id === self::DEMO_USER_ID
            ? self::DEMO_WALLET_ID
            : self::TEST_WALLET_ID;
        $wallet = DB::table('wallets')->where('id', $walletId)->first();
        $income = round((float) $product->price * (float) $product->commission_rate, 2);
        $newBalance = round((float) $wallet->balance + $income, 2);

        DB::table('wallets')->where('id', $walletId)->update([
            'commission' => $newBalance,
            'balance' => $newBalance,
            'updated_at' => $createdAt,
        ]);

        DB::table('wallet_transactions')->insert([
            'user_id' => $product->user_id,
            'wallet_id' => $walletId,
            'balance' => $wallet->balance,
            'withdrawal' => null,
            'income' => $income,
            'new_balance' => $newBalance,
            'status' => 3,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
