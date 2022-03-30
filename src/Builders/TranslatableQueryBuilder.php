<?php

namespace SkelaG\LaravelTranslatableModel\Builders;

use Illuminate\Database\Query\Builder as QueryBuilder;

class TranslatableQueryBuilder extends \Illuminate\Database\Eloquent\Builder
{
    private array $translatable;

    public function __construct(QueryBuilder $query, $translatable)
    {
        $this->translatable = $translatable;
        parent::__construct($query);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and'): TranslatableQueryBuilder|\Illuminate\Database\Eloquent\Builder|\m
    {
        if (in_array($column, $this->translatable)) {
            return $this->whereHas('translation', function ($query) use ($column, $operator, $value, $boolean) {
                $query->where($column, $operator, $value, $boolean);
            });
        } else {
            return parent::where($column, $operator, $value, $boolean);
        }
    }
}
