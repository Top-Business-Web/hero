<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsurancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_driver_id')
                ->constrained('insurance_drivers')
                ->cascadeOnDelete();
            $table->string('trans_action_id');
            $table->enum('type', ['google_pay', 'zain_cash', 'visa']);
            $table->integer('amount');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('insurance_payments');
    }
}
