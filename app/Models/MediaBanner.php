<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class MediaBanner extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'media_banners';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'position_id',
        'category_id',
        'alias_link',
        'picture',
        'sort_content'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function position() {
        return $this->belongsTo(MediaPosition::class, 'position_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
