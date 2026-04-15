<?php

// app/Models/EmployeeLoanSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoanSchedule extends Model
{
    protected $fillable = [
        'loan_id','due_date','due_amount','principal_component','interest_component',
        'paid_amount','paid_date','status'
    ];

    public function loan()
    {
        return $this->belongsTo(EmployeeLoan::class, 'loan_id');
    }
}
