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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal',12,2)->default(0);
            $table->decimal('total_vat',12,2)->default(0);
            $table->decimal('discount',12,2)->default(0);
            $table->decimal('grand_total',12,2)->default(0);
            $table->decimal('paid_amount',12,2)->default(0);

            $table->enum('payment_method',['CASH','BKASH','CARD'])->default('CASH');
            $table->enum('status',['PENDING','PAID','CANCELLED'])->default('PAID');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('sales');
    }
};
