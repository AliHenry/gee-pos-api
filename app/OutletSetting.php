<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutletSetting extends Model
{
    protected $table = 'outlet_setting';

    protected $primaryKey = 'outlet_uuid';

    protected $fillable = [
        'outlet_uuid',
        'currency',
        'online',
        'email',
        'facebook',
        'tags',
        'open_hours'
    ];

    protected $casts = [
        'outlet_uuid' => 'string'
    ];

    /**
     * @param $value
     * @return false|string
     */
    public function setTagsAttribute($value){
        return $this->attributes['tags'] = json_encode($value);
    }

    /**
     * @param $value
     * @return false|string
     */
    public function setOpenHoursAttribute($value){
        return $this->attributes['open_hours'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getTagsAttribute($value){
        return $this->attributes['tags'] = json_decode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getOpenHoursAttribute($value){
        return $this->attributes['open_hours'] = json_decode($value);
    }
}
