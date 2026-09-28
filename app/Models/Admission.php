<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'academic_session',
        'admission_fee',
        'allow_full_payment',
        'full_payment_discount_amount',
        'full_payment_discount_percentage',
        'allow_emi',
        'emi_tenures',
        'emi_down_payment',
        'allow_cash_payment',
        'cash_payment_days',
        'allow_dd_full_payment',
        'dd_full_payment_discount_amount',
        'dd_full_payment_discount_percentage',
        'allow_dd_emi',
        'dd_emi_tenures',
        'dd_emi_down_payment',
        'created_by',
        'status',
        'application_form_id',
    ];

    protected $casts = [
        'data' => 'array',
        'emi_tenures' => 'array',
        'dd_emi_tenures' => 'array',
        'allow_full_payment' => 'boolean',
        'allow_emi' => 'boolean',
        'allow_cash_payment' => 'boolean',
        'allow_dd_full_payment' => 'boolean',
        'allow_dd_emi' => 'boolean',
        'admission_fee' => 'decimal:2',
        'full_payment_discount_amount' => 'decimal:2',
        'full_payment_discount_percentage' => 'decimal:2',
        'emi_down_payment' => 'decimal:2',
        'dd_full_payment_discount_amount' => 'decimal:2',
        'dd_full_payment_discount_percentage' => 'decimal:2',
        'dd_emi_down_payment' => 'decimal:2',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function application_form()
    {
        return $this->belongsTo(ApplicationForm::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
