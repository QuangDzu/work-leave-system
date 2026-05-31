<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->char('id', 10)->primary();
            $table->char('user_id', 10);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(0);
            $table->text('reason');
            $table->enum('type', ['annual', 'sick', 'unpaid'])->default('annual');
            $table->enum('status', ['new', 'pending', 'approved', 'rejected', 'cancelled'])->default('new');
            $table->char('created_by', 10)->nullable();
            $table->char('updated_by', 10)->nullable();
            $table->char('deleted_by', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
