<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Player player
 * @property string identifier
 * @property string firstname
 * @property string lastname
 * @property string name
 * @property string gender
 * @property string dob
 * @property string story
 * @property string job
 * @property int height
 * @property int cash
 * @property int bank
 * @property int money
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
        'basicneeds', 'licenses', 'model', 'tattoos', 'ammo', 'animations',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'basicneeds' => 'array',
        'licenses'   => 'array',
        'model'      => 'array',
        'tattoos'    => 'array',
        'ammo'       => 'array',
        'animations' => 'array',
    ];

    

    /**
     * Gets the full name by concatenating firstname and lastname together.
     *
     * @return string
     */
    protected function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
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
        return $this->belongsTo(Player::class, 'identifier', 'identifier');
    }

}
