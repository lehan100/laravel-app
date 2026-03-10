<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
class Product extends Model {

    use HasFactory,
        SoftDeletes,Searchable;

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $preserveKeys = true;
    protected $fillable = [
        'name',
        'name_ascii',
        'sku',
        'alias',
        'quantity',
        'price',
        'special_price',
        'special_price_from',
        'special_price_to',
        'picture',
        'use_coupon',
        'entype_id',
        'hit_viewer',
        'hit_order'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at','sort','name_ascii'];
    public function toSearchableArray(): array
    {
        $this->loadMissing('attibute_sets');

        $array = [
            'id'            => (int) $this->id,
            'name'          => $this->name,
            'name_ascii'    => $this->name_ascii, 
            'sku'           => $this->sku,
            'price'         => (float) $this->price,
            'status'        => (int) $this->status,
            'quantity'      => (int) $this->quantity,
        ];

        if ($this->attibute_sets) {
            foreach ($this->attibute_sets as $attribute) {
                $key = 'attr_' . $attribute->name_alias; 
                $array[$key] = $attribute->value;
            }
        }

        return $array;
    }
    public function category() {
        return $this->belongsToMany(Category::class, 'product_of_categories')
                        ->withTimestamps()->wherePivot('deleted_at', NULL);
    }
    public function option_entries() {
        return $this->belongsToMany(ProductOptionEntries::class, 'product_of_option_entries')
                        ->withTimestamps()->wherePivot('deleted_at', NULL);
    }
    public function options() {
        return $this->hasMany(ProductOption::class, 'product_id');
    }

    public function attibute_sets() {
        return $this->hasMany(ProductAttributeSet::class, 'product_id');
    }

    public function contents() {
        return $this->hasOne(EntypeContent::class, "entype_id", "entype_id")
                        ->latest();
    }
    public function inventory() {
        return $this->hasOne(Inventory::class, "product_id", "id")
                        ->latest();
    }
    public function url() {
        return $this->hasOne(UrlRewrite::class, "product_id", 'id');
    }
    public function ratings() {
        return $this->hasMany(Rating::class, 'product_id');
    }
    public function sales() {
        return $this->hasMany(ProductSaleItems::class, 'product_id');
    }
   
    public function tier_prices() {
        return $this->hasOne(ProductofTierPrices::class, 'product_id');
    }
}
