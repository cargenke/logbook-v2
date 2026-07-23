<?php

namespace App\Filament\Pages;

use App\Actions\LogbookActions\GetChasisInfoAction;
use App\Actions\LogbookActions\UpdateLogbookInfoAction;
use App\Enums\UploadProcessTypeEnum;
use App\Exports\TemplateExports\AllUploadTemplateExport;
use App\Exports\TemplateExports\LogbooksPendingRequestTemplateExport;
use App\Models\UploadProcessLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class Acceptance extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.acceptance';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowRight;

    protected static string|UnitEnum|null $navigationGroup = 'Bulk Operations';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('id')
                    ->label('#'),
                TextColumn::make('creator.name')
                    ->label('Uploaded By')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-m-arrow-path',
                        '1' => 'heroicon-m-check',

                    })
                    ->formatStateUsing(fn (string $state): mixed => match ($state) {
                        '0' => 'Processing',
                        '1' => 'Processed',
                    })
                    ->color(fn (string $state): string => match ($state) {
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

            Action::make('download')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->tooltip('Download Template')
                ->action(function () {

                    return Excel::download(
                        new AllUploadTemplateExport([[
                            'chasis_number' => '',
                            'reg_number' => '',
                            'status' => '',
                        ]]),
                       now()->format('Y-m-d') . '-'. 'Acceptance Template.xlsx'
                    );

                }),

            Action::make('Add New Request')
                ->label('Upload Accepted Requests')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([

                    TextInput::make('name')
                        ->label('Chassis Number')
                        ->required()
                        ->rules([
                            'max:255',
                        ]),

                    TextInput::make('file_name')
                        ->label('Reg Number')
                        ->rules([
                            'max:255',
                        ]),

                ])
                ->action(function (array $data) {

                    try {
                        $record = UploadProcessLog::create([
                            'name' => $data['name'],
                            'file_name' => $data['file_name'],
                            'user_id' => auth()->id(),
                            'status' => 0,
                            'createdOn' => now(),
                            'process_type' => UploadProcessTypeEnum::DIRECT_TRANSFER_UPLOAD->value,
                            'createdBy' => auth()->id(),
                        ]);

                        $logbookInfo = (new GetChasisInfoAction($record['name']))->handle();

                        if (! $logbookInfo) {
                            Notification::make()
                                ->title('No logbook information found for the provided chassis number')
                                ->danger()
                                ->send();

                            return;
                        }

                        Log::info('Logbook info retrieved: '.json_encode($logbookInfo));

                        (new UpdateLogbookInfoAction($logbookInfo))->handle();

                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();

                        $record->update([
                            'status' => 1,
                        ]);

                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();

                    } catch (\Throwable $th) {

                        Log::info('Error uploading file: '.$th);
                        Notification::make()
                            ->title('Adding New Request Failed')
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Upload Acceptaed File')
                ->modalSubmitActionLabel('Add Request')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {
        return UploadProcessLog::query()
            ->where('process_type', UploadProcessTypeEnum::PENDING_ACCEPTANCE->value);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }
}
