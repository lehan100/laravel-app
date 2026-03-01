<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPayment extends Model {

    use HasFactory,
        SoftDeletes;

    protected $table = 'order_payments';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'order_id',
        'payment_code',
        'payment_name',
        'history'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

   
}
