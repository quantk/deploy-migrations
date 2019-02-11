<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeployMigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deploy_migrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->dateTime('created_at');
        });

        Schema::create('deploy_migrations_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->json('output')->nullable();
            $table->json('error')->nullable();
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deploy_migrations');
        Schema::dropIfExists('deploy_migrations_info');
    }
}
