<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable =[
        'id',
        'name',
        'slug',
        'description',
        'parent',
        'sort',
        'thumbnail',
        'thumbnail_alt',
        'status',
        'seo_title',
        'seo_keyword',
        'seo_description',
        'created_at',
        'updated_at'
    ];
}
