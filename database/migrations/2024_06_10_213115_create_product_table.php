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
        Schema::disableForeignKeyConstraints();

        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->text('description')->nullable();
            $table->bigInteger('qty');
            $table->decimal('price', 8, 2);
            $table->foreignId('category_id')->constrained('category')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('sub_category')->onDelete('set null');
            $table->longText('featured_image')->nullable();
            $table->bigInteger('rank')->default(0);
            $table->string('status')->default('available');
            $table->timestamps();
            $table->index(['category_id', 'subcategory_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
