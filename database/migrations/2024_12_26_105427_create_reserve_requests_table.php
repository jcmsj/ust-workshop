<?php

use App\Models\Reserve;
use App\Models\ReserveRequest;
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
        Schema::create('reserve_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('count');
            $table->enum('status', [
                ReserveRequest::STATUS_ACCEPTED,
                ReserveRequest::STATUS_PENDING,
                ReserveRequest::STATUS_REJECTED,
                ReserveRequest::STATUS_CANCELLED
            ]);
            $table->text('payment_details')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->decimal('cost_per_lead', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserve_requests');
    }
};
