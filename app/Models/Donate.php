<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donate extends Model
{
    protected $table = 'donate';
    protected $fillable =[
        'id',
        'user_id',
        'receiver_id',
        'story_id',
        'coin',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }
}
