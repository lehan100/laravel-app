<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttributeSet extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'product_attribute_sets';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'alias',
        'product_id',
        'attribute_set_ids'
    ];
    
     protected $hidden = ['created_at', 'updated_at', 'deleted_at','sort'];
    public function addFillable($column){
        static::retrieved(function($model) use ($column){
            $model->fillable = array_merge( $this->fillable, [$column]);
        });
        return $this;
    }

    public function label(){
        return $this->hasOne(AttributeSet::class,'alias','alias');
    }
    public function value(){
        return $this->hasOne(AttributeSetValue::class,'id','attribute_set_ids');
    }
}
