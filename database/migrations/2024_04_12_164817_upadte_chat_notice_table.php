<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_notice', function (Blueprint $table) {
            $table->tinyInteger('is_read')->default(0)->comment('是否已读：0-否、1-是');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_notice', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
