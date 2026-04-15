<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorType extends Model
{
    protected $table = 'contractor_types';

    protected $fillable = [
        'type_name',
        'status',
        'description',
        'created_by',
        'updated_by'
    ];
}
