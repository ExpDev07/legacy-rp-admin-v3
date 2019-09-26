<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SteamID;

/**
 * The link used for Steam's new invite code.
 */
const STEAM_INVITE_URL = 'http://s.team/p/';

/**
 * @package App
 *
 * @property string identifier
 * @property string name
 * @property bool staff
 * @property array identifiers
 */
class Player extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 'name', 'identifiers', 'staff'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'identifiers' => 'array',
        'staff'       => 'boolean',
    ];

    /**
     * Gets the ban relationship.
     *
     * @return HasOne
     */
    public function ban() : HasOne
    {
        return $this->hasOne('identifier', 'identifier');
    }

}
