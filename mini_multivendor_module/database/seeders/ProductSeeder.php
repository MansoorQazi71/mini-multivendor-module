<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = User::where('role', 'vendor')->first();
        if (! $vendor) return;

        $codes = [];

        $makeCode = function () use (&$codes) {
            do {
                $code = generateProductCode();
            } while (in_array($code, $codes, true));
            $codes[] = $code;
            return $code;
        };

        Product::updateOrCreate(
            ['code' => $makeCode()],
            [
                'user_id' => $vendor->id,
                'name' => 'Vendor Product A',
                'description' => 'Sample product A (pending)',
                'price' => 49.99,
                'status' => 'pending',
            ]
        );

        Product::updateOrCreate(
            ['code' => $makeCode()],
            [
                'user_id' => $vendor->id,
                'name' => 'Vendor Product B',
                'description' => 'Sample product B (approved)',
                'price' => 99.99,
                'status' => 'approved',
            ]
        );
    }
}
