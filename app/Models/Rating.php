<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_ratings';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'phone',
        'parent_id',
        'product_id',
        'content',
        'images',
        'rating',
        'is_purchase',
        'status'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
