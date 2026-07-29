<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductTemplate;
use App\Models\User;
use App\Models\Wallet;
use App\Services\MockupGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'artist']);
        Role::firstOrCreate(['name' => 'customer']);

        $artist = User::updateOrCreate(
            ['email' => 'seller@demo.com'],
            [
                'name' => 'DemoSeller',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => 2,
                'phone' => '123456789',
                'address' => '12 Demo Street',
                'postcode' => '50000',
                'state' => 'Selangor',
            ]
        );
        $artist->assignRole('artist');

        Artist::updateOrCreate(['id' => $artist->id], ['id' => $artist->id]);
        Wallet::updateOrCreate(
            ['user_id' => $artist->id],
            ['id' => (string) Str::uuid(), 'name' => $artist->name, 'commission' => 0, 'balance' => 0, 'status' => 1]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@demo.com'],
            [
                'name' => 'DemoCustomer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => 3,
                'phone' => '123456789',
                'address' => '88 Checkout Avenue',
                'postcode' => '50000',
                'state' => 'Selangor',
            ]
        );
        $customer->assignRole('customer');

        $this->makeAssets();

        $shirt = ProductTemplate::updateOrCreate(
            ['category' => 'Shirt'],
            [
                'commission' => 20,
                'mockup_image' => 'demo-shirt-front.png',
                'mockup_image_2' => 'demo-shirt-back.png',
                'status' => 1,
                'min' => 35,
                'color' => 'White,Gray,Black',
            ]
        );

        $oversized = ProductTemplate::updateOrCreate(
            ['category' => 'Oversized'],
            [
                'commission' => 20,
                'mockup_image' => 'demo-oversized-front.png',
                'mockup_image_2' => 'demo-oversized-back.png',
                'status' => 1,
                'min' => 50,
                'color' => 'White,Black',
            ]
        );

        $generator = extension_loaded('gd') ? app(MockupGenerator::class) : null;
        $products = [
            ['Solar Bloom Tee', 'sun, flower, warm', $shirt, 'demo-design-sun.png', 'White', 42, ['x' => 240, 'y' => 208, 'w' => 405, 'h' => 525]],
            ['Midnight Signal Tee', 'moon, signal, night', $shirt, 'demo-design-moon.png', 'Black', 44, ['x' => 240, 'y' => 208, 'w' => 405, 'h' => 525]],
            ['Quiet Wave Tee', 'wave, ocean, blue', $shirt, 'demo-design-wave.png', 'Gray', 39, ['x' => 240, 'y' => 208, 'w' => 405, 'h' => 525]],
            ['Garden Glyph Tee', 'garden, glyph, green', $shirt, 'demo-design-leaf.png', 'White', 41, ['x' => 240, 'y' => 208, 'w' => 405, 'h' => 525]],
            ['Oversized Solar', 'oversized, sun, streetwear', $oversized, 'demo-design-sun.png', 'White', 59, ['x' => 270, 'y' => 208, 'w' => 360, 'h' => 525]],
            ['Oversized Wave', 'oversized, ocean, graphic', $oversized, 'demo-design-wave.png', 'Black', 62, ['x' => 270, 'y' => 208, 'w' => 360, 'h' => 525]],
        ];

        $created = collect($products)->map(function ($item, $index) use ($artist, $generator) {
            [$title, $tags, $template, $design, $color, $price, $position] = $item;
            $path = $generator
                ? $generator->generate('demo-designs/'.$design, $template->mockup_image, $position, $color)
                : 'products/demo-product-'.($index + 1).'.svg';

            return Product::updateOrCreate(
                ['title' => $title, 'shopname' => $artist->name],
                [
                    'slug' => Str::slug($title).'-'.substr(md5($title), 0, 8),
                    'tags' => $tags,
                    'artist_id' => $artist->id,
                    'collection_id' => null,
                    'price' => $price,
                    'discount' => 1,
                    'commission' => round($price * 0.2, 2),
                    'color' => $template->color,
                    'category' => $template->category,
                    'image_front' => $design,
                    'image_front_path' => $path,
                    'image_back' => null,
                    'image_back_path' => null,
                    'product_image_path' => $path,
                    'product_image_2_path' => null,
                    'preview' => 0,
                    'status' => 1,
                ]
            );
        });

        $order = Order::updateOrCreate(
            ['id' => '00000000-0000-4000-8000-000000000001'],
            [
                'collection_id' => 'demo',
                'email' => $customer->email,
                'name' => $customer->name,
                'description' => 'Demo paid order',
                'delivery' => 10,
                'status' => 1,
                'amount' => 94,
                'paid' => 'true',
                'paid_at' => Carbon::now(),
                'address' => $customer->address,
                'postcode' => $customer->postcode,
                'state' => $customer->state,
                'phone' => $customer->phone,
            ]
        );

        foreach ($created->take(2) as $product) {
            $line = ProductOrder::firstOrNew(['billplz_id' => $order->id, 'product_id' => $product->id]);
            $line->fill([
                'id' => $line->exists ? $line->id : (string) Str::uuid(),
                'title' => $product->title,
                'price' => $product->price,
                'quantity' => 1,
                'size' => 'M',
                'color' => 'White',
            ])->save();
        }
    }

    private function makeAssets(): void
    {
        Storage::disk('public')->makeDirectory('mockup-image');
        Storage::disk('public')->makeDirectory('demo-designs');
        Storage::disk('public')->makeDirectory('products');

        if (extension_loaded('gd')) {
            $this->template('demo-shirt-front.png', 230, 130, 420, 690);
            $this->template('demo-shirt-back.png', 230, 120, 420, 700);
            $this->template('demo-oversized-front.png', 190, 115, 500, 720);
            $this->template('demo-oversized-back.png', 190, 100, 500, 730);
            $this->design('demo-design-sun.png', '#f59e0b', '#ef4444');
            $this->design('demo-design-moon.png', '#60a5fa', '#a78bfa');
            $this->design('demo-design-wave.png', '#22d3ee', '#2563eb');
            $this->design('demo-design-leaf.png', '#84cc16', '#10b981');

            return;
        }

        foreach (['demo-shirt-front.png', 'demo-shirt-back.png', 'demo-oversized-front.png', 'demo-oversized-back.png'] as $name) {
            Storage::disk('public')->put('mockup-image/'.$name, base64_decode($this->blankPng()));
        }

        foreach (['demo-design-sun.png', 'demo-design-moon.png', 'demo-design-wave.png', 'demo-design-leaf.png'] as $name) {
            Storage::disk('public')->put('demo-designs/'.$name, base64_decode($this->blankPng()));
        }

        foreach (range(1, 6) as $number) {
            Storage::disk('public')->put('products/demo-product-'.$number.'.svg', $this->fallbackProductSvg($number));
        }
    }

    private function template(string $name, int $x, int $y, int $w, int $h): void
    {
        $image = Image::canvas(880, 900, 'rgba(255,255,255,0)');
        $image->rectangle($x, $y, $x + $w, $y + $h, function ($draw) {
            $draw->background('rgba(255,255,255,0.18)');
            $draw->border(8, 'rgba(255,255,255,0.45)');
        });
        $image->rectangle($x + 110, $y, $x + $w - 110, $y + 75, function ($draw) {
            $draw->background('rgba(0,0,0,0.12)');
        });
        Storage::disk('public')->put('mockup-image/'.$name, (string) $image->encode('png'));
    }

    private function design(string $name, string $primary, string $secondary): void
    {
        $image = Image::canvas(600, 600, 'rgba(255,255,255,0)');
        $image->circle(260, 300, 270, function ($draw) use ($primary) {
            $draw->background($primary);
        });
        $image->rectangle(185, 350, 415, 420, function ($draw) use ($secondary) {
            $draw->background($secondary);
        });
        $image->circle(90, 230, 185, function ($draw) use ($secondary) {
            $draw->background($secondary);
        });
        Storage::disk('public')->put('demo-designs/'.$name, (string) $image->encode('png'));
    }

    private function blankPng(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';
    }

    private function fallbackProductSvg(int $number): string
    {
        $colors = [
            ['#ffffff', '#f59e0b', '#ef4444'],
            ['#111111', '#60a5fa', '#a78bfa'],
            ['#808080', '#22d3ee', '#2563eb'],
            ['#ffffff', '#84cc16', '#10b981'],
            ['#ffffff', '#f59e0b', '#ef4444'],
            ['#111111', '#22d3ee', '#2563eb'],
        ][$number - 1];

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="880" height="900" viewBox="0 0 880 900">
  <rect width="880" height="900" fill="#18181b"/>
  <rect x="210" y="110" width="460" height="700" rx="42" fill="{$colors[0]}" stroke="#d4d4d8" stroke-width="8"/>
  <rect x="320" y="110" width="240" height="80" rx="36" fill="#000000" opacity=".12"/>
  <circle cx="440" cy="410" r="110" fill="{$colors[1]}"/>
  <rect x="335" y="510" width="210" height="70" rx="18" fill="{$colors[2]}"/>
</svg>
SVG;
    }
}
