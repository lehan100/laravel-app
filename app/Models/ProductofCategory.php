<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductofCategory extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_of_categories';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'category_id',
        'product_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function products() {
        return $this->belongsTo(Product::class, "id","product_id");
    }
    public function product() {
        return $this->hasOne(Product::class, "id","product_id");
    }
}
