<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'employee_id','loan_no','requested_amount','approved_amount','disbursement_date',
        'start_deduction_date','tenure_months','interest_rate','repayment_type',
        'monthly_installment','status','approved_by','approved_at','notes'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function payments()
    {
        return $this->hasMany(EmployeeLoanPayment::class, 'loan_id');
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeLoanSchedule::class, 'loan_id');
    }

    public function getOutstandingAttribute()
    {
        $totalPaid = $this->payments()->sum('amount');
        return $this->approved_amount - $totalPaid;
    }
}
