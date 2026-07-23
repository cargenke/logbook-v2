<?php

namespace App\Filament\Resources\LogbookRequests\Pages;

use App\Filament\Resources\LogbookRequests\LogbookRequestResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLogbookRequest extends ViewRecord
{
    protected static string $resource = LogbookRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->hidden(fn () => $this->record->isClosed),

           Action::make('openForEditing')
                ->label('Open for Editing')
                ->icon('heroicon-o-lock-open')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () =>
                    auth()->user()->hasRole('SuperAdmin') &&
                    $this->record->isClosed
                )
                ->action(function () {
                    $this->record->update([
                        'isClosed' => false,
                    ]);

                    $this->refreshFormData([
                        'isClosed',
                    ]);

                    Notification::make()
                        ->title('Record opened for editing.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
