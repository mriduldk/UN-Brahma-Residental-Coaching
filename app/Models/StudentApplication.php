<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'application_code',
        'application_form_id',
        'student_id',
        'status',
        'submitted_at',
        'payment_status',
        'transaction_id',
        'current_step',
        'approval_status',
        'approval_remarks',
        'applied_for',
        'examination_center',
    ];

    public function getAppliedForDisplayAttribute()
    {
        return $this->applied_for ?? $this->application_student?->applied_for ?? 'N/A';
    }

    public function getExaminationCenterDisplayAttribute()
    {
        return $this->examination_center ?? $this->application_student?->examination_center ?? 'N/A';
    }

    public function getExaminationCenterVenueAttribute(): string
    {
        $center = strtoupper((string) ($this->examination_center ?? $this->application_student?->examination_center ?? ''));
        if (str_contains($center, 'KOKRAJHAR')) {
            return 'Kokrajhar Girls H.S.S.';
        }
        if (str_contains($center, 'CHIRANG') || str_contains($center, 'KASHIKOTRA')) {
            return 'Kashikotra H.S.S.';
        }
        if (str_contains($center, 'BAKSA') || str_contains($center, 'BARAMA')) {
            return 'Barama College';
        }
        if (str_contains($center, 'TAMULPUR')) {
            return 'Tamulpur HSS';
        }
        if (str_contains($center, 'UDALGURI')) {
            return 'Udalguri HSS';
        }
        return $this->examination_center_display;
    }

    public function getExaminationCenterFullAttribute(): string
    {
        $venue = $this->examination_center_venue;
        $district = $this->examination_center_display;
        if ($district && $district !== 'N/A') {
            return "{$venue} - {$district}";
        }
        return $venue;
    }

    public function getExamDateAttribute(): string
    {
        return 'Tuesday, 27th October, 2026';
    }

    public function getExamTimeAttribute(): string
    {
        $stream = strtoupper((string) ($this->applied_for_display ?? ''));
        if (str_contains($stream, 'UPSC') || str_contains($stream, 'APSC') || str_contains($stream, 'CIVIL')) {
            return '02:00 PM – 04:00 PM (2 Hours)';
        }
        return '10:00 AM – 12:00 PM (2 Hours)';
    }

    public function getExamTimeShortAttribute(): string
    {
        $stream = strtoupper((string) ($this->applied_for_display ?? ''));
        if (str_contains($stream, 'UPSC') || str_contains($stream, 'APSC') || str_contains($stream, 'CIVIL')) {
            return '02:00 PM – 04:00 PM';
        }
        return '10:00 AM – 12:00 PM';
    }

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function application_form()
    {
        return $this->belongsTo(ApplicationForm::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function application_student()
    {
        return $this->hasOne(ApplicationStudent::class);
    }

    public function application_address()
    {
        return $this->hasOne(ApplicationAddress::class);
    }

    public function application_parent()
    {
        return $this->hasOne(ApplicationParent::class);
    }

    public function application_academic_qualifications()
    {
        return $this->hasMany(ApplicationAcademicQualification::class);
    }

    public function application_documents()
    {
        return $this->hasOne(ApplicationDocument::class);
    }

    public function application_bank_detail()
    {
        return $this->hasOne(ApplicationBankDetail::class);
    }

    public function admission_payments()
    {
        return $this->hasMany(AdmissionPayment::class);
    }

    public function payment_orders()
    {
        return $this->hasMany(PaymentOrder::class);
    }

    public function payment_transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latest_payment_order()
    {
        return $this->hasOne(PaymentOrder::class)->latestOfMany();
    }

    public function admission_payment_orders()
    {
        return $this->hasMany(AdmissionPaymentOrder::class);
    }

    public function admission_payment_transactions()
    {
        return $this->hasManyThrough(AdmissionPaymentTransaction::class, AdmissionPaymentOrder::class);
    }

    public function admission_student()
    {
        return $this->hasOne(AdmissionStudent::class);
    }

    public function admit_card_download_logs()
    {
        return $this->hasMany(AdmitCardDownloadLog::class);
    }

    public static function getStreamCode(?string $appliedFor): string
    {
        if (!$appliedFor) {
            return '0';
        }

        $val = trim($appliedFor);
        if ($val === 'UPSC' || str_contains($val, 'UPSC')) {
            return '1';
        }
        if ($val === 'APSC' || str_contains($val, 'APSC')) {
            return '2';
        }
        if ($val === 'Engineering (JEE)' || str_contains($val, 'Engineering') || str_contains($val, 'JEE')) {
            return '3';
        }
        if ($val === 'Medical (NEET)' || str_contains($val, 'Medical') || str_contains($val, 'NEET')) {
            return '4';
        }

        return '0';
    }

    public static function generateApplicationCode(?string $appliedFor, $excludeId = null): string
    {
        $streamCode = self::getStreamCode($appliedFor);
        $year = '2026';
        $prefix = "BTC-{$year}{$streamCode}";

        $existingCodes = self::withTrashed()
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('application_code', 'LIKE', "{$prefix}%")
            ->pluck('application_code');

        $maxNum = 0;
        foreach ($existingCodes as $code) {
            $numPart = substr($code, strlen($prefix));
            if (is_numeric($numPart) && (int)$numPart > $maxNum) {
                $maxNum = (int)$numPart;
            }
        }

        $nextNum = $maxNum + 1;
        $candidateCode = $prefix . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        while (self::withTrashed()->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->where('application_code', $candidateCode)->exists()) {
            $nextNum++;
            $candidateCode = $prefix . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        }

        return $candidateCode;
    }
}
