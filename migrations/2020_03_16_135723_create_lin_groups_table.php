<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateLinGroupsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lin_group', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("权限分组主键");
            $table->string("name",50)->nullable(false)->comment("分组名称")->unique();
            $table->string("info",255)->nullable(false)->comment("分组描述信息");
            $table->dateTime('create_time');
            $table->dateTime('update_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lin_group');
    }
}
