<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('insurance_type')->nullable();
            $table->string('province_territory')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('sex')->nullable();
            $table->decimal('desired_amount', 15, 2)->nullable();
            $table->integer('length_coverage')->nullable();
            $table->integer('mortgage_amortization')->nullable();
            $table->string('length_payment')->nullable();
            $table->enum('health_class', ['Average', 'Good', 'Excellent'])->nullable();
            $table->boolean('tobacco_use')->default(false);
            $table->string('journey')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
