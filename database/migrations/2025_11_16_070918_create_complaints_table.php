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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();


            // Citizen who submitted the complaint
            $table->foreignId('citizen_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Target government entity
            $table->foreignId('entity_id')
                  ->constrained('government_entities')
                  ->onDelete('cascade');

            // Complaint type (service, corruption, infrastructure)
            $table->string('type')->nullable();

            // Location of the issue
            $table->string('location')->nullable();

            // Problem description
            $table->text('description')->nullable();

            // Attachments (images/docs) stored as JSON array of file paths
            $table->json('attachments')->nullable();

            // Reference number for tracking
            $table->string('reference_number')->unique();

            // Workflow status
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
