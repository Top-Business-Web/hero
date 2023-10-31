<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_schedules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date');
            $table->time('time');
            $table->boolean('status')->default(false);

            $table->foreign('user_id')
                ->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('trip_id')
                ->on('trips')->references('id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_schedules');
    }
}
