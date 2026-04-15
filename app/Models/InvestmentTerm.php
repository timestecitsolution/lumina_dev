<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentTerm extends Model
{
    protected $fillable = [
        'investment_id',
        'term',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
