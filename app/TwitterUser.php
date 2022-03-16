<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @package App
 */
class TwitterUser extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'twitter_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'username',
        'password',
        'avatar_url',
        'creator_cid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Returns a map of twitter_userid->twitter_user
     * This is used instead of a left join as it appears to be a lot faster
     *
     * @param array $source
     * @param string $sourceKey
     * @return array
     */
    public static function fetchIdMap(array $source, string $sourceKey): array
    {
        $ids = [];
        foreach ($source as $entry) {
            if (!in_array($entry[$sourceKey], $ids)) {
                $ids[] = $entry[$sourceKey];
            }
        }

        $users = self::query()->whereIn('id', $ids)->select([
            'id', 'username', 'avatar_url',
        ])->get();
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user->id] = [
                'username'  => $user->username,
                'avatar_url' => $user->avatar_url,
            ];
        }

        if (empty($userMap)) {
            $userMap['empty'] = 'empty';
        }

        return $userMap;
    }

}
