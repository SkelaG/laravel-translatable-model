<?php

namespace SkelaG\LaravelTranslatableModel\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationModel extends Model
{
    protected array $translatable = [];

    public function getTranslatableFields(): array
    {
        return $this->translatable;
    }

    /**
     * @return array|string[]
     */
    public function getFillable()
    {
        return array_merge($this->translatable, ['locale']);
    }
}
