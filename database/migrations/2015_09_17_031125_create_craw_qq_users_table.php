<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCrawQqUsersTable extends Migration
{
	protected $table = 'craw_qq_users';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->table, function (Blueprint $table) {
			$table->increments('id');
			$table->string('uin')->default('0');
			$table->string('gid')->default('0');
			$table->string('qq')->default('0')->index();
			$table->string('name')->default('');
			$table->integer('status')->default(0);
			$table->integer('count')->default(0);

			$table->index(['status', 'count']);

			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop($this->table);
	}
}
