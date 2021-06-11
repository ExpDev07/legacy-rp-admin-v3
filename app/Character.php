<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A character made by a player.
 *
 * @package App
 */
class Character extends Model
{
    use HasFactory;

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
        'blood_type',
        'backstory',

        'cash',
        'bank',
        'stocks_balance',

        'job_name',
        'department_name',
        'position_name',

        'character_created',
        'character_creation_timestamp',
        'character_deleted',
        'character_deletion_timestamp',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth'                => 'date',
        'character_slot'               => 'integer',
        'gender'                       => 'integer',
        'cash'                         => 'integer',
        'bank'                         => 'integer',
        'stocks_balance'               => 'double',
        'character_created'            => 'boolean',
        'character_creation_timestamp' => 'datetime',
        'character_deleted'            => 'boolean',
        'character_deletion_timestamp' => 'datetime',
    ];

    /**
     * Gets the full name by concatenating first name and last name together.
     *
     * @return string
     */
    protected function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Gets the total amount of money by adding cash and bank together.
     *
     * @return int
     */
    protected function getMoneyAttribute(): int
    {
        return $this->cash + $this->bank;
    }

    /**
     * Gets player relationship.
     *
     * @return BelongsTo
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'steam_identifier', 'steam_identifier');
    }

    /**
     * Gets the vehicles owned by this character.
     *
     * @return HasMany
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'owner_cid');
    }

    /**
     * Gets the vehicles owned by this character.
     *
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'property_renter_cid');
    }

}
