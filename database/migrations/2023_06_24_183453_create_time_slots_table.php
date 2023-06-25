<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBiginteger('service_id');
            $table->string('monday_starttime')->nullable();
			$table->string('monday_endtime')->nullable();
			$table->string('tuesday_starttime')->nullable();
			$table->string('tuesday_endtime')->nullable();
			$table->string('wednesday_starttime')->nullable();
			$table->string('wednesday_endtime')->nullable();
			$table->string('thursday_starttime')->nullable();
			$table->string('thursday_endtime')->nullable();
			$table->string('friday_starttime')->nullable();
			$table->string('friday_endtime')->nullable();
			$table->string('saturday_starttime')->nullable();
			$table->string('saturday_endtime')->nullable();
			$table->string('sunday_starttime')->nullable();
			$table->string('sunday_endtime')->nullable();

			$table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
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
        Schema::dropIfExists('time_slots');
    }
}
