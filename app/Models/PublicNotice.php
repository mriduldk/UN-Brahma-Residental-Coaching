<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicNotice extends Model
{
    protected $fillable = [
        'title',
        'description',
        'document_path',
        'is_active',
        'organization_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
