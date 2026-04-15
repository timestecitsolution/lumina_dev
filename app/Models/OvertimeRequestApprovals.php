<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequestApprovals extends Model
{
    use HasFactory;
    protected $table = 'overtime_request_approvals';
    protected $fillable = [
        'overtime_request_id',
        'action_by',
        'employee_id',
        'action_role',
        'action',
        'comment',
    ];

    // action_by relation
    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by', 'id');
    }

    // employee relation
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    // overtime request relation
    public function overtimeRequest()
    {
        return $this->belongsTo(OvertimeRequest::class, 'overtime_request_id', 'id');
    }
}
