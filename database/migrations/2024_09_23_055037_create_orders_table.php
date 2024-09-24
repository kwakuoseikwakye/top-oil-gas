<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('cylinder_id');
            $table->uuid('location_id');
            $table->uuid('weight_id');
            $table->string('status', 100);
            $table->integer('quantity');
            $table->dateTime('date_acquired');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cylinder_id')
                ->references('id')
                ->on('cylinders')
                ->onUpdate('cascade');
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
