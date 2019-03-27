<?php

namespace Ollieread\Repositories\Criteria;

use Ollieread\Repositories\Criteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LogicException;

abstract class FilterCriteria extends Criteria
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * FilterCriteria constructor.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function perform(Builder $query): Builder
    {
        $filter = $this->request->get('filter');

        // Make sure they've implemented a column property
        if (!isset($this->columns)) {
            $this->missingColumnProperty(static::class);
        }

        // check if it's a string and check
        // if it's an filterable column
        if (is_string($filter)) {

            $columns = $this->columns;
            $filter  = explode(';', $filter);

            $columns = array_filter($filter, function ($filter) use ($columns) {
                return in_array($filter, $columns, true);
            });

            if (!empty($columns)) {
                $query->select($columns);
            }
        }

        return $query;
    }

    /**
     * Let the developer know to implement a column
     * property in their FilterCriteria class
     *
     * @param $class
     *
     * @return mixed
     */
    private function missingColumnProperty($class)
    {
        return (function () use ($class) {
            throw new LogicException("Implement a column property in the {$class} class");
        })();
    }
}
