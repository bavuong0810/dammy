<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumPrice extends Model
{
    protected $table = 'premium_prices';
    protected $fillable =[
        'id',
        'name',
        'price',
        'expired',
        'created_at',
        'updated_at'
    ];
}
