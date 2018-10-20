<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    protected $primaryKey = 'prod_uuid';

    protected $fillable = [
        'prod_uuid',
        'cate_uuid',
        'outlet_uuid',
        'name',
        'description',
        'quantity',
        'price',
        'hide'
    ];

    protected $casts = [
        'cate_uuid' => 'string',
        'outlet_uuid' => 'string',
        'prod_uuid' => 'string'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cate_uuid');
    }

    public function likes()
    {
        return $this->belongsToMany(Customer::class, 'product_likes','prod_uuid','cus_uuid');
    }
}
