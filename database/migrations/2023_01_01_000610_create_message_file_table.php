<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMessageFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('message_file', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('file_id')->comment('附件编号：关联 upload.id');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读：0-否、1-是');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
		});

        DB::statement('ALTER TABLE `message_file` comment "附件聊天消息"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('message_file');
    }

}
