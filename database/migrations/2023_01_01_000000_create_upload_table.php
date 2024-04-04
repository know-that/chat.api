<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('upload', static function (Blueprint $table) {
            $table->bigIncrements('id')->comment("编号");
            $table->tinyInteger('from')->default(0)->comment('文件来源：0-本地、1-阿里云OSS');
            $table->string('marker', 50)->comment('文件流唯一标识');
            $table->string('name')->default('')->comment('文件名');
            $table->string('mime', 100)->comment("文件 mime 类型");
            $table->string('suffix', 50)->default('')->comment('文件后缀');
            $table->string('url')->default('')->comment('文件相对路径');
            $table->bigInteger('size')->default(0)->comment('文件大小：单位字节');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');

            $table->unique('marker');
        });

        DB::statement('ALTER TABLE `upload` comment "上传表"');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('upload');
    }
}
