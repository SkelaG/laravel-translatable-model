<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TranslatableModel extends Model
{
    protected $translationsModelName = null;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('translation', function ($builder) {
            return $builder->whereHas('translation')->with('translation');
        });
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
        return $this->withoutRelations('translation')->hasOne($this->getTranslationsModelName())->whereLocale($locale);
    }

    public function ru()
    {
        return $this->locale('ru');
    }

    public function en()
    {
        return $this->locale('en');
    }
}
