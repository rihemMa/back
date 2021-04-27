<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class quote extends Model
{
    protected $table = 'quotes';
    protected $fillable = [
        'total_ttc','ht_price', 'rate_tva', 'price_tva',
        'fiscal_timber','q
        uoteNum','DateFacturation',
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
