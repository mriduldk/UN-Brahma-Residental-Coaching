<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'mobile_verified_at',
        'password',
        'role',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function organization()
    {
        // Deprecated: use organizations() instead. Keeping for backward compatibility temporarily.
        return $this->belongsTo(Organization::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class)->withPivot('role')->withTimestamps();
    }

    public function hasRole($organizationId, $role)
    {
        return $this->organizations()
                    ->where('organization_id', $organizationId)
                    ->wherePivot('role', $role)
                    ->exists();
    }
    
    public function isAdmin($organizationId)
    {
        return $this->organizations()
                    ->where('organization_id', $organizationId)
                    ->wherePivot('role', 'admin')
                    ->exists();
    }

    public function currentOrganization()
    {
        // 1. Prioritize Admin Role (Staff/Admin)
        $adminOrg = $this->organizations()
                        ->wherePivot('role', 'admin')
                        ->first();
        
        if ($adminOrg) return $adminOrg;

        // 2. Fallback to any organization association (Staff/Admin)
        $fallbackOrg = $this->organizations()->first();
        if ($fallbackOrg) return $fallbackOrg;

        // 3. Fallback for Students
        if ($this->role === 'student' && $this->student) {
            $enrollment = $this->student->enrollments()
                ->where('status', 'active')
                ->first();
            
            if ($enrollment && $enrollment->academicYear) {
                return $enrollment->academicYear->organization;
            }
        }

        return null;
    }
    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'institution_user', 'user_id', 'institution_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function isInstitutionAdmin($institutionId)
    {
        return $this->institutions()
                    ->where('institution_id', $institutionId)
                    ->wherePivot('role', 'admin')
                    ->exists();
    }

    public function isSuperAdmin()
    {
        return $this->role === 'admin';
    }
}
