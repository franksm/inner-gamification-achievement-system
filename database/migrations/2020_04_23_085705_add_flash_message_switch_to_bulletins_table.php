<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlashMessageSwitchToBulletinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulletins', function (Blueprint $table) {
            $table->tinyInteger('flash_message_switch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulletins', function (Blueprint $table) {
            $table->dropColumn('flash_message_switch');
        });
    }
}