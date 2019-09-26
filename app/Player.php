<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
     * Whether to use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

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

    /***
     * Gets the characters relationship.
     *
     * @return HasMany
     */
    public function characters() : HasMany
    {
        return $this->hasMany(Character::class, 'identifier', 'identifier');
    }

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
