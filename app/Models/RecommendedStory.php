<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendedStory extends Model
{
    protected $table = 'recommended_stories';
    protected $fillable =[
        'id',
        'group_data',
        'date',
        'created_at',
        'updated_at'
    ];
}
