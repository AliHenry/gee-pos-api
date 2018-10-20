<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlet';

    protected $primaryKey = 'outlet_uuid';


    protected $casts = [
        'outlet_uuid' => 'string',
        'biz_uuid' => 'string'
    ];

    public function followers()
    {
        return $this->belongsToMany(Customer::class, 'outlet_followers', 'outlet_uuid', 'cus_uuid');
    }

    public function settings()
    {
        return $this->hasOne(OutletSetting::class, 'outlet_uuid', 'outlet_uuid');
    }
}
