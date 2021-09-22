<?php

namespace App\Statistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @package App
 */
class BanStatistic extends Statistic
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webpanel_ban_statistics';
}
