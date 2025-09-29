<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                //UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ProductSeeder::class
        ]);

        $orders = Order::factory(50)->create();
        $customers = Customer::factory(30)
            ->recycle($orders)
            ->create();

        //$purchases = Purchase::factory(60)->create();

        $users = User::factory(10)
            //->recycle($purchases)
            ->create();

        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com'
        ]);

        /*
        for ($i=0; $i < 10; $i++) {
            Product::factory()->create([
                'product_code' => IdGenerator::generate([
                    'table' => 'products',
                    'field' => 'product_code',
                    'length' => 4,
                    'prefix' => 'PC'
                ]),
            ]);
        }
        */

    }
}
