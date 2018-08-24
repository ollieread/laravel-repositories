<?php

namespace Ollieread\Repositories\Criteria;

use Ollieread\Repositories\Criteria;

/**
 * Ordered By Modification
 *
 * Return the result ordered by the updated_at column.
 *
 * @package Ollieread\Repositories\Criteria
 */
class OrderedByModification extends Criteria
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
        $query->orderBy('updated_at', $this->descending ? 'desc' : 'asc');
    }
}