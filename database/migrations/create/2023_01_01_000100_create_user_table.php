<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
		Schema::create('user', static function(Blueprint $table)
		{
			$table->string('id', 32);
			$table->string('account', 20)->default('')->comment('账号');
			$table->string('password', 64)->default('')->comment('密码');
            $table->string('name', 20)->default('')->comment('姓名');
            $table->string('nickname', 20)->default('')->comment('昵称');
			$table->string('avatar')->default('')->comment('头像，关联 file_upload.id');
            $table->tinyInteger('gender')->default(0)->comment('性别，0-女 1-男');
			$table->tinyInteger('is_muted')->default(0)->comment('是否被禁言：0-否，1-是');
			$table->tinyInteger('is_banned')->default(0)->comment('是否被封号：0-否，1-是');
            $table->dateTime('active_at')->useCurrent()->useCurrentOnUpdate()->comment('最后一次活跃时间');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('修改时间');

            $table->index('account');
		});

        DB::statement('ALTER TABLE `user` comment "用户表"');
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
    {
        Schema::dropIfExists('user');
    }

}
