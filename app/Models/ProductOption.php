<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_options';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'product_id',
        'title',
        'type',
        'status',
        'sort'
    ];
     protected $hidden = ['created_at', 'updated_at', 'deleted_at','sort'];
    public function attributes() {
        return $this->hasMany(ProductOptionAttribute::class, 'option_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
