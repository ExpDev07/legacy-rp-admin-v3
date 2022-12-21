<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A warning which can be given to players.
 *
 * @package App
 */
class CasinoLog extends Model
{
    const ValidGames = [
        self::GameBlackJack,
        self::GameTracks,
        self::GameSlots,
    ];

    const GameBlackJack = 'blackjack';
    const GameTracks    = 'tracks';
    const GameSlots     = 'slots';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'casino_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'license_identifier',
        'character_id',
        'game',
        'money_won',
        'bet_placed',
        'details',
        'timestamp',
    ];

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'license_identifier', 'license_identifier');
    }

    /**
     * Gets the character relationship.
     *
     * @return BelongsTo
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character_id', 'character_id');
    }

}
