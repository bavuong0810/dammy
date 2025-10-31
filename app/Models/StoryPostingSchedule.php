<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryPostingSchedule extends Model
{
    protected $table = 'story_posting_schedules';
    protected $fillable =[
        'id',
        'story_id',
        'time',
        'created_at',
        'updated_at'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }
}
