<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    protected $primaryKey = 'trans_uuid';

    protected $fillable = [
        'trans_uuid',
        'biz_uuid',
        'outlet_uuid',
        'user_id',
        'total',
        'quantity'
    ];

    protected $casts = [
        'trans_uuid' => 'string',
        'biz_uuid' => 'string',
        'outlet_uuid' => 'string',
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'transaction_product', 'trans_uuid', 'prod_uuid')
            ->withPivot( 'total', 'quantity')
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(Transaction::class, 'trans_uuid', 'trans_uuid');
    }

    public function sales_person()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}
