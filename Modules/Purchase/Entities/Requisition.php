<?php

namespace Modules\Purchase\Entities;


use App\Models\User;
use App\Models\Project;
use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends BaseModel
{
    use HasFactory;

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
        'status',
    ];

    protected $casts = [
        'req_date' => 'date',
    ];

    /* ================= Relationships ================= */

    // User who requested
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // User who approved
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Requisition items
    public function items()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id');
    }

    // Approval history
    public function approvals()
    {
        return $this->hasMany(RequisitionApproval::class, 'requisition_id');
    }

    /* ================= Scopes ================= */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
