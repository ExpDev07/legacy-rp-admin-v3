<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An action that has been logged.
 *
 * @package App
 *
 * @property string identifier
 * @property string action
 * @property string details
 * @property array metadata
 */
class Log extends Model
{

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
        'identifier', 'action', 'details', 'metadata'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'metadata'  => 'array',
        'timestamp' => 'date',
    ];

    /**
     * Gets the player relationship.
     *
     * @return BelongsTo
     */
    public function player()
    {
        return $this->belongsTo(Player::class, 'identifier', 'identifier');
    }

}
