<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInstallsment extends Model
{
    use HasFactory;
        protected $fillable = [
        'loans_id',
        'datetimes_installsment',
        'installsment',
        'payment_amount',
        'payment_method',
        'image',
        'status',
    ];
}
