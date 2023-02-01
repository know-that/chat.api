<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateChatNoticeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('chat_notice', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->char('user_id')->comment('用户编号，关联 user.id');
            $table->string('source_type')->default(1)->comment('资源类型：friend_request-好友请求，system_user-系统通知');
            $table->string('source_id', 32)->comment("资源编号，如 user.id");
            $table->string('message_type')->comment('消息类型：message_text-文本消息、message_file-附件消息、message_business_card-名片消息');
            $table->string('message_id')->comment('消息编号');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index(['user_id', 'source_type', 'source_id']);
            $table->index(['user_id', 'message_type', 'message_id']);
		});

        DB::statement('ALTER TABLE `chat_notice` comment "通知表"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('friend_request');
    }

}
