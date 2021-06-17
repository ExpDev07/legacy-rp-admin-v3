<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use kanalumaddela\LaravelSteamLogin\SteamUser;
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
        'is_soft_banned',
        'playtime',
        'total_joins',
        'priority_level',
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
        'is_soft_banned'  => 'boolean',
        'playtime'        => 'integer',
        'total_joins'     => 'integer',
        'priority_level'  => 'integer',
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
     * Gets the avatar attribute.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return $this->getSteamUser()->avatar ?? 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
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

        return array_merge([$identifier], $identifiers);
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
        return ($this->is_staff ?? false) || $this->isSuperAdmin();
    }

    /**
     * Checks whether this player is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin ?? false;
    }

    /**
     * Checks whether player is banned.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return !is_null($this->getActiveBan());
    }

    /**
     * Gets the active ban.
     *
     * @return Ban
     */
    public function getActiveBan(): ?Ban
    {
        return $this
            ->bans()
            ->get()
            ->filter(fn(Ban $ban) => !$ban->hasExpired())
            ->first();
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

    /**
     * Gets the steam user.
     *
     * @return SteamUser
     */
    public function getSteamUser(): SteamUser
    {
        $steam = new SteamUser($this->getSteamID()->ConvertToUInt64());
        $steam->getUserInfo();

        return $steam;
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

    /**
     * Returns the online status of the player
     *
     * @param string $steamIdentifier
     * @param bool $useCache
     * @return PlayerStatus
     */
    public static function getOnlineStatus(string $steamIdentifier, bool $useCache): PlayerStatus
    {
        $serverIps = explode(',', env('OP_FW_SERVERS', ''));

        if (!$serverIps) {
            return new PlayerStatus(PlayerStatus::STATUS_UNAVAILABLE, '', 0);
        }

        $validServer = false;
        foreach ($serverIps as $serverIp) {
            if ($serverIp) {
                $validServer = true;
                $steamIdentifiers = Server::fetchSteamIdentifiers($serverIp, $useCache);

                if (isset($steamIdentifiers[$steamIdentifier])) {
                    return new PlayerStatus(PlayerStatus::STATUS_ONLINE, $serverIp, intval($steamIdentifiers[$steamIdentifier]));
                }
            }
        }

        if (!$validServer) {
            return new PlayerStatus(PlayerStatus::STATUS_UNAVAILABLE, '', 0);
        }

        return new PlayerStatus(PlayerStatus::STATUS_OFFLINE, '', 0);
    }

    /**
     * Returns a map of steamIdentifier->player_name
     * This is used instead of a left join as it appears to be a lot faster
     *
     * @param array $source
     * @param string $sourceKey
     * @return array
     */
    public static function fetchSteamPlayerNameMap(array $source, string $sourceKey): array
    {
        $identifiers = [];
        foreach ($source as $entry) {
            if (!in_array($entry[$sourceKey], $identifiers)) {
                $identifiers[] = $entry[$sourceKey];
            }
        }

        $players = self::query()->whereIn('steam_identifier', $identifiers)->select([
            'steam_identifier', 'player_name',
        ])->get();
        $playerMap = [];
        foreach ($players as $player) {
            $playerMap[$player->steam_identifier] = $player->player_name;
        }

        if (empty($playerMap)) {
            $playerMap['empty'] = 'empty';
        }

        return $playerMap;
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
    } catch (Exception $ex) {
        return null;
    }
}
