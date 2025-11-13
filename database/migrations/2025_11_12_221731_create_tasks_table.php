<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            $table->date('due_date');
            $table->enum('priority', ['baixa', 'média', 'alta'])->default('média');
            $table->enum('status', ['pendente', 'em_andamento', 'concluida'])->default('pendente');
            $table->integer('estimated_hours')->nullable();
            $table->integer('spent_hours')->nullable(false);
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
