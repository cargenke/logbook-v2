<?php

namespace App\Filament\Pages;

use App\Actions\LogbookActions\SyncChasisSalesDataAction;
use App\Actions\LogbookActions\UpdateLogbookInfoAction;
use App\Enums\UploadProcessTypeEnum;
use App\Models\UploadProcessLog;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use UnitEnum;

class SyncSalesData extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.sync-sales-data';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Link;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('creator.name')
                    ->label('Requested By')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Description'),
                TextColumn::make('file_name')
                    ->label('Date'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        '-1' => 'heroicon-m-x-mark',
                        '0' => 'heroicon-m-x-mark',
                        '1' => 'heroicon-m-check',

                    })
                    ->formatStateUsing(fn (string $state): mixed => match ($state) {
                        '-1' => 'Queued',
                        '0' => 'Processing',
                        '1' => 'Processed',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '-1' => 'primary',
                        '0' => 'danger',
                        '1' => 'success',
                    }),

            ])
            ->defaultSort('id', 'desc')
            ->filters([

            ])
            ->actions([

            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('Add New Request')
                ->label('New Sync')
                ->icon('heroicon-m-plus-circle')
                ->form([

                    DatePicker::make('file_name')
                        ->label('Date')
                        ->required(),

                ])
                ->action(function (array $data) {

                    $record = UploadProcessLog::create([
                        'name' => 'Sync Sales Data For: '.$data['file_name'],
                        'file_name' => Carbon::parse($data['file_name'])->format('Ymd'),
                        'user_id' => auth()->id(),
                        'status' => -1,
                        'createdOn' => now(),
                        'process_type' => UploadProcessTypeEnum::SYNC_SALES->value,
                        'createdBy' => auth()->id(),
                    ]);

                    try {

                        $logbooks = (new SyncChasisSalesDataAction($record->file_name))->handle();

                        Notification::make()
                            ->title('Total Fetched : '.count($logbooks))
                            ->success()
                            ->send();

                        foreach ($logbooks as $key => $logbookInfo) {
                            (new UpdateLogbookInfoAction($logbookInfo))->handle();
                        }

                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();

                        $record->update([
                            'status' => 1,
                        ]);

                    } catch (\Throwable $th) {

                        $record->update([
                            'status' => 0,
                        ]);

                        Log::info('Error Creating Request: '.$th);
                        Notification::make()
                            ->title('Erro Syncing Data: '.$th->getMessage())
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Sync Sales Data')
                ->modalSubmitActionLabel('Sync Sales Data')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {

        if (auth()->user()?->hasAnyRole(['SuperAdmin'])) {
            return UploadProcessLog::query()->where('process_type', UploadProcessTypeEnum::SYNC_SALES->value);
        }

        return UploadProcessLog::query()
            ->where('user_id', auth()->user()->id)
            ->where('process_type', UploadProcessTypeEnum::SYNC_SALES->value);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }
}
