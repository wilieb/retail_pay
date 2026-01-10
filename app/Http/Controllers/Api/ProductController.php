<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    public function index(Request $request)
    {
        $query = Product::with('store.branch');

        if ($request->user()->role->name === 'branch_manager') {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            });
        } elseif ($request->user()->role->name === 'store_manager') {
            $query->where('store_id', $request->user()->store_id);
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku ?? 'N/A',
                'stock' => $product->quantity ?? 0,
                'unit_price' => $product->unit_price ?? 0,
                'retail_price' => $product->retail_price ?? 0,
                'category' => $product->category ?? 'N/A',
                'branch_name' => $product->store->branch->name ?? 'N/A',
                'store_name' => $product->store->name ?? 'N/A',
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'store_id' => 'required|exists:stores,id',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'category' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json($product->load('store.branch'), 201);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'quantity' => 'sometimes|integer|min:0',
            'unit_price' => 'sometimes|numeric|min:0',
            'retail_price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json($product->load('store.branch'));
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
