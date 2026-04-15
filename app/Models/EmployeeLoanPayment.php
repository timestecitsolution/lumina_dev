<?php
// app/Models/EmployeeLoanPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoanPayment extends Model
{
    protected $fillable = [
        'loan_id','payment_date','amount','method','reference','note'
    ];

    public function loan()
    {
        return $this->belongsTo(EmployeeLoan::class, 'loan_id');
    }
}
