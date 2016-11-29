<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendables', function (Blueprint $table) {
            $table->increments('id');
            
            $table->boolean('has_date_start');
            $table->boolean('has_date_end');
            $table->boolean('has_time_start');
            $table->boolean('has_time_end');

            $table->integer('attendable_id');
            $table->string('attendable_type');

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
        Schema::dropIfExists('attendables');
    }
}
