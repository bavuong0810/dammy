<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $table = 'chapters';
    protected $fillable =[
        'id',
        'user_id',
        'story_id',
        'name',
        'slug',
        'content',
        'content_images',
        'processing',
        'vol_number',
        'view',
        'coin',
        'status',
        'warning',
        'created_at',
        'updated_at',
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
