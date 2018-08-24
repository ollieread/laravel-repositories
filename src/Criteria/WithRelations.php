<?php

namespace Ollieread\Repositories\Criteria;

use Ollieread\Repositories\Criteria;

/**
 * With Relations
 *
 * Load all provided relations with the query.
 *
 * @package Ollieread\Repositories\Criteria
 */
class WithRelations extends Criteria
{
    /**
     * @var array
     */
    private $relations;

    public function __construct(...$relations)
    {
        $this->relations = \count($relations) === 1 && \is_array($relations[0]) ? $relations[0] : $relations;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function perform($query)
    {
        $query->with($this->relations);
    }
}