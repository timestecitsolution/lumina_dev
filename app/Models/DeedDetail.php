<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeedDetail extends Model
{
    protected $fillable = [
        'deed_id',
        'section_id',
        'unit_type',
        'per_unit_rate',
        'total_unit',
        'section_amount',
        'created_by',
        'updated_by'
    ];

    public function deed()
    {
        return $this->belongsTo(Deed::class, 'deed_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function steps()
    {
        return $this->hasMany(DeedDetailsStep::class, 'deed_details_id');
    }
}

