<?php

namespace Ollieread\Repositories;

abstract class Criteria
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    abstract public function perform($query);
}