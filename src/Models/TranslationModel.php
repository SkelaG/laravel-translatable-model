<?php

use Illuminate\Database\Eloquent\Model;

class TranslationModel extends Model
{
    public function getTranslatableFields()
    {
        return $this->fillable;
    }
}