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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('validators');
    }
}
