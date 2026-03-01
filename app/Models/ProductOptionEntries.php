<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionEntries extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_option_entries';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'title',
        'type',
        'status',
    ];
     protected $hidden = ['created_at', 'updated_at', 'deleted_at','sort'];
    public function attributes() {
        return $this->hasMany(ProductOptionAttribute::class, 'product_entries_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
