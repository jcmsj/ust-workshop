<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKanListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kan_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('board_id');
            $table->string('title');
            $table->integer('order')->comment('The order of the list in the board');
            $table->string('marker_color')->comment('CSS color value for the list marker');
            $table->timestamps();

            $table->foreign('board_id')
                  ->references('id')
                  ->on('kan_boards')
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
        Schema::dropIfExists('kan_lists');
    }
}
