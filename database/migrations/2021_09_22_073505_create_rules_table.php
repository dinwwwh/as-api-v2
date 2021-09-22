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
            $table->uuid('key')->primary();
            $table->string('name');
            $table->string('description');

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('rulables', function (Blueprint $table) {
            $table->foreignUuid('rule_key')->constrained('rules', 'key')->onDelete('cascade');
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
