<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    protected $fillable =[
        'id',
        'title',
        'slug',
        'description',
        'content',
        'thumbnail',
        'template',
        'created_at',
        'updated_at',
        'status'
    ];
}
