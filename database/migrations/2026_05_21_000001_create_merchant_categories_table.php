<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        $now = now();
        $categoryNames = collect()
            ->merge(DB::table('assistance_programs')->whereNotNull('merchant_category')->pluck('merchant_category'))
            ->merge(DB::table('merchant_profiles')->whereNotNull('merchant_category')->pluck('merchant_category'))
            ->filter()
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique(fn ($name) => Str::lower($name))
            ->values();

        foreach ($categoryNames as $name) {
            DB::table('merchant_categories')->insertOrIgnore([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => null,
                'status' => 'Active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('merchant_profiles', function (Blueprint $table) {
            $table->foreignId('merchant_category_id')
                ->nullable()
                ->after('merchant_category')
                ->constrained('merchant_categories')
                ->nullOnDelete();
        });

        Schema::table('assistance_programs', function (Blueprint $table) {
            $table->foreignId('merchant_category_id')
                ->nullable()
                ->after('merchant_category')
                ->constrained('merchant_categories')
                ->nullOnDelete();
        });

        DB::table('merchant_profiles')
            ->join('merchant_categories', 'merchant_categories.name', '=', 'merchant_profiles.merchant_category')
            ->update(['merchant_profiles.merchant_category_id' => DB::raw('merchant_categories.id')]);

        DB::table('assistance_programs')
            ->join('merchant_categories', 'merchant_categories.name', '=', 'assistance_programs.merchant_category')
            ->update(['assistance_programs.merchant_category_id' => DB::raw('merchant_categories.id')]);
    }

    public function down(): void
    {
        Schema::table('assistance_programs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('merchant_category_id');
        });

        Schema::table('merchant_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('merchant_category_id');
        });

        Schema::dropIfExists('merchant_categories');
    }
};
