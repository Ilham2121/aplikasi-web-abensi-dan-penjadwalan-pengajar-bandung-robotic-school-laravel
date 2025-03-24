<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('teacher_id');
            $table->dropColumn('attendance_date');
            $table->dropColumn('check_in_time');
            $table->dropUnique(['schedule_id', 'teacher_id', 'attendance_date']);
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('note');
            $table->text('rejection_reason')->nullable()->after('status');
            
            // Create a new unique constraint for schedule_id and created_at date
            $table->unique(['schedule_id', DB::raw('DATE(created_at)')], 'attendances_schedule_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('check_in_time');
            $table->dropColumn('status');
            $table->dropColumn('rejection_reason');
            $table->dropIndex('attendances_schedule_date_unique');
            
            // Re-add the original unique constraint
            $table->unique(['schedule_id', 'teacher_id', 'attendance_date']);
        });
    }
};
