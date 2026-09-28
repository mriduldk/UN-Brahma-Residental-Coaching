<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolStaff extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'school_staff';

    protected $fillable = [
        'organization_id',
        'user_id',
        'employee_id',
        'full_name',
        'email',
        'phone_number',
        'designation',
        'department',
        'gender',
        'joining_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'joining_date' => 'date',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function subjectMappings()
    {
        return $this->hasMany(StaffSubjectMapping::class, 'school_staff_id');
    }
}
