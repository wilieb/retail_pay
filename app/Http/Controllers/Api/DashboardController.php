<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;

class DashboardController
{
    public function stats(Request $request)
    {
        $query = Product::query();

        if ($request->user()->role->name === 'branch_manager') {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            });
        } elseif ($request->user()->role->name === 'store_manager') {
            $query->where('store_id', $request->user()->store_id);
        }

        $products = $query->get();

        $totalProducts = $products->count();
        $lowStockItems = $products->where('quantity', '<', 10)->count();
        $inventoryValue = $products->sum(function ($product) {
            return ($product->quantity * $product->unit_price);
        });
        $totalStockUnits = $products->sum('quantity');

        $salesQuery = Transaction::where('transaction_type', 'sales');
        if ($request->user()->role->name === 'branch_manager') {
            $salesQuery->whereHas('fromStore', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            });
        } elseif ($request->user()->role->name === 'store_manager') {
            $salesQuery->where('from_store_id', $request->user()->store_id);
        }
        
        $totalSales = $salesQuery->count();
        $totalRevenue = $salesQuery->sum('amount');

        $transfersQuery = Transfer::where('status', 'pending');
        if ($request->user()->role->name === 'branch_manager') {
            $transfersQuery->whereHas('fromStore', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            });
        } elseif ($request->user()->role->name === 'store_manager') {
            $transfersQuery->where('from_store_id', $request->user()->store_id);
        }

        $pendingTransfers = $transfersQuery->count();

        return response()->json([
            'totalProducts' => $totalProducts,
            'lowStockItems' => $lowStockItems,
            'inventoryValue' => round($inventoryValue, 2),
            'totalStockUnits' => $totalStockUnits,
            'totalSales' => $totalSales,
            'totalRevenue' => round($totalRevenue, 2),
            'pendingTransfers' => $pendingTransfers,
        ]);
    }
}
