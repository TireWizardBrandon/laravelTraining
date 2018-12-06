<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concerts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string("title");
            $table->string("subtitle");
            $table->dateTime("date");
            $table->integer("ticketPrice");
            $table->string("venue");
            $table->string("address");
            $table->string("city");
            $table->string("state");
            $table->string("zip");
            $table->string("additionalInformation");
            
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concerts');
    }
}
