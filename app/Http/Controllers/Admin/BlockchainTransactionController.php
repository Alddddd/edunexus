<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockchainTransaction;
use Illuminate\Http\Request;

class BlockchainTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'blockchain_status' => ['nullable', 'string', 'max:255'],
            'transaction_type' => ['nullable', 'string', 'max:255'],
        ]);

        $statusOptions = BlockchainTransaction::query()
            ->whereNotNull('blockchain_status')
            ->distinct()
            ->orderBy('blockchain_status')
            ->pluck('blockchain_status');

        $transactionTypeOptions = BlockchainTransaction::query()
            ->whereNotNull('transaction_type')
            ->distinct()
            ->orderBy('transaction_type')
            ->pluck('transaction_type');

        $stats = [
            'total' => BlockchainTransaction::count(),
            'confirmed' => BlockchainTransaction::where('blockchain_status', 'Confirmed')->count(),
            'pending' => BlockchainTransaction::where('blockchain_status', 'Pending')->count(),
            'failed' => BlockchainTransaction::where('blockchain_status', 'Failed')->count(),
            'with_hash' => BlockchainTransaction::whereNotNull('transaction_hash')->count(),
        ];

        $transactions = BlockchainTransaction::query()
            ->when($filters['blockchain_status'] ?? null, function ($query, $status) {
                $query->where('blockchain_status', $status);
            })
            ->when($filters['transaction_type'] ?? null, function ($query, $type) {
                $query->where('transaction_type', $type);
            })
            ->latest()
            ->latest('id')
            ->paginate(5)
            ->withQueryString();

        return view('admin.blockchain-transactions.index', compact(
            'transactions',
            'filters',
            'statusOptions',
            'transactionTypeOptions',
            'stats'
        ));
    }

    public function confirm(BlockchainTransaction $blockchainTransaction)
    {
        $blockchainTransaction->update([
            'blockchain_status' => 'Confirmed',
        ]);

        return back()->with(
            'success',
            'Blockchain transaction confirmed successfully.'
        );
    }
}
