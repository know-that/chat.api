<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMessageTextTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('message_text', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->tinyInteger('type')->default(1)->comment('消息类型：1-文字内容、2-富文本内容、3-markdown内容');
            $table->text('content')->comment('消息内容');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读：0-否、1-是');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
		});

        DB::statement('ALTER TABLE `message_text` comment "文本聊天消息"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('message_text');
    }

}
