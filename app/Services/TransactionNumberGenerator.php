<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionNumberGenerator
{
    public static function generate(string $type): string
    {
        $prefixMap = [
            'sales'    => 'SLS',
            'transfer' => 'TRN',
        ];

        if (!isset($prefixMap[$type])) {
            throw new \InvalidArgumentException('Invalid transaction type');
        }

        $year = now()->format('y'); 

        $prefix = $prefixMap[$type] . $year;

        $lastTransaction = Transaction::where('type', $type)
            ->where('transaction_id', 'like', $prefix . '%')
            ->orderByDesc('transaction_id')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_id, -3));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
