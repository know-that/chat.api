<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFriendTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('friend', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->char('user_id')->comment('用户编号，关联 user.id');
            $table->string('friend_type')->default(1)->comment('好友类型：user-用户、group_chat-群聊、system-系统通知');
            $table->string('friend_id', 32)->comment("好友编号，如 user.id");
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique(['user_id', 'friend_type', 'friend_id']);
		});

        DB::statement('ALTER TABLE `friend` comment "好友表"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('friend');
    }

}
