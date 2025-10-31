<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProvider extends Model
{
    public $timestamps = false;
    protected $table = 'user_providers';
    protected $fillable =[
        'id',
        'user_id',
        'provider',
        'provider_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
