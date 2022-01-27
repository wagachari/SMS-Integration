<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_integrations', function (Blueprint $table) {
            $table->bigIncrements('id');	
            $table->string('source_address', 100)->nullable();
            $table->string('dst_address', 15)->nullable();
            $table->string('message', 1500)->nullable();
            $table->string('amount', 25)->nullable();
            $table->string('message_id', 50)->nullable();
            $table->string('response_desc', 50)->nullable();
            $table->string('response_message', 50)->nullable();

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
        Schema::dropIfExists('sms_integrations');
    }
}
