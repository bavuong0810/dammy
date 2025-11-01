<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyBenefit extends Model
{
    protected $table = 'monthly_benefits';
    protected $fillable =[
        'id',
        'user_id',
        'level_1',
        'level_2',
        'level_3',
        'level_4',
        'level_5',
        //Bonus for each level
        'level_6',
        'level_7',
        'level_8',
        'level_9',
        'level_10',
        'level_11',
        'level_12',
        'level_13',
        'level_14',
        'level_15',
        'created_at',
        'updated_at'
    ];

    const VIEW_LEVEL = [
        'level_1' => 5000,
        'level_2' => 10000,
        'level_3' => 50000,
        'level_4' => 100000,
        'level_5' => 200000,
        'level_6' => 500000,
        'level_7' => 1000000,
    ];

    const BENEFIT = [
        'level_1' => 10000,
        'level_2' => 25000,
        'level_3' => 35000,
        'level_4' => 100000,
        'level_5' => 200000,
        'level_6' => 400000,
        'level_7' => 1000000
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
