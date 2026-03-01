<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'districts';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'province_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'sort'];

    public function province() {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function wards() {
        return $this->hasMany(Ward::class, 'district_id');
    }

    public function ward() {
        return $this->hasOne(Ward::class, 'district_id', 'id');
    }
}
