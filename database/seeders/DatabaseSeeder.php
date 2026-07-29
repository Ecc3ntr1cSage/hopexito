<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Artist;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = Role::firstOrCreate(['name'=> 'admin']);
        $admin = User::updateOrCreate(['email' => 'admin@hopexito.com'], [
            'name' => 'Admin',
            'password' => bcrypt('181d12b7a9A'),
            'email_verified_at' => now(),
            'role_id' => 1
        ]);
        $admin->assignRole($admin_role);

        $artist_role = Role::firstOrCreate(['name'=> 'artist']);
        $artist = User::updateOrCreate(['email' => 'supatee@gmail.com'], [
            'name' => 'SupaTee',
            'password' => bcrypt('1234567890'),
            'email_verified_at' => now(),
            'role_id' => 2
        ]);

        Wallet::updateOrCreate(['user_id' => $artist->id], [
            'id' => (string) Str::uuid(),
            'name' => $artist->name,
            'commission' => 20,
            'balance' => 20,
            'status' => 1
        ]);

        Artist::updateOrCreate(['id' => $artist->id], ['id' => $artist->id]);

        $artist->assignRole($artist_role);

        $customer_role = Role::firstOrCreate(['name'=> 'customer']);
        $customer = User::updateOrCreate(['email' => 'ilhamghaz@gmail.com'], [
            'name' => 'Nadham',
            'password' => bcrypt('1234567890'),
            'email_verified_at' => now(),
            'role_id' => 3
        ]);
        $customer->assignRole($customer_role);

        $this->call(DemoSeeder::class);

    }
}
