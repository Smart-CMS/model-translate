<?php

namespace SmartCms\ModelTranslate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SmartCms\Lang\Models\Language;
use SmartCms\ModelTranslate\Models\Translate;

class TranslateFactory extends Factory
{
    protected $model = Translate::class;

    public function definition()
    {
        return [
            'value' => $this->faker->sentence,
            'language_id' => Language::factory(),
        ];
    }
}
