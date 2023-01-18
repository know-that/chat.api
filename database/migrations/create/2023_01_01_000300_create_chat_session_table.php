<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateChatSessionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('chat_session', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->char('user_id')->comment('用户编号，关联 user.id');
            $table->string('source_type')->default(1)->comment('发送方类型：user-用户、group_chat-群聊、system_user-系统用户');
            $table->string('source_id', 32)->default('')->comment("发送方编号，如 user.id");
            $table->string('last_message_type')->comment("最近一次消息类型：chat-单聊、notice-通知");
            $table->string('last_message_id')->comment("最近一次消息类型id，如：chat.id");
            $table->dateTime('top_at')->nullable()->comment('置顶时间');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique(['user_id', 'source_type', 'source_id']);
		});

        DB::statement('ALTER TABLE `chat_session` comment "会话表"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('chat_session');
    }

}
