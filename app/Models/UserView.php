<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserView extends Model
{
    protected $table = 'user_views';
    protected $fillable =[
        'id',
        'user_id',
        'day',
        'week',
        'month',
        'year',
        'alltime',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
