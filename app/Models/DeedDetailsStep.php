<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeedDetailsStep extends Model
{
    protected $fillable = [
        'deed_details_id',
        'deed_id',
        'section_id',
        'step_id',
        'budget_amount_percentage',
        'budget_amount',
        'created_by',
        'updated_by'
    ];

    public function step()
    {
        return $this->belongsTo(Step::class, 'step_id');
    }
}

