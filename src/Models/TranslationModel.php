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
}
