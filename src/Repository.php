<?php

namespace Ollieread\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

/**
 * Repository
 *
 * The progenitor of all repository kind.
 *
 * @package Ollieread\Repositories
 */
abstract class Repository
{
    /**
     * @var array
     */
    private $criteria = [];

    /**
     * @var bool
     */
    private $withCriteria = true;

    /**
     * The class of the model that this repository is for.
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Create a new instance of this repositories model.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function make(array $attributes = [])
    {
        $modelClass = $this->model();
        return new $modelClass($attributes);
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(): Builder
    {
        return $this->make()->newQuery();
    }

    /**
     * Don't use criteria when building the query.
     *
     * @return \Ollieread\Repositories\Repository
     */
    public function noCriteria(): self
    {
        $this->withCriteria = false;
        return $this;
    }

    /**
     * Use criteria when building the query.
     *
     * @return \Ollieread\Repositories\Repository
     */
    public function useCriteria(): self
    {
        $this->withCriteria = true;
        return $this;
    }

    /**
     * Use the provided criteria.
     *
     * @param \Ollieread\Repositories\Criteria ...$criteria
     *
     * @return \Ollieread\Repositories\Repository
     */
    public function withCriteria(Criteria ...$criteria): self
    {
        $this->criteria[] = $criteria;
        return $this;
    }

    /**
     * Reset all criteria.
     *
     * @return \Ollieread\Repositories\Repository
     */
    public function flushCriteria(): self
    {
        $this->criteria = [];
        return $this;
    }

    /**
     * Build a query from the provided arguments, and apply any criteria
     * that are current set.
     *
     * @param array $arguments
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildQuery(array $arguments = [])
    {
        $query = $this->query();

        if ($arguments) {
            collect($arguments)->each(function ($value, $key) use ($query) {
                // If the value is a closure, we pass the query object into it and continue
                if ($value instanceof \Closure) {
                    $value($query);
                    return;
                }

                // If the value is an expression, we're probably doing a raw so we pass in the
                // value of the expression
                if ($value instanceof Expression) {
                    $query->whereRaw($value->getValue());
                    return;
                }

                // If the value is an array, we're doing a WHERE in
                if (\is_array($value)) {
                    $query->whereIn($key, $value);
                    return;
                }

                // If we reach this we're just doing a plain WHERE
                $query->where($key, '=', $value);
            });
        }

        if ($this->withCriteria) {
            collect($this->criteria)->each(function (Criteria $criteria) use ($query) {
                $criteria->perform($query);
            });
        }

        return $query;
    }

    /**
     * Get all matching models.
     *
     * @param array $arguments
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get(array $arguments = [], array $columns = ['*'])
    {
        return $this->buildQuery($arguments)->get($columns);
    }

    /**
     * Return the first matching model.
     *
     * @param array $arguments
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function first(array $arguments = [], array $columns = ['*'])
    {
        return $this->buildQuery($arguments)->first($columns);
    }

    /**
     * Return a length aware paginator with matching models.
     *
     * @param array  $arguments
     * @param int    $count
     * @param string $pageName
     * @param int    $page
     * @param array  $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(array $arguments = [], int $count = 20, string $pageName = 'page', int $page = 1, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->buildQuery($arguments)->paginate($count, $columns, $pageName, $page);
    }

    /**
     * Return a simple paginator with matching models.
     *
     * @param array  $arguments
     * @param int    $count
     * @param string $pageName
     * @param int    $page
     * @param array  $columns
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate(array $arguments = [], int $count = 20, string $pageName = 'page', int $page = 1, array $columns = ['*']): Paginator
    {
        return $this->buildQuery($arguments)->simplePaginate($count, $columns, $pageName, $page);
    }
}