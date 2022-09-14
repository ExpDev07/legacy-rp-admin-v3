<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A warning which can be given to players.
 *
 * @package App
 */
class Warning extends Model
{
    const ValidTypes = [
        self::TypeNote,
        self::TypeWarning,
        self::TypeStrike,
        self::TypeSystem,
        self::TypeHidden,
    ];

    const TypeNote    = 'note';
    const TypeWarning = 'warning';
    const TypeStrike  = 'strike';
    const TypeSystem  = 'system';
    const TypeHidden  = 'hidden';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'issuer_id',
        'message',
        'warning_type',
        'can_be_deleted',
    ];

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id', 'user_id');
    }

    /**
     * Gets the issuer relationship.
     *
     * @return BelongsTo
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'issuer_id', 'user_id');
    }

}
