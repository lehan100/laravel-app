<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{

    use HasFactory,
        SoftDeletes;

    protected $table = 'contacts';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'title',
        'message'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
