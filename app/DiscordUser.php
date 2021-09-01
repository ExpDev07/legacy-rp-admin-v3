<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class DiscordUser
{
    const CacheKey = 'discord_users';

    public string  $id            = '';
    public string  $username      = '';
    public string  $discriminator = '';
    private string $avatar        = '';

    /**
     * Returns the avatar link
     *
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar ? 'https://cdn.discordapp.com/avatars/' . $this->id . '/' . $this->avatar . '.png' : '';
    }

    /**
     * Loads a user
     *
     * @param string $discordId
     * @return DiscordUser|null
     */
    public static function getUser(string $discordId): ?DiscordUser
    {
        $key = CLUSTER . self::CacheKey . '_' . md5($discordId);

        if (Cache::store('file')->has($key)) {
            $cache = Cache::store('file')->get($key) ?? null;

            if (self::fromArray($cache)) {
                return self::fromArray($cache);
            }
        }

        $token = env('DISCORD_BOT_TOKEN', '');
        if (!$token) {
            return null;
        }

        $url = 'https://discord.com/api/v9/users/' . $discordId;

        try {
            $client = new Client();
            $res = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bot ' . $token,
                ],
            ]);

            $response = $res->getBody()->getContents();

            $data = json_decode($response, true);
            if ($data && is_array($data)) {
                $user = self::fromArray($data);

                if ($user) {
                    Cache::store('file')->put($key, $user->toArray(true), 24 * 60 * 60);
                }

                return $user;
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Creates a user from an array
     *
     * @param array $data
     * @return DiscordUser|null
     */
    public static function fromArray(array $data): ?DiscordUser
    {
        $required = [
            'id',
            'username',
            'avatar',
            'discriminator',
        ];

        foreach ($required as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return null;
            }
        }

        $user = new DiscordUser();
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->discriminator = $data['discriminator'];
        $user->avatar = $data['avatar'];

        return $user;
    }

    /**
     * Returns the user as an array
     *
     * @param bool $storeArray
     * @return array
     */
    public function toArray(bool $storeArray = false): array
    {
        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'discriminator' => $this->discriminator,
            'avatar'        => $storeArray ? $this->avatar : $this->getAvatar(),
        ];
    }
}
