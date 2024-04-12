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
        Schema::create('chat_group', static function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('group_chat_id', 32)->comment('收信群聊编号：关联 group_chat.id');
            $table->string('receiver_user_id', 32)->comment('收信用户编号：关联 user.id');
            $table->string('sender_user_id', 32)->default('')->comment('发信用户编号：关联 user.id');
            $table->string('message_type', 32)->comment('消息类型：message_text-文本消息、message_file-附件消息、message_business_card-名片消息');
            $table->string('message_id', 32)->comment('消息编号');
            $table->tinyInteger('is_system')->default(0)->comment("是否为系统消息：0-否、1-是");
            $table->tinyInteger('is_read')->default(0)->comment('是否已读：0-否、1-是');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');

            $table->index(['group_chat_id', 'sender_user_id']);
            $table->index(['message_type', 'message_id']);
        });

        DB::statement('ALTER TABLE `chat_group` comment "群聊记录表"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_group');
    }
};
