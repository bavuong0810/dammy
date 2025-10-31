<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportViewDaily extends Model
{
    protected $table = 'report_view_daily';
    protected $fillable =[
        'id',
        'total',
        'total_money',
        'day',
        'cost',
    ];
}
