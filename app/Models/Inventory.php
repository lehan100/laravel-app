<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{

    use HasFactory,
        SoftDeletes;

    protected $table = 'inventories';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'product_id',
        'available_quantity',
        'sold_quantity',
        'order_quantity'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];


    public function timeline()
    {
        return $this->hasOne(InventoryTimelines::class, 'inventory_id', 'id');
    }
    public function product()
    {
        return $this->hasOne(Product::class,  'id','product_id');
    }
    
}
