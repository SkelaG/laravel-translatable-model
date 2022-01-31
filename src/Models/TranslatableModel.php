<?php

namespace SkelaG\LaravelTranslatableModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use SkelaG\LaravelTranslatableModel\Classes\Translation;

class TranslatableModel extends Model
{
    protected $translationsModelName = null;
    protected $casts = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('translation', function ($builder) {
            return $builder->whereHas('translation')->with('translation');
        });

        static::updated(function (TranslatableModel $model) {
            $model->translation->save();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        foreach ($this->getTranslatable() as $field) {
            $this->casts[$field] = Translation::class;
        }
    }

    public function scopeSlug($query, $slug)
    {
        return $query->published()->whereHas('translations', function ($q) use ($slug) {
            $q->whereSlug($slug);
        });
    }

    public function getFillable()
    {
        return array_diff($this->fillable, $this->getTranslatable());
    }

    public function getTranslatable()
    {
        $translationModelName = $this->getTranslationsModelName();
        $translationModel     = (new $translationModelName());

        return $translationModel->getTranslatableFields();
    }

    public function saveTranslation($locale, $attributes)
    {
        $translatableAttributes = $this->getOnlyTranslatableFields($attributes);
        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            $translatableAttributes
        );
    }

    private function getOnlyTranslatableFields($attributes)
    {
        $translatableFields = array_intersect_key($attributes, array_flip($this->getTranslatable()));

        return $translatableFields;
    }

    private function getTranslationsModelName()
    {
        if ($this->translationsModelName) {
            return $this->translationsModelName;
        }
        $this->translationsModelName = get_called_class() . 'Translation';

        return $this->translationsModelName;
    }

    public function translation()
    {
        return $this->hasOne($this->getTranslationsModelName())->where('locale', App::getLocale());
    }

    public function translations()
    {
        return $this->hasMany($this->getTranslationsModelName());
    }

    public function locales()
    {
        $translations = $this->translations()->get();
        $locales      = [];

        foreach ($translations as $translation) {
            $locales[] = $translation->locale;
        }

        return $locales;
    }

    public function locale($locale)
    {
        return $this->withoutRelation('translation')->hasOne($this->getTranslationsModelName())->whereLocale($locale);
    }

    public function ru()
    {
        return $this->locale('ru');
    }

    public function en()
    {
        return $this->locale('en');
    }

    public function save(array $options = [])
    {
        foreach ($this->getTranslatable() as $field) {
            unset($this->{$field});
        }

        parent::save($options);
    }
}
