<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use App\Models\Wallet;
use App\Services\MockupGenerator;
use App\Support\MockupAssets;
use App\Support\MockupGeometry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = $this->seedAccount(
            email: 'user@demo.com',
            name: 'DemoUser',
            address: '12 Demo Street',
            bio: 'A demo user.'
        );
        $this->seedAccount(
            email: 'user@test.com',
            name: 'TestUser',
            address: '12 Test Street',
            bio: 'A tester user.'
        );

        $geometry = app(MockupGeometry::class);
        $mockups = app(MockupGenerator::class);

        foreach ($this->products() as $definition) {
            $product = Product::updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'user_id' => $user->id,
                    'title' => $definition['title'],
                    'product_type' => $definition['product_type'],
                    'visibility' => $definition['visibility'],
                    'tags' => $definition['tags'],
                    'price' => config('catalog.types.'.$definition['product_type'].'.price'),
                    'commission_rate' => config('catalog.commission_rate'),
                    'status' => $definition['status'],
                    'sold' => $definition['sold'],
                    'preview' => $definition['preview'],
                    'preview_color' => $definition['preview_color'],
                ]
            );

            $product->variants()->delete();

            foreach (config('catalog.types.'.$definition['product_type'].'.colors', []) as $color) {
                $frontPath = $definition['design_side'] === 'front'
                    ? $this->ensureGeneratedAsset($definition, $color, 'front', $geometry, $mockups)
                    : MockupAssets::path($definition['product_type'], $color, 'front');
                $backPath = $definition['design_side'] === 'back'
                    ? $this->ensureGeneratedAsset($definition, $color, 'back', $geometry, $mockups)
                    : MockupAssets::path($definition['product_type'], $color, 'back');

                $product->variants()->create([
                    'color' => $color,
                    'image_front_path' => $frontPath,
                    'image_back_path' => $backPath,
                ]);
            }
        }
    }

    private function seedAccount(string $email, string $name, string $address, string $bio): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => '123456789',
                'address' => $address,
                'postcode' => '50000',
                'state' => 'Selangor',
            ]
        );

        Profile::updateOrCreate(['user_id' => $user->id], ['bio' => $bio]);
        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'id' => (string) Str::uuid(),
                'name' => $user->name,
                'commission' => 0,
                'balance' => 0,
                'status' => 1,
            ]
        );

        return $user;
    }

    private function ensureGeneratedAsset(
        array $definition,
        string $color,
        string $side,
        MockupGeometry $geometry,
        MockupGenerator $mockups
    ): string {
        $path = sprintf(
            'products/%d/%s-%s.png',
            $definition['media_id'],
            strtolower($color),
            $side
        );

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        $source = public_path('image/hopexito.png');
        if (! is_file($source)) {
            return MockupAssets::path($definition['product_type'], $color, $side);
        }

        return $mockups->generate(
            $source,
            MockupAssets::path($definition['product_type'], $color, $side),
            $geometry->position($definition['product_type'], $side),
            $color,
            $path
        );
    }

    private function products(): array
    {
        return [
            [
                'media_id' => 1,
                'slug' => 'rA49dsY9YNk656pb72IUn91OMYx4TQ',
                'title' => 'Prod is down',
                'product_type' => 'sweat',
                'design_side' => 'front',
                'visibility' => 'public',
                'tags' => 'CS, IT, Devops',
                'status' => 1,
                'sold' => 0,
                'preview' => 0,
                'preview_color' => 'Gray',
            ],
            [
                'media_id' => 2,
                'slug' => '95G8hppcndj8x1PcFCjZvF590Th72Z',
                'title' => 'To the moon',
                'product_type' => 'hoodie',
                'design_side' => 'back',
                'visibility' => 'public',
                'tags' => 'Crypto, Btc, Eth',
                'status' => 1,
                'sold' => 0,
                'preview' => 1,
                'preview_color' => 'Black',
            ],
            [
                'media_id' => 3,
                'slug' => '8sGtjfwDPiCrIKL6NwEUzVuRv1UBC8',
                'title' => 'It works on my machine',
                'product_type' => 'hoodie',
                'design_side' => 'back',
                'visibility' => 'public',
                'tags' => 'CS, IT, Dev',
                'status' => 3,
                'sold' => 0,
                'preview' => 1,
                'preview_color' => 'Green',
            ],
            [
                'media_id' => 4,
                'slug' => 'r7R7ku37vf8tLtUF6zjR0psuz0J3X9',
                'title' => '404 Brain not found',
                'product_type' => 'sweat',
                'design_side' => 'front',
                'visibility' => 'public',
                'tags' => 'CS, IT, Meme',
                'status' => 1,
                'sold' => 0,
                'preview' => 0,
                'preview_color' => 'Black',
            ],
            [
                'media_id' => 5,
                'slug' => 'pWrQgx6vbH58KSGEyuL65wiqeAwjDu',
                'title' => 'HODL',
                'product_type' => 'hoodie',
                'design_side' => 'front',
                'visibility' => 'public',
                'tags' => 'Btc, Crypto, Fintech',
                'status' => 1,
                'sold' => 0,
                'preview' => 0,
                'preview_color' => 'Gray',
            ],
            [
                'media_id' => 6,
                'slug' => 'dDuOAbssWc1dySTwLWB6KndH4mKxNE',
                'title' => 'Deploy on Fri',
                'product_type' => 'sweat',
                'design_side' => 'back',
                'visibility' => 'public',
                'tags' => 'CS, IT, Devops',
                'status' => 1,
                'sold' => 0,
                'preview' => 1,
                'preview_color' => 'White',
            ],
            [
                'media_id' => 7,
                'slug' => 'bNJdVG51IefNHq1I6dFBpNC183Yqa8',
                'title' => 'Shitcoin',
                'product_type' => 'hoodie',
                'design_side' => 'back',
                'visibility' => 'public',
                'tags' => 'Crypto, Btc, Eth',
                'status' => 1,
                'sold' => 0,
                'preview' => 1,
                'preview_color' => 'Navy',
            ],
            [
                'media_id' => 8,
                'slug' => '83HwIu0Nis5qSDWq0gDfncsZPj551U',
                'title' => 'Blockchain decentralized',
                'product_type' => 'sweat',
                'design_side' => 'front',
                'visibility' => 'public',
                'tags' => 'Crypto, Btc, Eth',
                'status' => 1,
                'sold' => 0,
                'preview' => 0,
                'preview_color' => 'White',
            ],
        ];
    }
}
