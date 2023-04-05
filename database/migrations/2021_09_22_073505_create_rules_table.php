<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->string('key', 36)->primary();
            $table->string('name');
            $table->string('description');

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('rulables', function (Blueprint $table) {
            $table->string('rule_key', 36);
            $table->foreign('rule_key')
                ->references('key')
                ->on('rules')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->morphs('rulable');
            $table->timestamps();

            $table->primary(['rulable_id', 'rulable_type', 'rule_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rulables');
        Schema::dropIfExists('rules');
    }
}
