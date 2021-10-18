<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientError extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'errors_client';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'error_id';

    /**
     * Whether to use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'error_id',
        'steam_identifier',
        'error_location',
        'error_trace',
        'error_feedback',
        'player_ping',
        'server_id',
        'timestamp',
    ];

}
