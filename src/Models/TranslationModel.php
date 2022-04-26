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
     * @return array
     */
    public function getFillable(): array
    {
        return array_merge($this->translatable, ['locale']);
    }
}
