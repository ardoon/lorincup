<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{

    protected $fillable = ['title','field','description','participants'];

    public function tables()
    {
        return $this->hasMany('App\Models\Table');
    }


}
