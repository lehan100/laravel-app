<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class MediaPosition extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'media_positions';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'code',
        'mode'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function medias() {
        return $this->hasMany(MediaBanner::class, 'position_id');
    }

}
