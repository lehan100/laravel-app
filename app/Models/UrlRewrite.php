<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UrlRewrite extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'url_rewrites';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'path',
        'route',
        'category_id',
        'product_id',
        'post_id',
        'sale_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
