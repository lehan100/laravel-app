<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ward extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'wards';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'district_id'

    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at','sort'];
    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }
}
