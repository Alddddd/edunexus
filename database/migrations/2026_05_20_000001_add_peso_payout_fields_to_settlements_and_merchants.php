<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_profiles', function (Blueprint $table) {
            $table->string('payout_account_name')->nullable()->after('address');
            $table->string('payout_account_number')->nullable()->after('payout_account_name');
            $table->string('payout_qr')->nullable()->after('payout_account_number');
            $table->text('payout_notes')->nullable()->after('payout_qr');
        });

        Schema::table('settlements', function (Blueprint $table) {
            $table->decimal('total_released', 12, 2)->default(0)->after('amount');
            $table->decimal('remaining_balance', 12, 2)->default(0)->after('total_released');
            $table->dateTime('last_released_at')->nullable()->after('settled_at');
        });

        DB::table('settlements')
            ->where('status', 'Settled')
            ->update([
                'status' => 'Released',
                'total_released' => DB::raw('amount'),
                'remaining_balance' => 0,
                'last_released_at' => DB::raw('settled_at'),
            ]);

        DB::table('settlements')
            ->where('status', 'Pending')
            ->update([
                'total_released' => 0,
                'remaining_balance' => DB::raw('amount'),
            ]);
    }

    public function down(): void
    {
        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn([
                'total_released',
                'remaining_balance',
                'last_released_at',
            ]);
        });

        Schema::table('merchant_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'payout_account_name',
                'payout_account_number',
                'payout_qr',
                'payout_notes',
            ]);
        });
    }
};
