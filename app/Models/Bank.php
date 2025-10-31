<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    public $timestamps = false;
    protected $table = 'banks';
    protected $fillable =[
        'id',
        'name',
        'code',
        'bin',
        'shortName',
        'logo',
        'transferSupported',
        'lookupSupported',
        'short_name',
        'support',
        'isTransfer',
        'swift_code'
    ];
}
