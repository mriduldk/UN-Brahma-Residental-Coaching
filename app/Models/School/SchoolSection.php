<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class SchoolSection extends Model
{
    use SoftDeletes;
    protected $table = 'school_sections';

    protected $fillable = [
        'class_id',
        'name',
        'room_number',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
