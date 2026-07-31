<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductVariant;
use App\Models\Profile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'user@demo.com'],
            [
                'name' => 'DemoUser',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => '123456789',
                'address' => '12 Demo Street',
                'postcode' => '50000',
                'state' => 'Selangor',
            ]
        );
        Profile::updateOrCreate(['user_id' => $user->id], ['bio' => 'A HopeXito creator.']);
        Wallet::updateOrCreate(
            ['user_id' => $user->id],
            ['id' => (string) Str::uuid(), 'name' => $user->name, 'commission' => 0, 'balance' => 0, 'status' => 1]
        );

        $products = collect([
            ['Solar Shirt', 'sun, warm, graphic', 'shirt'],
            ['Midnight Sweatshirt', 'moon, night, graphic', 'sweat'],
            ['Quiet Hoodie', 'wave, ocean, graphic', 'hoodie'],
        ])->map(function (array $item) use ($user) {
            [$title, $tags, $type] = $item;
            $catalog = config('catalog.types.'.$type);
            $product = Product::updateOrCreate(
                ['title' => $title, 'user_id' => $user->id],
                [
                    'slug' => Str::slug($title).'-'.substr(md5($title), 0, 8),
                    'product_type' => $type,
                    'visibility' => 'public',
                    'tags' => $tags,
                    'price' => $catalog['price'],
                    'commission_rate' => config('catalog.commission_rate'),
                    'status' => 1,
                    'sold' => 0,
                ]
            );

            foreach (config('catalog.colors') as $color) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'color' => $color],
                    [
                        'image_front_path' => 'mockups/'.strtolower($color).'-'.$type.'-front.png',
                        'image_back_path' => 'mockups/'.strtolower($color).'-'.$type.'-back.png',
                    ]
                );
            }

            return $product;
        });

        $order = Order::updateOrCreate(
            ['id' => '00000000-0000-4000-8000-000000000001'],
            [
                'collection_id' => 'demo', 'email' => $user->email, 'name' => $user->name,
                'description' => 'Demo paid order', 'delivery' => 10, 'status' => 1,
                'amount' => 35, 'paid' => 'true', 'paid_at' => now(), 'address' => $user->address,
                'postcode' => $user->postcode, 'state' => $user->state, 'phone' => $user->phone,
            ]
        );
        $product = $products->first();
        ProductOrder::updateOrCreate(
            ['billplz_id' => $order->id, 'product_id' => $product->id],
            [
                'id' => (string) Str::uuid(), 'title' => $product->title, 'price' => $product->price,
                'quantity' => 1, 'size' => 'M', 'color' => 'White', 'is_owner_purchase' => true,
            ]
        );
    }
}
