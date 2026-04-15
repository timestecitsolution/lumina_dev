<?php

namespace Modules\Purchase\Http\Requests\Requisition;
namespace Modules\Purchase\Entities;

use Modules\Purchase\Entities\RequisitionItem;
use Modules\Purchase\Entities\Project;
use Modules\Purchase\Entities\RequisitionApproval;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $table = 'requisitions';

    protected $fillable = [
        'project_id',
        'requested_by',
        'approved_by',
        'req_no',
        'req_date',
        'delivery_date',
        'delivery_place',
        'note',
        'status'
    ];

    protected $casts = [
        'req_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id')
            ->orderBy('position');
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approvals()
    {
        return $this->hasMany(RequisitionApproval::class, 'requisition_id');
    }
}
