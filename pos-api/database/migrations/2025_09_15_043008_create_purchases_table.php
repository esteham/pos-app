<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('reference_no')->nullable();
            $table->dateTime('purchase_date')->default(now());
            $table->decimal('subtotal', 12,2)->default(0);
            $table->decimal('total_vat', 12,2)->default(0);
            $table->decimal('discount', 12,2)->default(0);
            $table->decimal('grand_total', 12,2)->default(0);
            $table->decimal('paid_amount', 12,2)->default(0);
            $table->string('payment_method')->default('CASH');
            $table->string('status')->default('COMPLETED');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};
