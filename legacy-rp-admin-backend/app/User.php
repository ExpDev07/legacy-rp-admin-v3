<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SteamID;

class User extends Authenticatable
{
    use Notifiable, HasSteam;

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
     * @see User::identifier().
     *
     * @return string
     */
    protected function getIdentifierAttribute()
    {
        return $this->identifier();
    }

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
     * {@inheritdoc}
     */
    public function steam_id()
    {
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
