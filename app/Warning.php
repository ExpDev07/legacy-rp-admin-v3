<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A warning which can be given to players.
 *
 * @package App
 *
 * @property int issuer_id
 * @property string type
 * @property string message
 */
class Warning extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'issuer_id', 'message',
    ];

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function player() : BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Gets the issuer relationship.
     *
     * @return BelongsTo
     */
    public function issuer() : BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

}
