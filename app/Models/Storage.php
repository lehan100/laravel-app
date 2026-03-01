<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Storage extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'storages';
    protected $primaryKey = 'id';
    protected $fillable = [
        'entype_id',
        'data'
    ];

    public function getStorage($entype_id = "") {
        $result = null;
        if ($entype_id != "") {
            $result = $this->select()->where("entype_id", $entype_id)->first();
        }
        return $result;
    }

}
