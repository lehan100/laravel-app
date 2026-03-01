<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductOfOptionEntries extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_of_option_entries';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'product_option_entries_id',
        'product_id',
        'order',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function products() {
        return $this->belongsTo(Product::class, "id","product_id");
    }
    public function product() {
        return $this->hasOne(Product::class, "id","product_id");
    }
    
}
