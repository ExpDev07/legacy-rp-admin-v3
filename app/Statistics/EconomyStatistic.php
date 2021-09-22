<?php

namespace App\Statistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @package App
 */
class EconomyStatistic extends Statistic
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webpanel_economy_statistics';
}
