<?php

namespace App;

use App\Helpers\CacheHelper;
use App\Helpers\GeneralHelper;
use App\Http\Resources\CharacterResource;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
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
        'license_identifier',
        'player_name',
        'player_aliases',
        'identifiers',
		'last_used_identifiers',
        'ips',
        'player_tokens',
        'is_staff',
        'is_senior_staff',
        'is_super_admin',
        'is_trusted',
        'is_debugger',
        'is_soft_banned',
        'panel_drug_department',
        'is_soft_banned',
        'playtime',
        'total_joins',
        'priority_level',
        'last_connection',
        'enabled_commands',
        'panel_tag',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'identifiers' => 'array',
        'last_used_identifiers' => 'array',
        'ips' => 'array',
        'player_tokens' => 'array',
        'player_aliases' => 'array',
        'enabled_commands' => 'array',
        'user_data' => 'array',
        'last_connection' => 'datetime',
        'is_trusted' => 'boolean',
        'is_staff' => 'boolean',
        'is_super_admin' => 'boolean',
        'is_debugger' => 'boolean',
        'panel_drug_department' => 'boolean',
        'is_soft_banned' => 'boolean',
        'playtime' => 'integer',
        'total_joins' => 'integer',
        'priority_level' => 'integer',
    ];

    public static function resolveTags(bool $refreshCache = false): array
    {
        if ($refreshCache || !CacheHelper::exists('tags')) {
            $tags = self::query()->select(['panel_tag'])->whereNotNull('panel_tag')->groupBy('panel_tag')->get()->toArray();

            CacheHelper::write('tags', $tags, CacheHelper::WEEK);
        }

        return CacheHelper::read('tags', []);
    }

    public function getActiveMute(): ?array
    {
        $data = $this->user_data ?? [];

        if (!isset($data['muted']) || !$data['muted']) {
            return null;
        }

        $mute = $data['muted'];

        if ($mute['expiryTimestamp'] && $mute['expiryTimestamp'] < time()) {
            return null;
        }

        return [
            'reason' => $mute['reason'] ?? null,
            'expires' => $mute['expiryTimestamp'] ?? null,
            'creator' => $mute['creatorName'] ?? null
        ];
    }

    /**
     * @param string $player
     * @param Request $request
     * @return Player|array|null
     */
    public static function resolvePlayer(string $player, Request $request)
    {
        $resolved = Player::query()->select()->where('license_identifier', '=', $player)->first();

        if ($resolved and $resolved instanceof Player) {
            return $resolved;
        }

        return null;
    }

    /**
     * Gets the route key name.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'license_identifier';
    }

    /**
     * @return string
     */
    public function getSteamIdentifier(): ?string
    {
        return $this->getIdentifier('steam') ?? null;
    }

    /**
     * Returns the discord user info (username, avatar, etc.)
     *
     * @return array|null
     */
    public function getDiscordInfo(): array
    {
        $ids = $this->getDiscordIDs();

        $users = [];

        foreach ($ids as $id) {
            $user = DiscordUser::getUser($id);

            $users[$id] = $user ? $user->toArray() : null;
        }

        return $users;
    }

    /**
     * Returns the discord user id
     *
     * @return array
     */
    public function getDiscordIDs(): array
    {
        $discords = [];

        $ids = $this->getIdentifiers();

        foreach ($ids as $id) {
            if (Str::startsWith($id, 'discord:')) {
                $discord = str_replace('discord:', '', $id);

                if (!in_array($discord, $discords)) {
                    $discords[] = $discord;
                }
            }
        }

        return $discords;
    }

    /**
     * Gets a URL to the player's steam profile.
     *
     * @return string
     */
    public function getSteamProfileUrl(): ?string
    {
		$steamId = $this->getSteamID();

        return $steamId ? self::STEAM_INVITE_URL . $steamId->RenderSteamInvite() : null;
    }

    /**
     * Gets all the identifiers.
     *
     * @return array
     */
    public function getIdentifiers(): array
    {
        $identifiers = $this->identifiers ?? [];
        $identifiers[] = $this->license_identifier;

        return array_values(
            array_unique(
                $identifiers
            )
        );
    }

    /**
     * Gets the last used identifiers.
     *
     * @return array
     */
    public function getLastUsedIdentifiers(): array
    {
        $identifiers = $this->last_used_identifiers ?? [];

		if (is_string($identifiers)) {
			$identifiers = json_decode($identifiers, true) ?? [];
		}

        return array_values(
            array_unique(
                $identifiers
            )
        );
    }

    /**
     * Gets all the ips.
     *
     * @return array
     */
    public function getIps(): array
    {
        $ips = $this->ips ?? [];

        return array_values(
            array_unique(
                $ips
            )
        );
    }

    /**
     * Gets all the tokens.
     *
     * @return array
     */
    public function getTokens(): array
    {
        $tokens = $this->player_tokens ?? [];

        return array_values(
            array_unique(
                $tokens
            )
        );
    }

    /**
     * Returns all bannable identifiers
     *
     * @return array
     */
    public function getBannableIdentifiers(): array
    {
        return array_values(array_filter($this->getIdentifiers(), function ($identifier) {
            return !Str::startsWith($identifier, 'ip:');
        }));
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
        return ($this->is_staff ?? false) || $this->isSeniorStaff();
    }

    /**
     * Checks whether this player is a senior staff member.
     *
     * @return bool
     */
    public function isSeniorStaff(): bool
    {
        return ($this->is_senior_staff ?? false) || $this->isSuperAdmin();
    }

    /**
     * Checks whether this player is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return ($this->is_super_admin ?? false) || $this->isRoot();
    }

    /**
     * Checks whether this player has root access to the panel.
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->license_identifier && GeneralHelper::isUserRoot($this->license_identifier);
    }

    /**
     * Checks whether this player is a debugger.
     *
     * @return bool
     */
    public function isDebugger(): bool
    {
        return $this->isSuperAdmin() || $this->is_debugger;
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
        return Ban::query()
            ->where('identifier', '=', $this->license_identifier)
            ->get()
            ->first();
    }

    /**
     * Gets the steam id.
     *
     * @return SteamID|null
     */
    public function getSteamID(): ?SteamID
    {
        return get_steam_id($this->getSteamIdentifier());
    }

    /**
     * Gets the characters' relationship.
     *
     * @return HasMany
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'license_identifier', 'license_identifier')->orderBy('character_slot');
    }

    /**
     * Gets the logs' relationship.
     *
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'identifier', 'license_identifier');
    }

    /**
     * Gets the warnings' relationship.
     *
     * @return HasMany
     */
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class, 'player_id', 'user_id');
    }

    public function fasterWarnings(bool $includeHidden = false): array
    {
        $warnings = Warning::query()
            ->select(['id', 'message', 'warning_type', 'created_at', 'updated_at', 'player_name', 'license_identifier', 'can_be_deleted'])
            ->where('player_id', '=', $this->user_id)
            ->leftJoin('users', 'issuer_id', '=', 'user_id');

        if (!$includeHidden) {
            $warnings = $warnings->where('warning_type', '!=', Warning::TypeHidden);
        }

        $warnings = $warnings->get();

        $plainWarnings = [];
        foreach ($warnings as $warning) {
            $plainWarnings[] = [
                'id' => $warning->id,
                'message' => $warning->message,
                'warningType' => $warning->warning_type,
                'createdAt' => $warning->created_at,
                'updatedAt' => $warning->updated_at,
                'canDelete' => $warning->can_be_deleted,
                'issuer' => [
                    'playerName' => $warning->player_name,
                    'licenseIdentifier' => $warning->license_identifier
                ]
            ];
        }

        return $plainWarnings;
    }

    /**
     * Gets the panel_logs relationship.
     *
     * @return HasMany
     */
    public function panelLogs(): HasMany
    {
        return $this->hasMany(PanelLog::class, 'target_identifier', 'license_identifier');
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
     * Returns a map of licenseIdentifier->serverId,server for each online player
     *
     * @param bool $useCache
     * @return array|null
     */
    public static function getAllOnlinePlayers(bool $useCache): ?array
    {
        $serverIps = explode(',', env('OP_FW_SERVERS', ''));

        if (!$serverIps) {
            return [];
        }

        $result = [];
        foreach ($serverIps as $serverIp) {
            if ($serverIp) {
                $licenseIdentifiers = Server::fetchLicenseIdentifiers($serverIp, $useCache);

                if ($licenseIdentifiers === null) {
                    return null;
                }

                foreach ($licenseIdentifiers as $key => $player) {
                    if (!isset($result[$key])) {
                        $flags = $player['flags'];

                        $fake = !!($flags & 2);

						$minigame = !!($flags & 4);
						$camCords = !!($flags & 8);
						$queue = !!($flags & 16);

                        $result[$key] = [
                            'id' => intval($player['source']),
                            'character' => $player['character'],
                            'license' => $key,
                            'server' => $serverIp,
                            'fakeDisconnected' => $fake,
                            'fakeName' => !!($flags & 1) ? $player['name'] : null,
							'minigame' => $minigame,
							'camCords' => $camCords,
							'queue' => $queue
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns the online status of the player
     *
     * @param string $licenseIdentifier
     * @param bool $useCache
     * @return PlayerStatus
     */
    public static function getOnlineStatus(string $licenseIdentifier, bool $useCache, bool $trueStatus = false): PlayerStatus
    {
        $serverIps = explode(',', env('OP_FW_SERVERS', ''));

        if (!$serverIps) {
            return new PlayerStatus(PlayerStatus::STATUS_UNAVAILABLE, '', 0);
        }

        $players = self::getAllOnlinePlayers($useCache);

        if ($players === null) {
            return new PlayerStatus(PlayerStatus::STATUS_UNAVAILABLE, '', 0);
        }

        if (isset($players[$licenseIdentifier])) {
            $player = $players[$licenseIdentifier];

            if (!$trueStatus && ($player['fakeDisconnected'] || $player['fakeName'])) {
                return new PlayerStatus(PlayerStatus::STATUS_OFFLINE, '', 0);
            }

            return new PlayerStatus(PlayerStatus::STATUS_ONLINE, $player['server'], $player['id'], $player['character'], $player['fakeName'], [
				'minigame' => $player['minigame'],
				'camCords' => $player['camCords'],
				'queue' => $player['queue']
			]);
        }

        return new PlayerStatus(PlayerStatus::STATUS_OFFLINE, '', 0);
    }

    /**
     * Returns a map of licenseIdentifier->player_name
     * This is used instead of a left join as it appears to be a lot faster
     *
     * @param array $source
     * @param string|array $sourceKey
     * @return array
     */
    public static function fetchLicensePlayerNameMap(array $source, $sourceKey): array
    {
        if (!is_array($sourceKey)) {
            $sourceKey = [$sourceKey];
        }

        $identifiers = [];
        foreach ($source as $entry) {
            foreach ($sourceKey as $key) {
                $d = is_array($entry) ? $entry[$key] : $entry->$key;

                if (!in_array($d, $identifiers)) {
                    $identifiers[] = $d;
                }
            }
        }

        $identifiers = array_values(array_unique($identifiers));
        $playerMap = CacheHelper::loadLicensePlayerNameMap($identifiers);

        if (empty($playerMap)) {
            $playerMap['empty'] = 'empty';
        }

        return $playerMap;
    }

	public static function findPlayerBySteam(string $steamIdentifier): ?Player
	{
		$players = Player::query()->where('identifiers', 'LIKE', "%\"" . $steamIdentifier . "\"%")->get();

		foreach($players as $player) {
			$identifiers = $player->identifiers ?? [];

			foreach ($identifiers as $identifier) {
				if (Str::startsWith($identifier, 'steam:')) {
					if ($identifier === $steamIdentifier) {
						return $player;
					}

					break;
				}
			}
		}

		return null;
	}

    public static function getIdentifierLabel(string $identifier): ?string
    {
        $type = explode(':', $identifier)[0];

        switch ($type) {
            case 'ip':
                return 'IP-Address';
            case 'steam':
                return 'Steam Account';
            case 'discord':
                return 'Discord Account';
            case 'fivem':
                return 'FiveM Account';
            case 'license':
            case 'license2':
                return 'Rockstar Account';
            case 'live':
                return 'Microsoft Account';
            case 'xbl':
                return 'XBox Live';
            default:
                return null;
        }
    }

    public static function isValidIdentifier(string $identifier): bool
    {
        return sizeof(explode(':', $identifier)) === 2 && self::getIdentifierLabel($identifier) !== null;
    }
}

/**
 * Takes the given identifier and tries to resolve a SteamID from it.
 *
 * @param string $identifier
 * @return SteamID|null
 */
function get_steam_id(?string $identifier): ?SteamID
{
	if (!$identifier) {
		return null;
	}

    try {
        // Get rid of any prefix.
        return new SteamID(hexdec(explode('steam:', $identifier)[1]));
    } catch (Exception $ex) {
        return null;
    }
}
