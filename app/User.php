<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @package App
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webpanel_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'name',
        'avatar',
        'remember_token',
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
     * Gets the game-server identifier.
     *
     * @return string
     */
    protected function getSteamIdentifierAttribute() : string
    {
        return 'steam:' . dechex($this->account_id);
    }

    /**
     * Checks whether this user is a staff member.
     *
     * @return bool
     */
    public function isStaff() : bool
    {
        return !is_null($this->player) && $this->player->isStaff();
    }

    /**
     * Gets the player relationship.
     *
     * @return HasOne
     */
    public function player() : HasOne
    {
        return $this->hasOne(Player::class, 'steam_identifier', 'steam_identifier');
    }

}
