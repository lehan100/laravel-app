<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TierPriceItems extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'tier_price_items';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'tier_price_id',
        'order_qty',
        'type',
        'special_percent',
        'special_price',
        'status'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
