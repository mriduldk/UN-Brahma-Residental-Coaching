<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormLink extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'application_form_id',
        'link_code',
        'description',
        'is_active',
    ];

    public function applicationForm()
    {
        return $this->belongsTo(ApplicationForm::class);
    }
}
