<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntranceExamResult extends Model
{
    use HasFactory;

    protected $table = 'entrance_exam_results';

    protected $fillable = [
        'student_application_id',
        'application_form_id',
        'stream',
        'roll_no',
        'candidate_name',
        'phone_number',
        'district',
        'marks',
        'rank',
        'district_rank',
        'qualification_status',
        'selection_quota',
        'remarks',
        'is_published',
        'published_at',
        'raw_payload',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'rank' => 'integer',
        'district_rank' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }

    public function application_by_roll()
    {
        return $this->hasOne(StudentApplication::class, 'roll_no', 'roll_no');
    }

    public function application_form()
    {
        return $this->belongsTo(ApplicationForm::class, 'application_form_id');
    }

    public function scopeStream($query, ?string $stream)
    {
        if (!$stream || strtolower($stream) === 'all') {
            return $query;
        }
        return $query->where('stream', strtoupper(trim($stream)));
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        $term = trim($search);
        return $query->where(function ($q) use ($term) {
            $q->where('roll_no', 'like', "%{$term}%")
              ->orWhere('candidate_name', 'like', "%{$term}%")
              ->orWhere('phone_number', 'like', "%{$term}%")
              ->orWhere('district', 'like', "%{$term}%");
        });
    }

    /**
     * Map common input strings to standard stream codes
     */
    public static function normalizeStream(?string $val): string
    {
        if (!$val) return 'UNKNOWN';
        $v = strtoupper(trim($val));
        if (str_contains($v, 'UPSC')) return 'UPSC';
        if (str_contains($v, 'APSC')) return 'APSC';
        if (str_contains($v, 'NEET') || str_contains($v, 'MEDICAL')) return 'NEET';
        if (str_contains($v, 'JEE') || str_contains($v, 'ENGINEERING')) return 'JEE';
        return $v;
    }
}
