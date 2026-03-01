<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SearchTerms extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'search_terms';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'query_text',
        'num_results',
        'popularity'

    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
