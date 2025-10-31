<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    protected $table = 'withdraw_requests';
    protected $fillable = [
        'id',
        'user_id',
        'code',
        'coin',
        'status',
        'message',
        'created_at',
        'updated_at'
    ];

    const StatusType = [
        'New' => 0,
        'Confirm' => 1,
        'Cancel' => 2,
    ];

    const StatusText = [
        0 => 'Chờ xử lý',
        1 => 'Đã duyệt',
        2 => 'Huỷ',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
