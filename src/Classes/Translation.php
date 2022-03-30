<?php

namespace SkelaG\LaravelTranslatableModel\Classes;

class Translation implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $model->translation ? $model->translation->{$key} : null;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $model->addTranslationField($key, $value);
    }
}
