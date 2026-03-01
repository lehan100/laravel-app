<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'posts';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'category_id',
        'entype_id',
        'name',
        'alias',
        'picture'
    ];
    protected $hidden = [ 'updated_at', 'deleted_at','sort'];
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function contents() {
        return $this->hasOne(EntypeContent::class, "entype_id", "entype_id");
    }
    public function url() {
        return $this->hasOne(UrlRewrite::class, "post_id", 'id');
    }
}
