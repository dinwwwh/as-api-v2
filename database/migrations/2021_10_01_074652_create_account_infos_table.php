<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            // Determine whether account creator can read and update
            // pivot value in this model
            // when account selling
            $table->boolean('can_creator')->default(false);

            // Determine whether account buyer can read
            // pivot value in this model
            // when account bought and pending confirming
            $table->boolean('can_buyer')->default(false);

            // Determine whether account buyer can read
            // pivot value in this model
            // when account bought and confirmed oke
            $table->boolean('can_buyer_oke')->default(false);

            $table->foreignId('account_type_id')->constrained('account_types', 'id')->onDelete('cascade');
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
        Schema::dropIfExists('account_infos');
    }
}
