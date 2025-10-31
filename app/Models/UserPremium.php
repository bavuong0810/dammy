<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPremium extends Model
{
    protected $table = 'user_premiums';
    protected $fillable = [
        'id',
        'user_id',
        'coin',
        'time',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
