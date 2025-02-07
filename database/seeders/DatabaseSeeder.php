<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Artist;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Wallet;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = Role::create(['name'=> 'admin']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@hopexito.com',
            'password' => bcrypt('181d12b7a9A'),
            'email_verified_at' => now(),
            'role_id' => 1
        ]);
        $admin->assignRole($admin_role);

        $artist_role = Role::create(['name'=> 'artist']);
        $artist = User::create([
            'name' => 'SupaTee',
            'email' => 'supatee@gmail.com',
            'password' => bcrypt('1234567890'),
            'email_verified_at' => now(),
            'role_id' => 2
        ]);

        Wallet::create([
            'id' => uniqid(8),
            'name' => $artist->name,
            'user_id' => $artist->id,
            'commission' => 20,
            'balance' => 20,
            'status' => 1
        ]);

        Artist::create([
            'id' => $artist->id
        ]);

        $artist->assignRole($artist_role);

        $customer_role = Role::create(['name'=> 'customer']);
        $customer = User::create([
            'name' => 'Nadham',
            'email' => 'ilhamghaz@gmail.com',
            'password' => bcrypt('1234567890'),
            'email_verified_at' => now(),
            'role_id' => 3
        ]);
        $customer->assignRole($customer_role);

    }
}
