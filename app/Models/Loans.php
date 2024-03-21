<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    use HasFactory;

    protected $fillable = [
        'loans_id',
        'user_id',
        'date_loan',
        'loan_amount',
        'month_tenor',
        'interest_rate',
        'status',
    ];
}
