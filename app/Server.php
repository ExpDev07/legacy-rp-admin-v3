<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @package App
 */
class Server extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webpanel_servers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
    ];

    /**
     * Gets the API data.
     *
     * @return array|mixed
     */
    public function fetchApi(): array
    {
        // example: https://c3s1.op-framework.com/op-framework/api.json
        return Http::get(Str::finish($this->url, '/') . 'api.json')->json() ?? [];
    }

    /**
     * Gets the connections data.
     *
     * @return array
     */
    public function fetchConnections(): array
    {
        // example: https://c3s1.op-framework.com/op-framework/connections.json
        return Http::get(Str::finish($this->url, '/') . 'connections.json')->json() ?? [];
    }

}
