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
        Schema::create('overtime_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('overtime_request_id');
            $table->unsignedInteger('action_by');
            $table->unsignedInteger('employee_id');
            $table->string('action_role')->nullable();
            $table->enum('action', ['approved', 'rejected', 'pending'])->default('pending');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('overtime_request_id')
                ->references('id')
                ->on('overtime_requests')
                ->onDelete('cascade');

            $table->foreign('action_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employee_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_request_approvals');
    }
};
