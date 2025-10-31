<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavouriteStory extends Model
{
    protected $table = 'favourite_stories';
    protected $fillable =[
        'id',
        'story_id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
