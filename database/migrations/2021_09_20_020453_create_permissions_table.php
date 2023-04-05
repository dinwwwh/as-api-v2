<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->string('key', 36)->primary();
            $table->string('name');
            $table->string('description');

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('permissibles', function (Blueprint $table) {
            $table->string('permission_key', 36);
            $table->foreign('permission_key')
                ->references('key')
                ->on('permissions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->morphs('permissible');
            $table->timestamps();

            $table->primary(['permission_key', 'permissible_id', 'permissible_type'], 'constrained_permissible_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissibles');
        Schema::dropIfExists('permissions');
    }
}
