<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class quote extends Model
{
    protected $table = 'bills';
    protected $fillable = [
        'total_ttc','ht_price', 'rate_tva', 'price_tva',
        'fiscal_timber','QuoteNum','DateFacturation',
       'description' , 'client_id', 'inWord'
    ];



    public function item()
    {
        return $this->hasMany(Item::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
