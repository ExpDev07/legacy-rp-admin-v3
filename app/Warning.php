<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A warning which can be given to players.
 *
 * @package App
 */
class Warning extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'issuer_id', 'type', 'message'
    ];

    /**
     * Gets the player that received this warning.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
    /**
     * Gets the player that issued this warning.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issuer()
    {
        return $this->belongsTo(Player::class);
    }

}
