<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deed extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($deed) {
            foreach ($deed->details as $detail) {
                $detail->steps()->delete();
            }
            $deed->details()->delete();
        });
    }

    protected $fillable = [
        'deed_name',
        'project_id',
        'contractor_id',
        'deed_total_amount',
        'deed_date',
        'deed_file',
        'status',
        'created_by',
        'updated_by'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function details()
    {
        return $this->hasMany(DeedDetail::class, 'deed_id');
    }
}
