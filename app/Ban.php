<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
