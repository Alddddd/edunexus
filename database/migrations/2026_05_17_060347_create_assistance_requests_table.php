<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('assistance_requests', function (Blueprint $table) {
    $table->id();

    $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('program_id')->constrained('assistance_programs')->cascadeOnDelete();

    $table->decimal('requested_amount', 12, 2);
    $table->decimal('approved_amount', 12, 2)->nullable();

    $table->string('status')->default('Pending');

    $table->dateTime('approval_date')->nullable();
    $table->dateTime('expiration_date')->nullable();

    $table->string('reference_code')->nullable()->unique();
    $table->text('qr_code')->nullable();

    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

    $table->text('reason')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistance_requests');
    }
};
