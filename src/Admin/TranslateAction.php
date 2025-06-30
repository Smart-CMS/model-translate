<?php

namespace SmartCms\ModelTranslate\Admin;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Form;
use Filament\Support\Enums\Width;
use SmartCms\ModelTranslate\Models\Translate;

class TranslateAction
{
    public static function make(string $name = 'translate'): Action
    {
        return Action::make($name)
            ->hidden(function () {
                return app('lang')->adminLanguages()->count() <= 1;
            })
            ->modalWidth(Width::TwoExtraLarge)
            ->badge(function ($record) {
                return $record->translatable()->count();
            })
            ->color(function ($record) {
                if ($record->translatable()->count() > 0) {
                    return 'info';
                }

                return 'danger';
            })
            ->icon(function (): string {
                return 'heroicon-o-language';
            })->schema(function (Form $form, Action $action) {
                if ($action->isHidden()) {
                    return [];
                }
                $fields = [];
                $languages = get_active_languages();
                foreach ($languages as $language) {
                    $fields[] = TextInput::make($language->slug . '.name')->label(__('core::admin.name') . ' (' . $language->name . ')');
                }

                return $form->schema($fields);
            })->fillForm(function ($record) {
                $translates = [];
                $languages = get_active_languages();
                foreach ($languages as $language) {
                    $translates[$language->slug] = [
                        'name' => $record->translatable()->where('language_id', $language->id)->first()->value ?? '',
                    ];
                }

                return $translates;
            })->action(function ($record, $data) {
                foreach (get_active_languages() as $lang) {
                    $name = $data[$lang->slug]['name'] ?? '';
                    if ($name == '') {
                        Translate::query()->where([
                            'language_id' => $lang->id,
                            'entity_id' => $record->id,
                            'entity_type' => get_class($record),
                        ])->delete();

                        continue;
                    }
                    Translate::query()->updateOrCreate([
                        'language_id' => $lang->id,
                        'entity_id' => $record->id,
                        'entity_type' => get_class($record),
                    ], ['value' => $name]);
                }
                Notification::make()->success()->title(__('core::admin.saved'))->send();
            });
    }
}
