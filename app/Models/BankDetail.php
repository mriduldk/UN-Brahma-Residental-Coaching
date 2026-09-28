<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use SoftDeletes;
    use HasFactory, \App\Traits\CapitalizeAttributes;

    protected $fillable = [
        'student_id',
        'account_holder_name',
        'bank_name',
        'branch',
        'account_number',
        'ifsc_code',
        'upi_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
