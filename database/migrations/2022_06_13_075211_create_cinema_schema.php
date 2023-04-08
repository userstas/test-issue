<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /** ToDo: Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different showrooms

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('duration')->nullable();
            $table->timestamps();
        });

        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('number_of_seats');
            $table->timestamps();
        });

        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->float('price')->nullable();
            $table->string('currency_code');
            $table->date('start');
            $table->date('finish');
            $table->boolean('is_booked_out')->default(false);

            $table->integer('film_id')->nullable();
            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('set null');

            $table->integer('showroom_id')->nullable();
            $table->foreign('showroom_id')
                ->references('id')
                ->on('showrooms')
                ->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->string('currency_code');
            $table->dateTime('time_of_pay');

            $table->integer('show_id')->nullable();
            $table->foreign('show_id')
                ->references('id')
                ->on('shows')
                ->onDelete('set null');

            $table->timestamps();
        });

        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->float('cost')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_for_couple')->default(false);
            $table->string('percentage_premium')->nullable(); // percents
            $table->float('percentage_premium_multiplier')->nullable(); //value to increase a standard value
            $table->boolean('is_booked')->default(false);

            $table->integer('showroom_id')->nullable();
            $table->foreign('showroom_id')
                ->references('id')
                ->on('showrooms')
                ->onDelete('set null');

            $table->timestamps();
        });

        Schema::create('user_bookings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('booking_time');
            $table->integer('seat_id')->nullable();
            $table->foreign('seat_id')
                ->references('id')
                ->on('seats')
                ->onDelete('set null');

            $table->integer('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->bigInteger('id');

            $table->integer('film_id')->nullable();
            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('set null');

            $table->integer('seat_id')->nullable();
            $table->foreign('seat_id')
                ->references('id')
                ->on('seats')
                ->onDelete('set null');

            $table->integer('show_id')->nullable();
            $table->foreign('show_id')
                ->references('id')
                ->on('shows')
                ->onDelete('set null');

            $table->integer('showroom_id')->nullable();
            $table->foreign('showroom_id')
                ->references('id')
                ->on('showrooms')
                ->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('shows_seats', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('seat_id')->nullable();
            $table->foreign('seat_id')
                ->references('id')
                ->on('seats')
                ->onDelete('set null');

            $table->integer('show_id')->nullable();
            $table->foreign('show_id')
                ->references('id')
                ->on('shows')
                ->onDelete('set null');
            $table->timestamps();
        });

//        throw new \Exception('implement in coding task 4, you can ignore this exception if you are just running the initial migrations.');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows_seats');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('user_bookings');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('pays');
        Schema::dropIfExists('shows');
        Schema::dropIfExists('showrooms');
        Schema::dropIfExists('films');
    }
}
