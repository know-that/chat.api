<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateChatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('chat', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->string('receiver_user_id', 32)->comment('收信用户编号：关联 user.id');
            $table->string('sender_user_id', 32)->comment('发信用户编号：关联 user.id');
            $table->text('content')->comment('消息内容');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读：0-否、1-是');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');

            $table->index(['receiver_user_id', 'sender_user_id']);
		});

        DB::statement('ALTER TABLE `chat` comment "消息表（单聊）"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('chat');
    }

}
