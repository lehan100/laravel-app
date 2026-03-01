<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductCouponCode extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'product_coupon_codes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'coupon_code_id',
        'category_id',
        'product_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function product_of_coupons() {
        return $this->hasOne(ProductofCategory::class, "product_id","product_id");
    }
    public function category_of_coupons() {
        return $this->hasMany(ProductofCategory::class, 'category_id','category_id');
    }
}
