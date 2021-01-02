<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use SteamID;

/**
 * @package App
 */
class Player extends Model
{
    use HasFactory;

    /**
     * The link used for Steam's new invite code.
     */
    const STEAM_INVITE_URL = 'http://s.team/p/';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Whether to use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'steam_identifier',
        'player_name',
        'identifiers',
        'is_staff',
        'is_super_admin',
        'playtime',
        'last_connection',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'identifiers'     => 'array',
        'last_connection' => 'datetime',
        'is_staff'        => 'boolean',
        'is_super_admin'  => 'boolean',
        'playtime'        => 'integer',
    ];

    /**
     * Gets the route key name.
     *
     * @return string|mixed
     */
    public function getRouteKeyName()
    {
        return 'steam_identifier';
    }

    /**
     * Gets a URL to the player's steam profile.
     *
     * @return string
     */
    public function getSteamProfileUrl(): string
    {
        return self::STEAM_INVITE_URL . $this->getSteamID()->RenderSteamInvite();
    }


    /**
     * Gets all the identifiers.
     *
     * @return array
     */
    public function getIdentifiers(): array
    {
        $identifier = $this->identifier;
        $identifiers = $this->identifiers;

        if (in_array($identifier, $identifiers, true)) {
            return $identifiers;
        }

        return array_merge([ $identifier ], $identifiers);
    }

    /**
     * Gets the identifier for the provided key.
     *
     * @param $key
     * @return mixed|null
     */
    public function getIdentifier($key)
    {
        foreach ($this->getIdentifiers() as $identifier) {
            if (strpos($identifier, $key) === 0) return $identifier;
            continue;
        }
        return null;
    }

    /**
     * Checks whether this player is a staff member.
     *
     * @return bool
     */
    public function isStaff(): bool
    {
        return $this->is_staff || $this->is_super_admin;
    }

    /**
     * Checks whether player is banned.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return !is_null($this->bans()->first());
    }

    /**
     * Gets the steam id.
     *
     * @return SteamID|null
     */
    public function getSteamID(): ?SteamID
    {
        return get_steam_id($this->steam_identifier);
    }

    /***
     * Gets the characters relationship.
     *
     * @return HasMany
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'steam_identifier', 'steam_identifier');
    }

    /**
     * Gets the logs relationship.
     *
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'identifier', 'steam_identifier');
    }

    /**
     * Gets the warnings relationship.
     *
     * @return HasMany
     */
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class, 'player_id', 'user_id');
    }

    /**
     * Gets the query for bans.
     *
     * @return Builder
     */
    public function bans(): Builder
    {
        return Ban::query()->whereIn('identifier', $this->getIdentifiers());
    }

}

/**
 * Takes the given identifier and tries to resolve a SteamID from it.
 *
 * @param string $identifier
 * @return SteamID|null
 */
function get_steam_id(string $identifier): ?SteamID
{
    try {
        // Get rid of any prefix.
        return new SteamID(hexdec(explode('steam:', $identifier)[1]));
    }
    catch (Exception $ex) {
        return null;
    }
}
