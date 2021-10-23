<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidatorablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validatorables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validator_id')->constrained('validators', 'id');
            $table->morphs('parent');
            $table->integer('order')->nullable();

            /**
             * Contain infos about anything that help `checkable model`
             * handle to provide `readable fields` for validator or help
             * handle infos from `updatable fields` of validator to `checkable model`
             *
             */
            $table->json('mapped_readable_fields');
            $table->json('mapped_updatable_fields');

            /**
             * Determine will validate in which hooks of validatable
             *
             */
            $table->integer('type')->nullable();

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
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
        Schema::dropIfExists('validatorables');
    }
}
