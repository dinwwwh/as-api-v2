<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->string('slug', 36)->primary();
            $table->string('name', 36);
            $table->string('description')->nullable();
            $table->unsignedInteger('type')->nullable();

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::table('tags',  function (Blueprint $table) {
            $table->string('parent_slug', 36)->nullable();
            $table->foreign('parent_slug')
                ->references('slug')
                ->on('tags')
                ->onDelete('cascade')
                ->onUpdate('set null');
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->string('tag_slug', 36);
            $table->foreign('tag_slug')
                ->references('slug')
                ->on('tags')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->morphs('taggable');
            $table->timestamps();

            $table->primary(['taggable_id', 'taggable_type', 'tag_slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
}
