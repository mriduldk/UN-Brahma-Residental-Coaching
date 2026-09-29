<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationForm extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($form) {

            $linkCode = \Illuminate\Support\Str::random(12);
            $form->formLinks()->create([
                'link_code' => $linkCode,
                'description' => 'Default Link',
                'is_active' => true,
            ]);
        });
    }

    protected $fillable = [
        'application_code',
        'application_name',
        'application_description',
        'session',
        'no_of_vacancies',
        'application_fee',
        'category_fees',
        'status',
        'deadline',
        'advertisement_pdf',
        'created_by',
        'field_config',
        'institution_id',
        'course_fee', 
        'organization_id',
        'is_application_fee_required',
        'is_admission_required',
        'admission_fee',
        'is_approval_required',
        'applied_for_options',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function admission_students()
    {
        return $this->hasMany(AdmissionStudent::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function student_applications()
    {
        return $this->hasMany(StudentApplication::class);
    }

    protected $casts = [
        'field_config' => 'array',
        'deadline' => 'date',
        'application_fee' => 'decimal:2',
        'course_fee' => 'decimal:2',
        'organization_id' => 'integer',
        'is_application_fee_required' => 'boolean',
        'is_admission_required' => 'boolean',
        'admission_fee' => 'decimal:2',
        'is_approval_required' => 'boolean',
        'category_fees' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Helper to check if a specific section is enabled
    public function isSectionEnabled(string $section): bool
    {
        if (empty($this->field_config)) {
            return true; 
        }
        return isset($this->field_config[$section]['enabled']);
    }

    // Helper to check if a specific field is enabled in config
    public function isFieldVisible(string $section, string $field): bool
    {
        if (empty($this->field_config)) {
            return true; 
        }
        
        // If section is disabled, fields are hidden
        if (!$this->isSectionEnabled($section)) {
            return false;
        }

        // Fields removed from application forms
        if (in_array($field, ['marital_status', 'is_ex_serviceman', 'ncc_certificate', 'has_national_sports_achievement', 'has_fine_arts_cultural_achievement'])) {
            return false;
        }

        // Documents removed from application forms
        if ($section === 'documents' && in_array($field, [
            'passport',
            'transfer_certificate',
            'bed_parangat_marksheet',
            'bed_parangat_certificate',
            'tet_certificate',
            'pwd_certificate',
            'ex_serviceman_certificate',
            'ncc_certificate_doc',
            'sports_certificate',
            'cultural_certificate',
            'language_diploma_certificate',
        ])) {
            return false;
        }

        $sectionFields = $this->field_config[$section]['fields'] ?? [];

        if ($field === 'community' && in_array('nationality', $sectionFields)) {
            return true;
        }
        if ($field === 'nationality' && in_array('community', $sectionFields)) {
            return true;
        }
        if ($section === 'parents' && $field === 'father_annual_income') {
            return true;
        }
        if ($section === 'personal' && $field === 'alternate_mobile_number') {
            return true;
        }

        return in_array($field, $sectionFields);
    }

    public function getDefaultQualifications(): array
    {
        if (isset($this->field_config['academic']['default_qualifications']) && is_array($this->field_config['academic']['default_qualifications']) && !empty($this->field_config['academic']['default_qualifications'])) {
            return $this->field_config['academic']['default_qualifications'];
        }
        return ['HSLC', 'HS', 'GRADUATION'];
    }

    /**
     * Get configured Applied For options for this form.
     */
    public function getAppliedForOptions(): array
    {
        // 1. Direct column if set
        if (!empty($this->attributes['applied_for_options'])) {
            $val = $this->attributes['applied_for_options'];
            if (is_array($val)) return array_values(array_filter(array_map('trim', $val)));
            $decoded = json_decode($val, true);
            if (is_array($decoded)) return array_values(array_filter(array_map('trim', $decoded)));
            if (is_string($val)) {
                $lines = preg_split('/[\r\n,]+/', $val);
                return array_values(array_filter(array_map('trim', $lines)));
            }
        }

        // 2. field_config['applied_for_options']
        if (isset($this->field_config['applied_for_options'])) {
            $val = $this->field_config['applied_for_options'];
            if (is_array($val)) return array_values(array_filter(array_map('trim', $val)));
            if (is_string($val)) {
                $lines = preg_split('/[\r\n,]+/', $val);
                return array_values(array_filter(array_map('trim', $lines)));
            }
        }

        // 3. field_config['personal']['applied_for_options']
        if (isset($this->field_config['personal']['applied_for_options'])) {
            $val = $this->field_config['personal']['applied_for_options'];
            if (is_array($val)) return array_values(array_filter(array_map('trim', $val)));
            if (is_string($val)) {
                $lines = preg_split('/[\r\n,]+/', $val);
                return array_values(array_filter(array_map('trim', $lines)));
            }
        }

        return [];
    }

    /**
     * Get effective Applied For options with graceful fallbacks.
     */
    public function getEffectiveAppliedForOptions(): array
    {
        $options = $this->getAppliedForOptions();
        if (!empty($options)) {
            return $options;
        }

        if (!empty($this->application_name)) {
            return [$this->application_name];
        }

        return ['General Coaching'];
    }

    public function formLinks()
    {
        return $this->hasMany(FormLink::class);
    }

    public function getApplicationFeeFor(\App\Models\Student $student, ?\App\Models\StudentApplication $application = null): float
    {
        // If not fee required, return 0
        if (!$this->is_application_fee_required) {
            return 0.0;
        }

        // If category_fees is not set, fallback to legacy application_fee
        if (empty($this->category_fees)) {
            return (float) $this->application_fee;
        }

        $category = $application ? ($application->application_student->category ?? $student->category) : $student->category;
        $is_pwd = $application ? ($application->application_student->is_pwd ?? $student->is_pwd) : $student->is_pwd;

        // 1. Check PwD Status
        if ($is_pwd && isset($this->category_fees['pwd']) && $this->category_fees['pwd'] !== '') {
            return (float) $this->category_fees['pwd'];
        }

        // 2. Check Reserved Categories (SC, ST, OBC, MOBC)
        $reservedCategories = ['SC', 'ST', 'OBC', 'MOBC'];
        if (in_array(strtoupper($category ?? ''), $reservedCategories) && isset($this->category_fees['reserved']) && $this->category_fees['reserved'] !== '') {
            return (float) $this->category_fees['reserved'];
        }

        // 3. Fallback to General
        if (isset($this->category_fees['general']) && $this->category_fees['general'] !== '') {
            return (float) $this->category_fees['general'];
        }

        // Absolute fallback
        return (float) $this->application_fee;
    }
}
