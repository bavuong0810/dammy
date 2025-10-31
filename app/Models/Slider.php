<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'sliders';
    protected $fillable = [
        'id',
        'name',
        'src',
        'sort',
        'link',
        'status',
        'description',
        'target',
        'created_at',
        'updated_at'
    ];
}
