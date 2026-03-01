<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTimelines extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'inventory_timelines';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'inventory_id',
        'comments'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

   
}
