<?php

namespace Modules\Purchase\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionApproval extends Model
{
    use HasFactory;

    protected $table = 'requisition_approvals';

    protected $fillable = [
        'requisition_id',
        'action_by',
        'action_role',
        'employee_id',
        'action',
        'comment',
    ];

    /* ================= Relationships ================= */

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'action_by');
    }
}
