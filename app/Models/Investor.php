<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'assigned_employee_from_investor',
        'notes',
    ];

    protected $casts = [
        'assigned_employee_from_investor' => 'boolean',
    ];
}
