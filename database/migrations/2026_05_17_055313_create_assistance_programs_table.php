<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('assistance_programs', function (Blueprint $table) {
    $table->id();
    $table->string('program_name');
    $table->text('description')->nullable();
    $table->string('merchant_category');
    $table->decimal('maximum_amount', 12, 2);
    $table->unsignedInteger('expiration_days')->default(30);
    $table->string('status')->default('Active');
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance_programs');
    }
};