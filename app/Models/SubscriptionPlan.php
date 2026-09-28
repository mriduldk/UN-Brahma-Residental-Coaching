<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'business_type',
        'standard_price',
        'discounted_price',
        'free_months',
        'discounted_months',
    ];
}
