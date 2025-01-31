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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_library')->onDelete('cascade');
            $table->date('loan_date');
            $table->date('return_date')->nullable();
            $table->text('observation')->nullable();
            $table->string('status')->default('Empréstimo');
            $table->timestamps();
        });

        Schema::create('book_loan', function (Blueprint $table) {
            $table->foreignId('copy_id')->constrained('copies')->onDelete('cascade');
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->primary(['copy_id', 'loan_id']);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
        Schema::dropIfExists('book_loan');
    }
};
