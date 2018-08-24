<?php

namespace Ollieread\Repositories;

abstract class Criteria
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function perform($query);
}