<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class QuotaPaymentOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'application_form_id',
        'razorpay_order_id',
        'amount',
        'currency',
        'status',
        'receipt',
        'notes',
        'json_response',
        'pending_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'notes' => 'array',
        'json_response' => 'array',
        'pending_payload' => 'array',
        'application_form_id' => 'integer',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function applicationForm()
    {
        return $this->belongsTo(ApplicationForm::class);
    }

    public function transactions()
    {
        return $this->hasMany(QuotaPaymentTransaction::class);
    }
}
