<?php

namespace SmartCms\ModelTranslate\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use SmartCms\ModelTranslate\Models\Translate;

/**
 * Trait HasTranslate
 */
trait HasTranslate
{
    /**
     * Get the translatable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function translatable()
    {
        return $this->morphOne(Translate::class, 'entity');
    }

    /**
     * Get the translatable name attribute.
     *
     * @return string
     */
    public function translate(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return Cache::memo()->get($this->get_translate_key(), function (): string {
                    $value = $this->attributes['name'] ?? '';
                    $translation = $this->translatable()->where('language_id', current_lang_id())->first();
                    if ($translation && ! blank($translation->value)) {
                        $value = $translation->value;
                    }

                    return $value;
                });
            }
        );
    }

    /**
     * Get the translatable key.
     *
     * @return string
     */
    public function get_translate_key()
    {
        return $this->getTable() . '_' . $this->id;
    }
}
