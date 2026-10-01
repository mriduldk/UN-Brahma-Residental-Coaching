<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtclaConstituency extends Model
{
    use HasFactory;

    protected $table = 'btcla_constituencies';

    protected $fillable = [
        'number',
        'name',
        'category',
        'is_active',
        'order',
    ];

    protected $casts = [
        'number' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope active constituencies ordered by order or number.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order')->orderBy('number');
    }
}
