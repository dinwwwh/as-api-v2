<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedBigInteger('fee')->default(0); // fee when validate = this validators

            /**
             * Description for approver describe about how to validate
             *
             */
            $table->text('approver_description');

            /**
             * Is array contain ordered fields that the validator need to read
             *
             */
            $table->json('readable_fields');

            /**
             * Is array contain ordered fields that the validator need to update
             *
             */
            $table->json('updatable_fields');

            # For callbackable trait
            $table->json('callback')->nullable();


            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('validatorables', function (Blueprint $table) {
            $table->foreignId('validator_id')->constrained('validators', 'id')->onDelete('cascade');
            $table->morphs('validatorable');
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
        Schema::dropIfExists('validators');
    }
}
