<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Store;
use App\Models\Branch;
use Illuminate\Http\Request;

class InventoryController
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
                'quantity' => $product->quantity ?? 0,
                'remaining_stock' => $product->quantity ?? 0,
                'branch_name' => $product->store->branch->name ?? 'N/A',
                'store_name' => $product->store->name ?? 'N/A',
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json($products);
    }

    public function storeInventory(Request $request, string $storeId)
    {
        $store = Store::findOrFail($storeId);
        
        if ($request->user()->role->name === 'store_manager' && $request->user()->store_id != $storeId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        } elseif ($request->user()->role->name === 'branch_manager' && $store->branch_id != $request->user()->branch_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $products = $store->products()->get()->map(function ($product) use ($store) {
            return [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku ?? 'N/A',
                'quantity' => $product->quantity ?? 0,
                'unit_price' => $product->unit_price ?? 0,
                'retail_price' => $product->retail_price ?? 0,
                'store_name' => $store->name,
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json($products);
    }

    public function branchInventory(Request $request, string $branchId)
    {
        $branch = Branch::findOrFail($branchId);

        if ($request->user()->role->name !== 'admin' && $branch->id != $request->user()->branch_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $products = Product::whereHas('store', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->with('store')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku ?? 'N/A',
                'quantity' => $product->quantity ?? 0,
                'store_name' => $product->store->name,
                'unit_price' => $product->unit_price ?? 0,
                'retail_price' => $product->retail_price ?? 0,
            ];
        });

        return response()->json($products);
    }
}
