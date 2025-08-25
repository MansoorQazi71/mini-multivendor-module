<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}

    /** Vendor: list only own products */
    public function index()
    {
        $products = $this->service->listForVendor();
        return view('products.index', compact('products'));
    }

    /** Vendor: show create form */
    public function create()
    {
        return view('products.create');
    }

    /** Vendor: store product (goes pending, fires event) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
        ]);

        $product = $this->service->create($validated, $request->user()->id);

        return redirect()->route('products.index')
            ->with('success', 'Product submitted for approval (code: '.$product->code.').');
    }

    /** Vendor: edit own product */
    public function edit(Product $product)
    {
        if ($product->user_id !== auth()->id()) abort(403);
        return view('products.edit', compact('product'));
    }

    /** Vendor: update own product */
    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
        ]);

        $this->service->update($product, $validated);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    /** Vendor: delete own product (soft delete) */
    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) abort(403);

        $this->service->delete($product);
        return back()->with('success', 'Product deleted.');
    }
}
