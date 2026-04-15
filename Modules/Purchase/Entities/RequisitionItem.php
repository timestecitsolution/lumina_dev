<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_name',
        'quantity',
        'unit',
        'position',
    ];

    /* ================= Relationships ================= */

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
