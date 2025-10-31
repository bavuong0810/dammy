<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestChangeUserType extends Model
{
    protected $table = 'request_change_user_types';
    protected $fillable =[
        'id',
        'user_id',
        'note',
        'phone',
        'facebook',
        'status',
        'created_at',
        'updated_at'
    ];

    const Status = [
        'New' => 0,
        'Confirm' => 1,
        'Cancel' => 2
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
