<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function promos()
    {
    	return $this->belongsToMany('App\Promo')->where('status','published');
    }

    public function bigGames()
    {
    	return $this->belongsToMany('App\Game');
    }

    public function notBroadcasted()
    {
        return $this->belongsToMany('App\Game','game_broadcast_store');
    }

    public function users()
    {
    	return $this->hasMany('App\User');
    }
}
