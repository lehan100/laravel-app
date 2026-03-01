<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductSales extends Model
{
    use HasFactory,
    SoftDeletes;
    protected $table = 'product_sales';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'alias',
        'decsription',
        'is_homepage',
        'date_from',
        'date_end',
        'status'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function product_items() {
        return $this->hasMany(ProductSaleItems::class, 'product_sales_id');
    }
    public function url() {
        return $this->hasOne(UrlRewrite::class, "sale_id", 'id');
    }
}
