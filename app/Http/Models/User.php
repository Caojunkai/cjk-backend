<?php

namespace App\Http\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /*
     * 添加数据库里不存在的字段
     */
    protected $appends = ['followed'];

    /*
     * 类型转换
     */
    protected $casts = [
        'use_gravatar' => 'boolean',
        'site_admin' => 'boolean'
    ];

    /*
     * 黑名单
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'mobile',
        'password',
        'first_name',
        'last_name',
        'country',
        'address',
        'phone',
        'status',
        'remember_token',
        'deleted_at',
    ];


}
