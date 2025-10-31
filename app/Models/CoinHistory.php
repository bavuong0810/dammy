<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinHistory extends Model
{
    protected $table = 'user_coin_histories';
    protected $fillable =[
        'id',
        'user_id',
        'coin',
        'type',
        'message',
        'transaction_type',
        'created_at',
        'updated_at'
    ];

    const TransactionType = [
        'MINUS' => 0,
        'PLUS' => 1,
    ];

    const Type = [
        'Donate' => 0,
        'Donated' => 1,
        'Recharge' => 2,
        'System' => 3,
        'BuyChapter' => 4,
        'BuyVIP' => 5,
        'Withdraw' => 6,
        'RecommendedStory' => 7,
    ];

    const TypeText = [
        0 => 'Donate',
        1 => 'Được Donated',
        2 => 'Nạp xu',
        3 => 'Hệ thống',
        4 => 'Mua chương',
        5 => 'Mua VIP',
        6 => 'Rút xu',
        7 => 'Đăng ký đề cử'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
