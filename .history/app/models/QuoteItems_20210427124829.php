<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class QuoteItems extends Model
{
    protected $table = 'quoteItems' ;
    protected $fillable = [
        'designation','quantity','u_price','total_price','quote_id'
    ];
}
