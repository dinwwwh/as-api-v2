<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargedCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharged_cards', function (Blueprint $table) {
            $table->id();
            $table->string('serial');
            $table->string('code');
            $table->string('telco');
            $table->integer('face_value');
            $table->integer('real_face_value')->nullable();
            $table->integer('received_value')->nullable();
            $table->string('description')->nullable();

            $table->foreignId('approver_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');

            $table->timestamp('paid_at')->nullable(); // paid time to user create card
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
        Schema::dropIfExists('recharged_cards');
    }
}
