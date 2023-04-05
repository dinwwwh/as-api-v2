<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('key', 36)->primary();
            $table->string('name');
            $table->string('description');
            $table->string('color'); // Use for front-end decorate for main color

            $table->foreignId('creator_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updater_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->string('role_key', 36);
            $table->foreign('role_key')
                ->references('key')
                ->on('roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('permission_key', 36);
            $table->foreign('permission_key')
                ->references('key')
                ->on('permissions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();

            $table->primary(['role_key', 'permission_key']);
        });

        Schema::create('rolables', function (Blueprint $table) {
            $table->string('role_key', 36);
            $table->foreign('role_key')
                ->references('key')
                ->on('roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->morphs('rolable');
            $table->timestamps();

            $table->primary(['role_key', 'rolable_id', 'rolable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('rolables');
        Schema::dropIfExists('roles');
    }
}
