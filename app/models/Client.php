<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{

    use SoftDeletes;
    protected $table = 'clients' ;
    protected $fillable = [
        'client_name','email','webSite','address','matFisc','fax','phone'
    ];



    public function project()
    {
        return $this->HasMany(Project::class) ;
    }

    public function bill()
    {
        return $this->HasMany(Bill::class) ;
    }

    public function contact()
    {
        return $this->HasMany(Contact::class) ;
    }
}
