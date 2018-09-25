<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';

    protected $primaryKey = 'cate_uuid';

    protected $fillable = [
        'cate_uuid',
        'biz_uuid',
        'outlet_uuid',
        'name',
        'description',
        'parent_id'
    ];

    protected $casts = [
        'cate_uuid' => 'string',
        'biz_uuid' => 'string',
        'outlet_uuid' => 'string',
        'parent_id' => 'string'
    ];

    public function parent()
    {
        return $this->belongsTo('App\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Category', 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
