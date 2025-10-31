<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoin extends Model
{
    protected $table = 'user_coins';
    protected $fillable = [
        'id',
        'user_id',
        'coin',
        'total_coin',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
