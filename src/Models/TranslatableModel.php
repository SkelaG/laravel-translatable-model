<?php

namespace SkelaG\LaravelTranslatableModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use SkelaG\LaravelTranslatableModel\Builders\TranslatableQueryBuilder;
use SkelaG\LaravelTranslatableModel\Classes\Translation;

class TranslatableModel extends Model
{
    protected ?string $translationsModelName = null;
    protected $casts = [];
    protected array $translation_attributes = [];
    protected ?string $locale = null;

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeOnlyWithTranslations(TranslatableQueryBuilder $builder)
    {
        return $builder->whereHas('translation');
    }

    public function __construct(array $attributes = [])
    {
        foreach ($this->getTranslatable() as $field) {
            if (isset($attributes[$field])) {
                $this->translation_attributes[$field] = $attributes[$field];
            }
            $this->casts[$field] = Translation::class;
        }
        parent::__construct($attributes);
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

    public function addTranslationField($column, $value)
    {
        $this->translation_attributes[$column] = $value;
    }

    public function getTranslatable()
    {
        $translationModelName = $this->getTranslationsModelName();
        $translationModel = (new $translationModelName());

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
        $this->translationsModelName = get_called_class().'Translation';

        return $this->translationsModelName;
    }

    public function translation()
    {
        return $this->hasOne($this->getTranslationsModelName())->where('locale', $this->getLocale());
    }

    public function translations()
    {
        return $this->hasMany($this->getTranslationsModelName());
    }

    public function locales()
    {
        $translations = $this->translations()->get();
        $locales = [];

        foreach ($translations as $translation) {
            $locales[] = $translation->locale;
        }

        return $locales;
    }

    public function locale($locale)
    {
        return $this->hasOne($this->getTranslationsModelName())->whereLocale($locale);
    }

    public function fill(array $attributes)
    {
        foreach ($this->getTranslatable() as $field) {
            if (isset($attributes[$field])) {
                $this->translation_attributes[$field] = $attributes[$field];
                unset($attributes[$field]);
            }
        }
        return parent::fill($attributes);
    }
    
    public function hasTranslation(?string $locale = null): bool
    {
        return (bool) $this->locale($locale);
    }

    public function save(array $options = [])
    {
        $this->translation_attributes['locale'] = $this->getLocale();

        $this->syncAttributes();
        parent::save($options);

        if ($this->translation_attributes) {
            if (!$this->translation) {
                $this->translation()->create($this->translation_attributes);
            } else {
                $this->translation()->update($this->translation_attributes);
            }
        }

        $this->refresh();
    }

    public function update(array $attributes = [], array $options = [])
    {
        if (!$this->translation) {
            $attributes['locale'] = App::getLocale();
            $this->translation()->create($attributes);
            $this->refresh();
        }
        $this->translation->update($attributes, $options);
        return parent::update($attributes, $options);
    }

    private function syncAttributes()
    {
        foreach ($this->translation_attributes as $key => $value) {
            unset($this->attributes[$key]);
            unset($this->original[$key]);
        }
    }

    public function newEloquentBuilder($query): TranslatableQueryBuilder
    {
        return new TranslatableQueryBuilder($query, $this->getTranslatable());
    }

    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        $this->load('translation');
        return $this;
    }
    
    public function getTranslationsArray(): array
    {
        $ans = [];
        foreach ($this->translations()->get() as $translation) {
            $ans[$translation->locale] = $translation->toArray();
        }
        
        return $ans;
    }

    protected function getLocale(): string
    {
        if ($this->locale) {
            return $this->locale;
        }
        return App::getLocale();
    }
}
