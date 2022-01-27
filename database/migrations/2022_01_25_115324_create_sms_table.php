<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->bigIncrements('id');	
            $table->string('source_address', 25)->nullable();
            $table->string('dst_address', 25)->nullable();
            $table->string('message', 1500)->nullable();
            $table->string('amount', 25)->nullable();
            $table->string('message_id', 255)->nullable();
            $table->string('sent_at', 255)->nullable();
            $table->string('delivered_at', 255)->nullable();
            $table->string('response_desc', 500)->nullable();
            $table->string('response_message', 500)->nullable();

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
        Schema::dropIfExists('sms');
    }
}
