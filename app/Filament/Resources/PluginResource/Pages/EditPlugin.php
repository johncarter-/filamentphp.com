<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Enums\PluginStatus;
use App\Filament\Resources\PluginResource;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\EditRecord;

class EditPlugin extends EditRecord
{
    protected static string $resource = PluginResource::class;

    protected bool $shouldSaveAsDraft = false;

    protected bool $shouldSaveAsPending = false;

    protected function afterSave(): void
    {
        $this->emit('pluginUpdated', ['link' => $this->record]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->is_admin) {
            return $data;
        }

        if ($this->shouldSaveAsDraft) {
            $data['status'] = PluginStatus::DRAFT;
        }

        if ($this->shouldSaveAsPending) {
            $data['status'] = PluginStatus::PENDING;
        }

        return $data;
    }

    protected function getSavedNotificationMessage(): ?string
    {
        if ($this->shouldSaveAsDraft) {
            return 'Saved as draft';
        }

        if ($this->shouldSaveAsPending) {
            return 'Submitted for review. You\'re awesome!';
        }

        return parent::getSavedNotificationMessage();
    }

    protected function getSaveAsDraftFormAction(): Action
    {
        return ButtonAction::make('saveAsDraft')
            ->label('Save as draft')
            ->action(function () {
                $this->shouldSaveAsDraft = true;

                $this->save();
            })
            ->color('secondary')
            ->visible(fn (): bool => (! auth()->user()->is_admin) && $this->record->status === PluginStatus::PENDING);
    }

    protected function getSaveAsPendingFormAction(): Action
    {
        return ButtonAction::make('saveAsPending')
            ->label('Submit for review')
            ->action(function () {
                $this->shouldSaveAsPending = true;

                $this->save();
            })
            ->color('success')
            ->visible(fn (): bool => (! auth()->user()->is_admin) && $this->record->status === PluginStatus::DRAFT);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getSaveAsDraftFormAction(),
            $this->getSaveAsPendingFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getActions(): array
    {
        return array_merge([
            $this->getViewAction(),
        ], parent::getActions());
    }

    protected function getViewAction(): Action
    {
        return parent::getViewAction()
            ->label('Preview on our website')
            ->openUrlInNewTab()
            ->url(route('plugins.view', ['plugin' => $this->record]));
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PluginResource\Widgets\EditPluginHeader::class,
        ];
    }
}
