<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use kanalumaddela\LaravelSteamLogin\SteamUser;
use SteamID;

/**
 * @package App
 */
class Property extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    private static $companyNameMap = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stocks_company_properties';

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
    protected $primaryKey = 'property_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_name',
        'property_address',
        'property_cost',
        'property_renter',
        'property_renter_cid',
        'property_income',
        'shared_keys',
        'company_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'property_cost'       => 'integer',
        'property_income'     => 'integer',
        'property_renter_cid' => 'integer',
    ];

    public function companyName(): string
    {
        $id = $this->company_id;

        if (!isset(self::$companyNameMap[$id])) {
            $company = DB::table('stocks_companies')->where('company_id', '=', $id)->select(['company_name'])->first();

            self::$companyNameMap[$id] = $company ? $company->company_name : 'Unknown';
        }

        return self::$companyNameMap[$id];
    }
}
