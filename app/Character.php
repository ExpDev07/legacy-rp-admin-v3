<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A character made by a player.
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
    protected $primaryKey = 'character_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'steam_identifier',
        'character_slot',
        'gender',
        'first_name',
        'last_name',
        'date_of_birth',
        'cash',
        'bank',
        'job_name',
        'backstory',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth'  => 'date',
        'character_slot' => 'integer',
        'cash'           => 'integer',
        'bank'           => 'integer'
    ];

    /**
     * Gets the full name by concatenating first name and last name together.
     *
     * @return string
     */
    protected function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Gets the total amount of money by adding cash and bank together.
     *
     * @return int
     */
    protected function getMoneyAttribute()
    {
        return $this->cash + $this->bank;
    }

    /**
     * Gets player relationship.
     *
     * @return BelongsTo
     */
    public function player() : BelongsTo
    {
        return $this->belongsTo(Player::class, 'steam_identifier', 'steam_identifier');
    }

}
