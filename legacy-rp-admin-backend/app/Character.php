<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A character that belongs to a player.
 *
 * @package App
 */
class Character extends Model
{

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
    protected $primaryKey = 'cid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 'slot', 'firstname', 'lastname', 'gender', 'height', 'dob', 'story', 'cash', 'bank', 'job',
        'basicneeds', 'licenses', 'model', 'tattoos', 'ammo', 'animations'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'basicneeds' => 'array',
        'licenses' => 'array',
        'model' => 'array',
        'tattoos' => 'array',
        'ammo' => 'array',
        'animations' => 'array',
    ];

}
