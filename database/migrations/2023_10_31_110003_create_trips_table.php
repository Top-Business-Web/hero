<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['complete', 'new']);
            $table->enum('trip_type', ['scheduled','normal','quick'])->default('normal');
            $table->text('from_address');
            $table->bigInteger('from_long');
            $table->bigInteger('from_lat');
            $table->text('to_address');
            $table->bigInteger('to_long');
            $table->bigInteger('to_lat');
            $table->time('time_ride')->nullable();
            $table->time('time_arrive')->nullable();
            $table->bigInteger('distance')->nullable();
            $table->string('time')->nullable();
            $table->double('price', 10, 2)->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->boolean('ended')->default(false);
            $table->foreign('driver_id')
                ->on('users')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('user_id')
                ->on('users')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();


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
        Schema::dropIfExists('trips');
    }
}
