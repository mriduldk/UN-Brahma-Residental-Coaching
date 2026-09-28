<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class LiveClass extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'staff_id',
        'subject_name',
        'topic',
        'meeting_id',
        'start_url',
        'join_url',
        'password',
        'start_time',
        'duration',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'duration' => 'integer',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function staff()
    {
        return $this->belongsTo(SchoolStaff::class, 'staff_id');
    }

    public function getIsLiveAttribute()
    {
        $now = now();
        $end = $this->start_time->copy()->addMinutes($this->duration);
        return $now->between($this->start_time, $end) && $this->status !== 'completed' && $this->status !== 'cancelled';
    }
}
