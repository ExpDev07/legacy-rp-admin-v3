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
        'steam_identifier',
        'player_name',
        'player_aliases',
        'identifiers',
        'is_staff',
        'is_senior_staff',
        'is_super_admin',
        'is_trusted',
        'is_panel_trusted',
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
        'player_aliases' => 'array',
        'enabled_commands' => 'array',
        'user_data' => 'array',
        'last_connection' => 'datetime',
        'is_trusted' => 'boolean',
        'is_staff' => 'boolean',
        'is_super_admin' => 'boolean',
        'is_panel_trusted' => 'boolean',
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
        if (Str::startsWith($player, 'steam:1100002')) {
            $steam = str_replace('steam:1100002', 'steam:1100001', $player);

            $key = 'fake_' . $steam;

            $status = Player::getOnlineStatus($steam, false, true);

            if ($status && $status->fakeName && $status->character) {
                $resolved = Player::query()->select()->where('steam_identifier', '=', $steam)->first();

                if ($resolved) {
                    $characters = Character::query()->select()->where('character_id', '=', $status->character)->get();

                    $res = [
                        'id' => $resolved->user_id,
                        'avatar' => null,
                        'discord' => null,
                        'steamIdentifier' => $player,
                        'overrideSteam' => $steam,
                        'steam36' => base_convert(str_replace('steam:', '', $player), 16, 36),
                        'playerName' => $status->fakeName,
                        'playTime' => $resolved->playtime,
                        'lastConnection' => $resolved->last_connection,
                        'steamProfileUrl' => $resolved->getSteamProfileUrl() . 'f',
                        'isTrusted' => false,
                        'isDebugger' => false,
                        'isPanelTrusted' => false,
                        'isStaff' => false,
                        'isSeniorStaff' => false,
                        'isSuperAdmin' => false,
                        'isRoot' => false,
                        'isBanned' => false,
                        'warnings' => 0,
                        'ban' => null,
                        'status' => [
                            'status' => PlayerStatus::STATUS_ONLINE,
                            'serverIp' => $status->serverIp,
                            'serverId' => $status->serverId,
                            'serverName' => $status->serverName,
                            'character' => $status->character,
                            'fakeName' => null,
                        ],
                    ];

                    $data = [
                        'player' => $res,
                        'characters' => CharacterResource::collection($characters),
                        'warnings' => [],
                        'panelLogs' => [],
                        'discord' => null,
                        'kickReason' => '',
                        'screenshots' => [],
                        'whitelisted' => false,
                        'tags' => Player::resolveTags()
                    ];

                    CacheHelper::write($key, $data, 3 * CacheHelper::MONTH);
                } else {
                    return null;
                }
            } else if (CacheHelper::exists($key)) {
                $data = CacheHelper::read($key);

                $data['player']['status']['status'] = PlayerStatus::STATUS_OFFLINE;
                $data['player']['status']['character'] = 0;

                CacheHelper::write($key, $data, 3 * CacheHelper::MONTH);
            }

            return CacheHelper::read($key);
        }

        $resolved = Player::query()->select()->where('steam_identifier', '=', $player)->first();

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
        return 'steam_identifier';
    }

    /**
     * Gets the avatar attribute.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return self::getAvatar($this->steam_identifier);
    }

    public static function getAvatar(string $steamIdentifier): string
    {
        $steam = self::getSteamUser($steamIdentifier);

        return $steam && isset($steam['avatar']) ? $steam['avatar'] : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
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
        $identifiers = $this->identifiers ?? [];
        $identifiers[] = $this->steam_identifier;

        return array_values(
            array_unique(
                $identifiers
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
        return GeneralHelper::isUserRoot($this->steam_identifier);
    }

    /**
     * Checks whether this player is a trusted panel user.
     *
     * @return bool
     */
    public function isPanelTrusted(): bool
    {
        return $this->isSuperAdmin() || $this->is_panel_trusted;
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
            ->where('identifier', '=', $this->steam_identifier)
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
        return get_steam_id($this->steam_identifier);
    }

    /**
     * Gets the steam user.
     *
     * @param string $steamIdenfier
     * @return array|null
     */
    public static function getSteamUser(string $steamIdenfier): ?array
    {
        $steam = get_steam_id($steamIdenfier);

        if ($steam) {
            $id = $steam->ConvertToUInt64();
            $key = 'steam_user_' . md5($id);

            if (CacheHelper::exists($key)) {
                return CacheHelper::read($key, []);
            }

            try {
                $steam = new SteamUser($id);
                $steam->getUserInfo();

                $info = $steam->toArray();
                CacheHelper::write($key, $info, CacheHelper::DAY);

                return $info;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Gets the characters' relationship.
     *
     * @return HasMany
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'steam_identifier', 'steam_identifier')->orderBy('character_slot');
    }

    /**
     * Gets the logs' relationship.
     *
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'identifier', 'steam_identifier');
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
            ->select(['id', 'message', 'warning_type', 'created_at', 'updated_at', 'player_name', 'steam_identifier', 'can_be_deleted'])
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
                    'avatar' => $warning->steam_identifier ? Player::getAvatar($warning->steam_identifier) : null,
                    'playerName' => $warning->player_name,
                    'steamIdentifier' => $warning->steam_identifier
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
        return $this->hasMany(PanelLog::class, 'target_identifier', 'steam_identifier');
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
     * Returns a map of steamIdentifier->serverId,server for each online player
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
                $steamIdentifiers = Server::fetchSteamIdentifiers($serverIp, $useCache);

                if ($steamIdentifiers === null) {
                    return null;
                }

                foreach ($steamIdentifiers as $key => $player) {
                    if (!isset($result[$key])) {
                        $flags = $player['flags'];

                        $fake = $flags / 2 >= 1;
                        if ($fake) {
                            $fake -= 2;
                        }

                        $result[$key] = [
                            'id' => intval($player['source']),
                            'character' => $player['character'],
                            'steam' => $key,
                            'server' => $serverIp,
                            'fakeDisconnected' => $fake,
                            'fakeName' => $flags !== 0 ? $player['name'] : null,
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
     * @param string $steamIdentifier
     * @param bool $useCache
     * @return PlayerStatus
     */
    public static function getOnlineStatus(string $steamIdentifier, bool $useCache, bool $trueStatus = false): PlayerStatus
    {
        $serverIps = explode(',', env('OP_FW_SERVERS', ''));

        if (!$serverIps) {
            return new PlayerStatus(PlayerStatus::STATUS_UNAVAILABLE, '', 0);
        }

        $players = self::getAllOnlinePlayers($useCache);

        if ($players === null) {
            return new PlayerStatus(PlayerStatus::STATUS_UNAVAILABLE, '', 0);
        }

        if (isset($players[$steamIdentifier])) {
            $player = $players[$steamIdentifier];

            if (!$trueStatus && ($player['fakeDisconnected'] || $player['fakeName'])) {
                return new PlayerStatus(PlayerStatus::STATUS_OFFLINE, '', 0);
            }

            return new PlayerStatus(PlayerStatus::STATUS_ONLINE, $player['server'], $player['id'], $player['character'], $player['fakeName']);
        }

        return new PlayerStatus(PlayerStatus::STATUS_OFFLINE, '', 0);
    }

    /**
     * Returns a map of steamIdentifier->player_name
     * This is used instead of a left join as it appears to be a lot faster
     *
     * @param array $source
     * @param string|array $sourceKey
     * @return array
     */
    public static function fetchSteamPlayerNameMap(array $source, $sourceKey): array
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
        $playerMap = CacheHelper::loadSteamPlayerNameMap($identifiers);

        if (empty($playerMap)) {
            $playerMap['empty'] = 'empty';
        }

        return $playerMap;
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
function get_steam_id(string $identifier): ?SteamID
{
    try {
        // Get rid of any prefix.
        return new SteamID(hexdec(explode('steam:', $identifier)[1]));
    } catch (Exception $ex) {
        return null;
    }
}
