<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusMapping extends Model
{
    protected $guarded=[''];
    
    public function receiver()
    {
        return $this->hasOne('App\User', 'id', 'receiver_id');
    }

    public function sender()
    {
        return $this->hasOne('App\User', 'id', 'sender_id');
    }
    
}