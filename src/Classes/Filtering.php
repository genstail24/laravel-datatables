<?php

namespace Debva\Datatables\Classes;

use Debva\Datatables\Http\Requests\DatatablesRequest;

trait Filtering
{
    abstract protected function setColumns(): array;

    public function performFiltering(DatatablesRequest $request, $queryBuilder)
    {
        $columnFilters = $request->getColumnFilters();

        foreach ($this->setColumns() as $column) {
            if ($column->isFilterable() and array_key_exists($column->getAttribute(), $columnFilters)) {

                $filterValues = $columnFilters[$column->getAttribute()];

                if (!is_array($filterValues)) {
                    $filterValues = [$filterValues];
                }

                $queryBuilder = $queryBuilder->where(function ($query) use ($filterValues, $column) {
                    foreach ($filterValues as $value) {
                        $attributeName = $column->getWhereClauseAttribute();
                        if ($column->getType() === 'date') {
                            $query->orWhereDate($attributeName, $value);
                        } else {
                            $query->orWhere($attributeName, 'LIKE', "%$value%");
                        }
                    }
                });
            }
        }

        return $queryBuilder;
    }
}