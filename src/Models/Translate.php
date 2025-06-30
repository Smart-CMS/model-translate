<?php

namespace SmartCms\ModelTranslate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use SmartCms\Lang\Models\Language;

/**
 * Class Translate
 *
 * @property int $id The unique identifier for the model.
 * @property string $value The translated value.
 * @property int $language_id The language identifier.
 * @property string $translatable_type The type of model this translation belongs to.
 * @property int $translatable_id The ID of the model this translation belongs to.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \SmartCms\Lang\Models\Language $language The language of this translation.
 * @property-read mixed $entity The model this translation belongs to.
 */
class Translate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
