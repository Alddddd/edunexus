<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')
                ->constrained('settlements')
                ->cascadeOnDelete();
            $table->string('settlement_reference')->unique();
            $table->string('payout_type');
            $table->decimal('amount', 12, 2);
            $table->decimal('settlement_total', 12, 2);
            $table->decimal('total_released_after', 12, 2);
            $table->decimal('remaining_balance_after', 12, 2);
            $table->string('payout_channel')->default('GCash/PHP simulation');
            $table->string('settlement_rail')->default('ERC-20-compatible');
            $table->string('network')->default('Morph testnet');
            $table->string('transaction_hash')->nullable();
            $table->string('blockchain_status')->default('Pending');
            $table->string('proof_hash')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('released_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_payouts');
    }
};
