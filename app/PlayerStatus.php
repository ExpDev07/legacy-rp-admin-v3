<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use kanalumaddela\LaravelSteamLogin\SteamUser;
use SteamID;

/**
 * @package App
 */
class PlayerStatus
{
    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_OFFLINE     = 'offline';
    const STATUS_ONLINE      = 'online';

    /**
     * Connection Status
     *
     * @var string
     */
    public string $status = self::STATUS_UNAVAILABLE;

    /**
     * The ID in server (the one you see when pressing U)
     *
     * @var int
     */
    public int $serverId = 0;

    /**
     * The loaded character id if they are loaded into one
     *
     * @var int|null
     */
    public ?int $character = 0;

    /**
     * The ip of the server the player is in
     *
     * @var string
     */
    public string $serverIp;

    /**
     * The subdomain of the server (c3s1)
     *
     * @var string
     */
    public string $serverName = '';

	/**
	 * The metadata of the player
	 *
	 * @var array
	 */
	public array $metadata = [];

    /**
     * The fake name of the player
     *
     * @var string|null
     */
    public ?string $fakeName = null;

    public function __construct(string $status, string $serverIp, int $serverId, ?int $character = null, ?string $fakeName = null, ?array $metadata = null)
    {
        $this->status = $status;
        $this->serverIp = Server::fixApiUrl($serverIp);
        $this->serverId = $serverId;
        $this->character = $character;
        $this->fakeName = $fakeName;

		$this->metadata = $metadata ?? [];

        $this->serverName = Server::getServerName($serverIp);
    }

    public function isOnline(): bool
    {
        return $this->status === self::STATUS_ONLINE;
    }
}
