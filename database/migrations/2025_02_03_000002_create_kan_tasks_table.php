<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKanTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kan_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('list_id');
            $table->text('content')->comment('HTML parsable string');
            $table->integer('order')->comment('The order of the task in the list');
            $table->timestamps();

            $table->foreign('list_id')
                  ->references('id')
                  ->on('kan_lists')
                  ->onDelete('cascade');

            $table->archivedAt(); // Macro
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kan_tasks');
    }
}
