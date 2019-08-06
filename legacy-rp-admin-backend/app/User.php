<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SteamID;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The link used for Steam's new invite code.
     *
     * @var string
     */
    protected static $STEAM_INVITE_LINK = 'http://s.team/p/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'name', 'avatar', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
    ];

    /**
     * Gets the identifier, which is a HEX-ified version of the account id with a "steam:" prefix. Used as a way of
     * identifying users/players on the game-server.
     *
     * @return string
     */
    public function identifier()
    {
        return 'steam:' . dechex($this->account_id);
    }

    /**
     * Gets a link to the user's steam profile.
     *
     * @return string The link.
     */
    public function steam_profile()
    {
        // Per https://github.com/xPaw/SteamID.php, we should use the "RenderSteamInvite" method to get the code which
        // can be used with the steam invite/profile link.
        return self::$STEAM_INVITE_LINK . $this->steam_id()->RenderSteamInvite();
    }

    /**
     * Gets the whole steam id instance. Can be used to get multiple different types of steam IDs.
     *
     * @return SteamID The steam id.
     */
    public function steam_id()
    {
        // Just wrap the account id with an instance of SteamID by xPaw.
        return new SteamID($this->account_id);
    }

    /**
     * Gets the player on the game-server associated with this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function player()
    {
        return $this->hasOne(Player::class, 'identifier', 'identifier');
    }

}
