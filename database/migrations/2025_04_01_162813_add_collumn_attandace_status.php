<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('attendance_status')->default(0); // Menambahkan kolom attendance_status dengan nilai default 0 (tidak hadir)
        });
    }
    
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('attendance_status');
        });
    }
    
};
