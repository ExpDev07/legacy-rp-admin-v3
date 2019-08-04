<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 'name'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'identifier', 'identifier');
    }

}
