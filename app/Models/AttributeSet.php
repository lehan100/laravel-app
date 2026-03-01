<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
     use HasFactory,
        SoftDeletes;

    protected $table = 'attribute_sets';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'alias',
        'type'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function attributes() {
        return $this->hasMany(AttributeSetValue::class, 'attribute_set_id');
    }
}
