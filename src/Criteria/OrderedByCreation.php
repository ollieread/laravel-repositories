<?php

namespace Ollieread\Repositories\Criteria;

use Ollieread\Repositories\Criteria;

/**
 * Ordered By Creation
 *
 * Return the result ordered by the created_at column.
 *
 * @package Ollieread\Repositories\Criteria
 */
class OrderedByCreation extends Criteria
{
    /**
     * @var bool
     */
    private $descending;

    public function __construct(bool $descending = true)
    {
        $this->descending = $descending;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function perform($query)
    {
        $query->orderBy('created_at', $this->descending ? 'desc' : 'asc');
    }
}