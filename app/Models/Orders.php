<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{

    use HasFactory,
        SoftDeletes;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'invoice_id',
        'gender',
        'phone',
        'email',
        'city_id',
        'district_id',
        'ward_id',
        'address',
        'note',
        'price_total',
        'price_shipping',
        'status',
        'payment_method'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function items()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function timeline()
    {
        return $this->hasOne(OrderTimelines::class, 'order_id', 'id');
    }
    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'city_id');
    }
    public function district()
    {
        return $this->hasOne(District::class, 'id', 'district_id');
    }
    public function ward()
    {
        return $this->hasOne(Ward::class, 'id', 'ward_id');
    }
}
