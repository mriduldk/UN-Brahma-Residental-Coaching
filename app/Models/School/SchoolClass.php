<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class SchoolClass extends Model
{
    use SoftDeletes;
    protected $table = 'school_classes';

    protected $fillable = [
        'organization_id',
        'name',
        'code',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function sections()
    {
        return $this->hasMany(SchoolSection::class, 'class_id');
    }
}
