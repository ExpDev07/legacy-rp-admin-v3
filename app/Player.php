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
 * @property string staff
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
        'identifier', 'name', 'identifiers', 'staff', 'playtime'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'identifiers' => 'array',
    ];

    /**
     * Checks whether this player is a staff member.
     *
     * @return bool
     */
    function isStaff() : bool
    {
        return !is_null($this->staff);
    }

    /**
     * Checks whether player is banned.
     *
     * @return bool
     */
    function isBanned() : bool
    {
        return !is_null($this->bans()->first());
    }

    /**
     * Gets the amount of time this player has spent on the server in a nice readable string.
     *
     * @return string
     */
    function getPlayTime() : string
    {
        return self::seconds_to_human($this->playtime);
    }

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
     * Gets the bans relationship.
     *
     * @return HasMany
     */
    public function bans() : HasMany
    {
        return $this->hasMany(Ban::class, 'identifier', 'identifier');
    }

    /**
     * Gets the warnings relationship.
     *
     * @return HasMany
     */
    public function warnings() : HasMany
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Converts the given seconds to a human readable string.
     *
     * https://snippetsofcode.wordpress.com/2012/08/25/php-function-to-convert-seconds-into-human-readable-format-months-days-hours-minutes/
     *
     * @param $ss number
     * @return string
     */
    protected static function seconds_to_human($ss)
    {
        $m = floor(($ss % 3600) / 60);
        $h = floor(($ss % 86400) / 3600);
        $d = floor(($ss % 2592000) / 86400);
        $M = floor($ss / 2592000);

        // Return a friendly string that humans can easily read.
        return "$M months, $d days, $h hours, and $m minutes";
    }

}
