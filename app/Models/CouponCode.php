<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    use HasFactory,
    SoftDeletes;
    protected $table = 'coupon_codes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'coupon_code',
        'type',
        'uses',
        'max_uses_user',
        'discount_amount',
        'discount_amount_from',
        'discount_max',
        'date_from',
        'date_end',
        'status',
        'is_public'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function product_coupon_codes() {
        return $this->hasMany(ProductCouponCode::class, 'coupon_code_id');
    }

}
