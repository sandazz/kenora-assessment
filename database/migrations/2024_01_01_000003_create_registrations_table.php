<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->restrictOnDelete();
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->string('status')->default('active');
            $table->foreignId('registered_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('registered_at');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->tinyInteger('active_key')->nullable();
            $table->timestamps();

            // Unique constraint: same email can only have one active seat per workshop
            // NULLs don't collide, so cancelled rows are unlimited
            $table->unique(['workshop_id', 'attendee_email', 'active_key'], 'registrations_unique_active');
            $table->index(['workshop_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
