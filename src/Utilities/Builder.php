<?php

namespace Debva\Utilities;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait Builder
{
    protected $whereClauseAttribute;

    protected $with;
    
    /**
     * @param string $whereClauseAttribute
     * 
     * @return $this
     */
    public function whereClauseAttribute(string $whereClauseAttribute)
    {
        $this->whereClauseAttribute = $whereClauseAttribute;
        return $this;
    }

    /**
     * @param string $with
     * 
     * @return $this
     */
    public function with(string $with)
    {
        $this->with = $with;
        return $this;
    }

    /**
     * @return string
     */
    public function getWhereClauseAttribute(): string
    {
        return $this->whereClauseAttribute ?? $this->attribute;
    }

    /**
     * @return string|null
     */
    public function getWith()
    {
        return $this->with;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $queryBuilder
     * 
     * @return string|null
     */
    public function getData($queryBuilder)
    {
        if ($this->getWith()) {
            if ($queryBuilder->{$this->getWith()} instanceof \Collection) {
                $data = [];
                foreach ($queryBuilder->{$this->getWith()} as $relation) {
                    $data[] = $relation->{$this->attribute};
                }
            } else {
                $data = data_get($queryBuilder->{$this->getWith()}, $this->attribute);
            }
        } else {
            $data = data_get($queryBuilder, $this->attribute);
        }

        if ($this->getType('date')) {
            $data = strftime($this->dateOutputFormat, strtotime($data));
        }

        return $data;
    }
}