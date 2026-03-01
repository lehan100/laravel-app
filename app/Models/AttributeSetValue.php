<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AttributeSetValue extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $table = 'attribute_set_values';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'alias',
        'type'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function attributeSet(){
        return $this->belongsTo(AttributeSet::class,'attribute_set_id');
    }
    
    public function productAttributeSets() {
        return $this->hasMany(ProductAttributeSet::class,'attribute_set_ids','id');
    }
}
