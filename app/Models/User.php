<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'id',
        'username',
        'password',
        'email',
        'name',
        'birthday',
        'address',
        'province',
        'district',
        'ward',
        'avatar',
        'total_view',
        'total_listen',
        'total_view_month',
        'total_listen_month',
        'facebook',
        'phone',
        'total_donate',
        'type',
        'bookmarks',
        'email_verified_at',
        'active',
        'premium_date',
        'last_login',
        'bank_account',
        'email_activation',
        'created_at',
        'updated_at',
        'request_change_type',
        'about_me',
        'total_follow',
        'register_with_social',
        'is_change_password',
        'view_price',
        'view_time',
        'team_signature',
        'team_accept_time',
        'recommended_my_stories'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const UserType = [
        'User' => 0,
        'TranslateTeam' => 1
    ];
    public function coin()
    {
        return $this->hasOne(UserCoin::class, 'user_id', 'id');
    }

    public function premium_histories()
    {
        return $this->hasMany(UserPremium::class, 'user_id', 'id');
    }
}
