<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidationRole extends Model
{
    protected $table = 'validation_roles';

    protected $fillable = [
        'validation_name',
    ];

    public $timestamps = true;

    /**
     * Relationship:
     * One validation role -> many validation permissions
     */
    public function permissions()
    {
        return $this->hasMany(
            ValidationPermission::class,
            'validation_role_id',
            'id'
        );
    }
    public function validationRole()
    {
        return $this->belongsTo(
            ValidationRole::class,
            'validation_role_id',
            'id'
        );
    }
}
