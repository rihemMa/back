<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->integer('total_ttc');
            $table->integer('ht_price');
            $table->integer('rate_tva');
            $table->integer('price_tva');
            $table->integer('fiscal_timber');
            $table->string('billNum');
            $table->string('inWord')->nullable();
            $table->string('description')->nullable();
            $table->string('bill_file')->nullable();
            $table->timestamp('DateFacturation');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('SET NULL');
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
        Schema::dropIfExists('bills');
    }
}
