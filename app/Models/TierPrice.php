<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TierPrice extends Model
{
    use HasFactory,
    SoftDeletes;
    protected $table = 'tier_prices';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'status',
        'date_from',
        'date_end',
        'status'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function items() {
        return $this->hasMany(TierPriceItems::class, 'tier_price_id');
    }
    public function products(){
        return $this->belongsToMany(Product::class, 'product_of_tier_prices')
                        ->withTimestamps()->wherePivot('deleted_at', NULL);
    }
}
