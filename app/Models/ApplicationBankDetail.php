<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ApplicationBankDetail extends Model
{
    use SoftDeletes;
    use \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_application_id',
        'account_holder_name',
        'bank_name',
        'branch',
        'account_number',
        'ifsc_code',
        'upi_id',
    ];

    public function student_application()
    {
        return $this->belongsTo(StudentApplication::class);
    }
}
