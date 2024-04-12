<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMessageFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
    {
        Schema::table('message_file', function (Blueprint $table) {
            $table->string('type', 20)->after('file_id')->comment("文件类型：image、video、audio、file");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('message_file', function (Blueprint $table) {
            $table->string('type', 20)->after('file_id')->comment("文件类型：jpg、jpeg、png、gif、video、audio、excel、word、pdf、txt、markdown");
        });
    }
}
