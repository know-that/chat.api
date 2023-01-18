<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNoticeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('notice', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->char('user_id')->comment('用户编号，关联 user.id');
            $table->string('source_type')->default(1)->comment('好友类型：friend_request-好友请求');
            $table->string('source_id', 32)->comment("好友编号，如 user.id");
            $table->string('content')->default('')->comment('消息内容');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读：0-否、1-是');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique(['user_id', 'source_type', 'source_id']);
		});

        DB::statement('ALTER TABLE `friend_request` comment "好友表"');
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
