<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductVariant;
use App\Models\Profile;
use App\Models\User;
use App\Models\Wallet;
use App\Support\MockupAssets;
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


    }
}
