<?php

namespace SkelaG\LaravelTranslatableModel\Builders;

use Arr;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TranslatableQueryBuilder extends \Illuminate\Database\Eloquent\Builder
{
    private array $translatable;

    public function __construct(QueryBuilder $query, $translatable)
    {
        $this->translatable = $translatable;
        parent::__construct($query);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and'): TranslatableQueryBuilder|\Illuminate\Database\Eloquent\Builder
    {
        if (in_array($column, $this->translatable)) {
            return $this->whereHas('translation', function ($query) use ($column, $operator, $value, $boolean) {
                $query->where($column, $operator, $value, $boolean);
            });
        } elseif (is_array($column) && Arr::isAssoc($column)) {
            foreach ($column as  $key => $value) {
                if (in_array($key, $this->translatable)) {
                    $this->whereHas('translation', function ($query) use ($key, $value) {
                        $query->where($key, $value);
                    });
                } else {
                    $this->where($key, $value);
                }
            }
            return $this;
        } else {
            return parent::where($column, $operator, $value, $boolean);
        }
    }
}
