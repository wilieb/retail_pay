<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionNumberGenerator
{
    public static function generate(string $type): string
    {
        $prefixMap = [
            'sales'     => 'SLS',
            'transfers' => 'TRN',
        ];

        if (!isset($prefixMap[$type])) {
            throw new \InvalidArgumentException('Invalid transaction type');
        }

        $year = now()->format('y');
        $prefix = $prefixMap[$type] . $year;

        $lastTransaction = Transaction::where('transaction_type', $type)
            ->where('transaction_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_id, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
