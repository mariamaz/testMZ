<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
            Schema::create('shops', function(Blueprint $table) {
                $table->increments('id');
                $table->string('oid');
                $table->string('picture');
                $table->string('name', 100);
                $table->string('email', 100);
                $table->string('city', 100);
                $table->float('latitude');
                $table->float('longitude');

            });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      //  Schema::dropIfExists('shops');
    }
}
