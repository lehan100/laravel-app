<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EntypeContent extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'entype_contents';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'entype_id',
        'sort_content',
        'content',
        'title',
        'keyword',
        'description'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at','content'];
}
