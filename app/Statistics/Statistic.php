<?php

namespace App\Statistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App
 */
class Statistic extends Model
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
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_updated',
        'day',
        'opening',
        'closing',
        'high',
        'low',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'opening' => 'integer',
        'closing' => 'integer',
        'high'    => 'integer',
        'low'     => 'integer',
    ];
}
