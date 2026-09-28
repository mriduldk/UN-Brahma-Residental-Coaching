<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'student_id',
        'photo',
        'signature',
        'aadhaar_card',
        'passport',
        'hslc_marksheet',
        'hs_marksheet',
        'graduation_marksheet',
        'post_graduation_marksheet',
        'hslc_pass_certificate',
        'hs_pass_certificate',
        'graduation_pass_certificate',
        'post_graduation_pass_certificate',
        'hslc_admit_card',
        'hs_admit_card',
        'graduation_admit_card',
        'post_graduation_admit_card',
        'transfer_certificate',
        'caste_certificate',
        'income_certificate',
        'address_proof',
        'bed_parangat_marksheet',
        'bed_parangat_certificate',
        'tet_certificate',
        'pwd_certificate',
        'ex_serviceman_certificate',
        'ncc_certificate_doc',
        'sports_certificate',
        'cultural_certificate',
        'btc_residential_certificate',
        'language_diploma_certificate',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
