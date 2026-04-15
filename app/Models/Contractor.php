<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $table = 'contractors';

    protected $fillable = [
        'contractor_type_id',
        'name',
        'phone',
        'address',
        'tin',
        'bin',
        'trade_license_no',
        'trade_license_img',
        'nid',
        'nid_img',
        'profile_img',
        'status',
        'created_by',
        'updated_by'
    ];

    // Contractor Type Relation
    public function type()
    {
        return $this->belongsTo(ContractorType::class, 'contractor_type_id');
    }

    // Status accessor (for convenience)
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'active' : 'inactive';
    }
}
