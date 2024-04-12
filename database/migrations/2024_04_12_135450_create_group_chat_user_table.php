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
        Schema::create('group_chat_user', function (Blueprint $table) {
            $table->id();
            $table->string('group_chat_id', 32)->comment('群聊编号：关联 group_chat.id');
            $table->string('user_id', 32)->comment('用户编号：关联 user.id');
            $table->tinyInteger('identity')->default(0)->comment('身份：0-成员、1-群主、2-管理员');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_chat_user');
    }
};
