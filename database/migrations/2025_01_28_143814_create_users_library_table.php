<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users_library', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('cpf', 11)->unique();
            $table->string('email')->unique();
            $table->char('contact', 20);
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_library');
    }
};
