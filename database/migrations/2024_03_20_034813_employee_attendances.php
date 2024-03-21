<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmployeeAttendances extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->date('date');
            $table->time('entry_hour');
            $table->time('out_hour');
            $table->string('entry_photo');
            $table->string('out_photo');
            $table->text('entry_location');
            $table->text('out_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
