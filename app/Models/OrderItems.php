<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItems extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'order_items';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'order_id',
        'name',
        'sku',
        'qty',
        'price',
        'option',
        'path',
        'picture',
        'gift'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

   
}
