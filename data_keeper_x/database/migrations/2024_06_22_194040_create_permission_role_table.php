<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->bigInteger('permission_id');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
