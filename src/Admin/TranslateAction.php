<?php

namespace SmartCms\ModelTranslate\Admin;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use SmartCms\ModelTranslate\Models\Translate;

class TranslateAction
{
    public static function make(string $name = 'translate'): Action
    {
        return Action::make($name)
            ->hidden(function (string $operation) {
                return app('lang')->adminLanguages()->count() <= 1 || $operation == 'create';
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
            })->schema(function (Schema $form, Action $action) {
                if ($action->isHidden()) {
                    return [];
                }

                return app('lang')->adminLanguages()->map(function ($lang) {
                    return TextInput::make($lang->slug . '.name')->label(__('core::admin.name') . ' (' . $lang->name . ')');
                })->toArray();

                return $form->schema($fields);
            })->fillForm(function ($record) {
                $translates = [];
                $languages = app('lang')->adminLanguages();
                foreach ($languages as $language) {
                    $translates[$language->slug] = [
                        'name' => $record->translatable()->where('language_id', $language->id)->first()->value ?? '',
                    ];
                }

                return $translates;
            })->action(function ($record, $data) {
                foreach (app('lang')->adminLanguages() as $lang) {
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
