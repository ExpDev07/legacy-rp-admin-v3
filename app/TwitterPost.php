<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An action that has been logged.
 *
 * @package App
 */
class TwitterPost extends Model
{
    use HasFactory;

    /**
     * Column name for when the model was created.
     */
    const CREATED_AT = 'time';

    /**
     * Column name for when the model was last updated.
     */
    const UPDATED_AT = 'time';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'twitter_tweets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'authorId',
        'message',
        'time',
        'likes',
        'is_deleted',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'time'       => 'datetime',
        'likes'      => 'integer',
        'is_deleted' => 'integer',
    ];

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function twitterUser(): BelongsTo
    {
        return $this->belongsTo(TwitterUser::class, 'id', 'authorId');
    }

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'character_id', 'realUser');
    }

}
