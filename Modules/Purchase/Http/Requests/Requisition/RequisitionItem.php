<?php

namespace Modules\Purchase\Http\Requests\Requisition;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_name',
        'quantity',
        'unit',
        'position'
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
