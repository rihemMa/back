<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class QuoteItems extends Model
{
    protected $table = 'items' ;
    protected $fillable = [
        'designation','quantity','u_price','total_price','bill_id'
    ];
}
