<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionAttribute extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_option_attributes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'title',
        'option_id',
        'product_entries_id',
        'price',
        'special_price',
        'special_price_from',
        'special_price_to',
        'picture',
        'color',
        'status'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at','sort'];
}
