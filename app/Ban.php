<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A ban that can be issued by a player and received by a players.
 *
 * @package App
 */
class Ban extends Model
{
    use HasFactory;

    /**
     * Column name for when the model was created.
     */
    const CREATED_AT = 'timestamp';

    /**
     * Column name for when the model was last updated.
     */
    const UPDATED_AT = 'timestamp';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_bans';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ban_hash',
        'identifier',
        'smurf_account',
        'creator_name',
        'reason',
        'timestamp',
        'expire',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'timestamp' => 'datetime',
        'expire'    => 'datetime',
    ];

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function player() : BelongsTo
    {
        return $this->belongsTo(Player::class, 'steam_identifier', 'identifier');
    }

    /**
     * Gets the issuer relationship.
     *
     * @return BelongsTo
     */
    public function issuer() : BelongsTo
    {
        return $this->belongsTo(Player::class, 'creator_name', 'player_name');
    }

}
