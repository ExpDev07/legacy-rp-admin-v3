<?php

namespace App\JsonApi\Players;

use App\Player;
use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Adapter extends AbstractAdapter
{

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
        parent::__construct(new Player(), $paging);
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        // Filtering of a a query search.
        if ($title = strtolower($filters->get('query'))) {
            $query
                ->where('lower(identifier)', 'like', "%{$query}%")
                ->orWhere('lower(name)', 'like', "%{$query}%");
        }
    }

}
