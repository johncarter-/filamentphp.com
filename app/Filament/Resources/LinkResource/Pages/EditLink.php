<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Enums\LinkStatus;
use App\Filament\Resources\LinkResource;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;

class EditLink extends EditRecord
{
    protected static string $resource = LinkResource::class;

    protected bool $shouldSaveAsDraft = false;

    protected bool $shouldSaveAsPending = false;

    protected function afterSave(): void
    {
        $this->emit('linkUpdated', ['link' => $this->record]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->is_admin) {
            return $data;
        }

        if ($this->shouldSaveAsDraft) {
            $data['status'] = LinkStatus::DRAFT;
        }

        if ($this->shouldSaveAsPending) {
            $data['status'] = LinkStatus::PENDING;
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
            ->visible(fn (): bool => (! auth()->user()->is_admin) && $this->record->status === LinkStatus::PENDING);
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
            ->visible(fn (): bool => (! auth()->user()->is_admin) && $this->record->status === LinkStatus::DRAFT);
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

    protected function getHeaderWidgets(): array
    {
        return [
            LinkResource\Widgets\EditLinkHeader::class,
        ];
    }
}
