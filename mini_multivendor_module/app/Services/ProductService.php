<?php

namespace App\Services;

use App\Events\ProductCreated;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\ProductApproved;
use App\Notifications\ProductRejected;

class ProductService
{
    /** List products for the authenticated vendor */
    public function listForVendor(int $perPage = 10): LengthAwarePaginator
    {
        $userId = Auth::id();
        return Product::ownedBy($userId)->latest()->paginate($perPage);
    }

    /** List products by status (admin) */
    public function listByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return Product::where('status', $status)->latest()->paginate($perPage);
    }

    /** Create a product for a given user and dispatch event */
    public function create(array $data, int $userId): Product
    {
        return DB::transaction(function () use ($data, $userId) {
            $product = new Product();
            $product->fill([
                'user_id'     => $userId,
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'] ?? 0,
                'status'      => Product::STATUS_PENDING,
                'code'        => generateProductCode(),
            ]);
            $product->save();

            // Event → queued notification to admins
            ProductCreated::dispatch($product);

            return $product;
        });
    }

    /** Update product (vendor) */
    public function update(Product $product, array $data): Product
    {
        $product->fill([
            'name'        => $data['name']        ?? $product->name,
            'description' => $data['description'] ?? $product->description,
            'price'       => $data['price']       ?? $product->price,
        ]);
        $product->save();

        return $product;
    }

    /** Soft delete (vendor) */
    public function delete(Product $product): void
    {
        $product->delete();
    }

    /** Admin actions */
    public function approve(Product $product): Product
    {
        $product->status = Product::STATUS_APPROVED;
        $product->save();

        // Notify the vendor (queued)
        $product->user->notify(new ProductApproved($product));

        return $product;
    }

    public function reject(Product $product): Product
    {
        $product->status = Product::STATUS_REJECTED;
        $product->save();

        // Notify the vendor (queued)
        $product->user->notify(new ProductRejected($product));

        return $product;
    }
}
