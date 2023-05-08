<?php

use App\Models\Brand;
use App\Models\User;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Brand::class, 'brand_id')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique()->nullable();
            $table->decimal('qty')->nullable()->default(0);
            $table->string('tags')->nullable();
            $table->string('sizes')->nullable();
            $table->string('colors')->nullable();
            $table->decimal('price');
            $table->decimal('sale_price')->nullable();
            $table->text('short_desc')->nullable();
            $table->text('desc')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_hot')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_offer')->default(0);
            $table->boolean('is_deal')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
