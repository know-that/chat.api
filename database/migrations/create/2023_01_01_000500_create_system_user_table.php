<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSystemUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('system_user', static function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->tinyInteger('type')->default(1)->comment('好友类型：1-公告通知、2-好友请求');
            $table->string('nickname')->default('')->comment("名称");
            $table->string('avatar')->default('')->comment("头像");
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index(['type']);
		});

        DB::statement('ALTER TABLE `system_user` comment "系统用户"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('system_user');
    }

}
