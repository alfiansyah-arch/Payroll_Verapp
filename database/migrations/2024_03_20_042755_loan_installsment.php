<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LoanInstallsment extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_installsment', function (Blueprint $table) {
            $table->id();
            $table->string('loans_id');
            $table->datetime('datetimes_installsment');
            $table->string('installsment');
            $table->string('payment_amount');
            $table->string('payment_method');
            $table->string('image');
            $table->string('status');
            $table->timestamps();
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
