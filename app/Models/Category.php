<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'alias',
        'parent_id',
        'sort',
        'page',
        'picture',
        'status',
        'position_menu',
        'position_top',
        'position_main',
        'position_footer',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function category_parents() {
        return $this->hasMany(Category::class, "parent_id");
    }
    public function products() {
        return $this->hasMany(ProductofCategory::class, 'category_id');
    }

    public function contents() {
        return $this->hasOne(EntypeContent::class, "entype_id", "entype_id")
                        ->latest();
    }

    public function banners() {
        return $this->hasMany(MediaBanner::class, "category_id");
    }

    public function url() {
        return $this->hasOne(UrlRewrite::class, "category_id", 'id');
    }

}
