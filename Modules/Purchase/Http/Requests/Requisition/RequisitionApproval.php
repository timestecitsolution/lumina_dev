<?php

namespace Modules\Purchase\Http\Requests\Requisition;

use Illuminate\Database\Eloquent\Model;

class RequisitionApproval extends Model
{
    protected $table = 'requisition_approvals';

    protected $fillable = [
        'requisition_id',
        'action_by',
        'action_role',
        'action',
        'comment'
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
