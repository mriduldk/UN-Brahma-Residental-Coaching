<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'school_staff_id',
        'date',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(SchoolStaff::class, 'school_staff_id');
    }
}
