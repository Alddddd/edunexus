<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settlement_payouts', function (Blueprint $table) {
            $table->string('payout_account_name_used')->nullable()->after('network');
            $table->string('payout_account_number_used')->nullable()->after('payout_account_name_used');
            $table->string('payout_qr_used')->nullable()->after('payout_account_number_used');
            $table->text('payout_notes_used')->nullable()->after('payout_qr_used');
        });
    }

    public function down(): void
    {
        Schema::table('settlement_payouts', function (Blueprint $table) {
            $table->dropColumn([
                'payout_account_name_used',
                'payout_account_number_used',
                'payout_qr_used',
                'payout_notes_used',
            ]);
        });
    }
};
