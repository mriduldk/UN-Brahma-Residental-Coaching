<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class GlobalConfig extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'form_creation_charge',
        'registration_fee_charge',
        'free_form_limit',
    ];
}
