<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $fillable =[
        'id',
        'user_id',
        'story_id',
        'error',
        'chapter_id',
        'note',
        'status',
        'created_at',
        'updated_at',
    ];

    const StatusType = [
        'New' => 0,
        'Confirm' => 1,
        'Cancel' => 2
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id', 'id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }
}
