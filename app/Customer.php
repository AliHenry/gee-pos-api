<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'customers';

    protected $primaryKey = 'cus_uuid';

    protected $fillable = [
        'cus_uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar'
    ];

    protected $casts = [
        'cus_uuid' => 'string'
    ];

    protected $hidden = [
      'password'
    ];
}
