<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageIdentityTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'message_identity_tracking',
            function (Blueprint $table) {
                $table->string('channel');
                $table->string('identity');
                $table->integer('processed_at');
                $table->unique(['channel', 'identity']);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_identity_tracking');
    }
}
