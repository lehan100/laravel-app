<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductofTierPrices extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_of_tier_prices';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'tier_price_id',
        'product_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function products() {
        return $this->belongsTo(Product::class, "product_id","id");
    }
    public function product() {
        return $this->hasOne(Product::class, "id","product_id");
    }
    public function tier_price() {
        return $this->hasOne(TierPrice::class, "id","tier_price_id");
    }
}
