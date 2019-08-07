<?php

namespace App\JsonApi\Users;

use App\User;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\UnauthorizedException;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;

class Adapter extends AbstractAdapter
{

    // https://laravel-json-api.readthedocs.io/en/latest/basics/adapters/

    /**
     * The default pagination to use.
     *
     * @var array
     */
    protected $defaultPagination = [ 'number' => 1 ];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new User(), $paging);
    }

    /**
     * @param EncodingParametersInterface $parameters
     * @return mixed
     */
    public function query(EncodingParametersInterface $parameters)
    {
        if (request()->get('me')){
            // User requested to see themselves. Will be based of auth token.
            return request()->user();
        }
        return parent::query($parameters);
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        // TODO
    }

    protected function player()
    {
        return $this->hasOne();
    }


}
