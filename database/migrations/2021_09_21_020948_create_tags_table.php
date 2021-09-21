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
            $table->uuid('slug')->primary();
            $table->string('name', 36);
            $table->string('description')->nullable();
            $table->unsignedInteger('type')->nullable();

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::table('tags',  function (Blueprint $table) {
            $table->foreignUuid('parent_slug')->nullable()->constrained('tags', 'slug')->onDelete('set null');
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignUuid('tag_slug')->constrained('tags', 'slug')->onDelete('cascade');
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
