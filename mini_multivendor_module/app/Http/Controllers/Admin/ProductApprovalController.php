<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;

class ProductApprovalController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function pending()
    {
        $products = $this->service->listByStatus(Product::STATUS_PENDING);
        return view('products.pending', compact('products'));
    }

    public function approve(Product $product)
    {
        $this->service->approve($product);
        return back()->with('success', 'Product approved.');
    }

    public function reject(Product $product)
    {
        $this->service->reject($product);
        return back()->with('success', 'Product rejected.');
    }
}
