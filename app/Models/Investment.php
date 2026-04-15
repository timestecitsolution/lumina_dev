<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = [
        'date',
        'investment_name',
        'investor_id',
        'project_id',
        'amount',
        'profit_percent',
        'investment_type',
        'provide_employee',
        'transaction_type',
        'bank_id',
        'note',
        'timeline',
        'refference',
    ];

    protected $casts = [
        'date' => 'date',
        'provide_employee' => 'boolean',
        'amount' => 'decimal:2',
        'profit_percent' => 'decimal:2',
    ];

    // Investor Relation
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    // Bank Relation
    public function bank()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }

    // Project Relation
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Terms Relation
    public function terms()
    {
        return $this->hasMany(InvestmentTerm::class);
    }
}
