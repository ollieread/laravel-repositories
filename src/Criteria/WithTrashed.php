<?php

namespace Ollieread\Repositories\Criteria;

use Ollieread\Repositories\Criteria;

/**
 * With Trashed
 *
 * Include all soft deleted entries in the query.
 *
 * @package Ollieread\Repositories\Criteria
 */
class WithTrashed extends Criteria
{

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function perform($query)
    {
        $query->withTrashed();
    }
}