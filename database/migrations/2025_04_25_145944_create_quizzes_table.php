<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id');
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['final', 'placement']);
            $table->integer('duration'); // en minutes
            $table->integer('passing_score')->nullable(); // seulement pour les quiz finaux
            $table->boolean('is_published')->default(false);
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
        Schema::dropIfExists('quizzes');
    }
}
