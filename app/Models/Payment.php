<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable =[
        'id',
        'user_id',
        'code',
        'amount',
        'status',
        'note',
    ];

    const Status = [
        'New' => 0,
        'ConfirmTransfer' => 1,
        'Confirm' => 2,
        'Cancel' => 3
    ];

    const StatusType = [
        0 => 'Mới',
        1 => 'Xác nhận đã chuyển khoản',
        2 => 'Đã xác nhận',
        3 => 'Huỷ giao dịch'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
