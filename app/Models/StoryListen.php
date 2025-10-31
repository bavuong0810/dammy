<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryListen extends Model
{
    protected $table = 'story_listens';
    protected $fillable =[
        'id',
        'story_id',
        'day',
        'week',
        'month',
        'year',
        'alltime',
        'created_at',
        'updated_at'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }
}
