<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InterfaceModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Province extends Model
{

    use HasFactory,
        SoftDeletes;

    protected $table = 'provinces';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'type'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function district()
    {
        return $this->hasMany(District::class, 'province_id');
    }

    public function districtOne()
    {
        return $this->hasOne(District::class, 'province_id', 'id');
    }
}
