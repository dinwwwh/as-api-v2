<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('cost')->nullable(); // To help creator know profit money when analysis
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('tax'); // Tax for creator not buyer

            $table->unsignedBigInteger('bought_at_price')->nullable(); // sub tax = real received mony for creator
            $table->timestamp('bought_at')->nullable();
            $table->foreignId('buyer_id')->nullable()->constrained('users', 'id')->onDelete('set null');

            // Time buyer confirmed that account is oke
            // If has buyer that this field is null => account is not oke
            $table->timestamp('confirmed_at')->nullable();

            // Time app pay money for creator
            $table->timestamp('paid_at')->nullable();

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
        Schema::dropIfExists('accounts');
    }
}
