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

            $table->enum('type', ['complete', 'new', 'reject']);
            $table->text('from_address');
            $table->bigInteger('from_long');
            $table->bigInteger('from_lat');
            $table->text('to_address');
            $table->bigInteger('to_long');
            $table->bigInteger('to_lat');
            $table->time('time_ride');
            $table->time('time_arrive');
            $table->bigInteger('distance');
            $table->string('time');
            $table->double('price', 10, 2);
            $table->string('name')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')
                ->on('clients')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')
                ->on('clients')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('trips');
    }
}
