<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{

    protected $guarded = ['id'];

    public function tournament()
    {
        return $this->belongsTo('App\Models\Tournament');
    }

}
