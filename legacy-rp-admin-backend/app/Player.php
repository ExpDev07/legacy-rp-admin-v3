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
        'identifier', 'name'
    ];

    /**
     * {@inheritdoc}
     */
    public function steam_id()
    {
        return new SteamID(self::get_account_id($this->identifier));
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
