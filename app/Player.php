<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SteamID;

class Player extends Model
{
    use HasSteam;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 'name', 'identifiers'
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
     * {@inheritdoc}
     */
    public function steam_id()
    {
        return new SteamID(self::get_account_id($this->identifier));
    }

    /**
     * Gets the player's ban (if banned).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ban()
    {
        return $this->hasOne('identifier', 'identifier');
    }

    /**
     * Gets the steam account id from the provided identifier.
     *
     * @param string $identifier The identifier.
     * @return string The account id.
     */
    private static function get_account_id(string $identifier)
    {
        str_replace('steam:', '', $identifier); // clean the identifier.
        return hexdec($identifier);
    }

}
