<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidationPermission extends Model
{
    protected $table = 'validation_permissions';

    protected $fillable = [
        'designation_id',
        'validation_role_id',
        'employee_id',
        'priority',
    ];

    public $timestamps = true;

    /**
     * Relation: validation role
     */
    public function validationRole()
    {
        return $this->belongsTo(ValidationRole::class, 'validation_role_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * Relation: system role (optional)
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
