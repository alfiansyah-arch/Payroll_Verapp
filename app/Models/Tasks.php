<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date_start',
        'date_end',
        'hour_start',
        'hour_end',
        'description',
        'image',
        'status',
    ];
}
