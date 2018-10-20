<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;
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

    public function product_likes()
    {
        return $this->belongsToMany(Product::class, 'product_likes', 'cus_uuid', 'prod_uuid');
    }

    public function follow_outlet()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_followers', 'cus_uuid', 'outlet_uuid');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }
}
