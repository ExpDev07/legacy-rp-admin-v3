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
class Log extends Model
{
    use HasFactory;

    /**
     * Column name for when the model was created.
     */
    const CREATED_AT = 'timestamp';

    /**
     * Column name for when the model was last updated.
     */
    const UPDATED_AT = 'timestamp';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier',
        'action',
        'details',
        'metadata',
        'timestamp',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata'  => 'array',
        'timestamp' => 'datetime',
    ];

    /**
     * Gets the server id.
     *
     * @return mixed|null
     */
    protected function getServerIdAttribute()
    {
        return $this->metadata['serverId'] ?? null;
    }

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function player()
    {
        return $this->belongsTo(Player::class, 'identifier', 'steam_identifier');
    }

}
