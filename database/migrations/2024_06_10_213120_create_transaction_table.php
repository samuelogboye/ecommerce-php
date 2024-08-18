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

        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained()->onDelete('cascade');
            $table->longText('payment_ref');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2); // Specified precision and scale
            $table->string('status');
            $table->timestamps();

            // Indexing foreign key and payment reference
            $table->index(['order_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
