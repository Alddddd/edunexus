<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {

            $table->boolean('is_claimed')->default(false);

            $table->dateTime('claimed_at')->nullable();

            $table->foreignId('claimed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('claim_status')
                ->default('Unclaimed');

        });
    }

    public function down(): void
    {
        Schema::table('assistance_requests', function (Blueprint $table) {

            $table->dropColumn([
                'is_claimed',
                'claimed_at',
                'claim_status',
            ]);

            $table->dropConstrainedForeignId('claimed_by');

        });
    }
};