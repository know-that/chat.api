<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('group_chat', static function(Blueprint $table)
        {
            $table->string('id', 32)->primary();
            $table->string('sn', 50)->comment('群号');
            $table->string('nickname', 50)->comment('名称');
            $table->string('avatar')->default('')->comment('头像，关联 file_upload.id');
            $table->string('creator_id')->default('')->comment('创建用户，关联 user.id');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');

            $table->unique(['sn']);
        });

        DB::statement('ALTER TABLE `group_chat` comment "群聊表"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_chat');
    }
};
