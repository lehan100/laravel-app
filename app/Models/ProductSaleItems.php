<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductSaleItems extends Model
{
    use HasFactory,
        SoftDeletes;
    protected $table = 'product_sale_items';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'product_sales_id',
        'product_id',
        'quantity_is_uses_product',
        'special_percent',
        'special_price',
        'buy_qty',
        'gift_sku',
        'gift_qty',
        'order_qty',
        // 'date_from',
        // 'date_end',
        'gift_sku_info',
        'status'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function info()
    {
        return $this->belongsTo(ProductSales::class, "product_sales_id", 'id');
    }
    public function product()
    {
        return $this->hasOne(Product::class, "id", "product_id")
            ->latest();
    }
}
