<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlannedOffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planned_offs', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBiginteger('service_id');
            $table->string('reason')->nullable();
			$table->date('date');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->tinyInteger('is_fullday')->nullable();

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
        Schema::dropIfExists('planned_offs');
    }
}
