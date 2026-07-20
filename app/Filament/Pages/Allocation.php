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

class Allocation extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.allocation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowRight;

    protected static string|UnitEnum|null $navigationGroup = 'Bulk Operations';

    protected static ?int $navigationSort = 8;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('creator.name')
                    ->label('Requested By')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Chassis Number'),
                TextColumn::make('file_name')
                    ->label('Reg Number'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-m-x-mark',
                        '1' => 'heroicon-m-check',

                    })
                    ->formatStateUsing(fn(string $state): mixed => match ($state) {
                        '0' => 'Processing',
                        '1' => 'Processed',
                    })
                    ->color(fn(string $state): string => match ($state) {
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
                        new AllUploadTemplateExport([
                            [
                                'chasis_number' => '',
                                'reg_number' => '',
                            ]
                        ]),
                        now()->format('Y-m-d') . '-Allocation Template.xlsx'
                    );

                }),

            Action::make('Add New Request')
                ->label('Upload Allocation')
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
                            'process_type' => UploadProcessTypeEnum::ALLOCATION->value,
                            'createdBy' => auth()->id(),
                        ]);

                        $logbookInfo = (new GetChasisInfoAction($record['name']))->handle();

                        if (!$logbookInfo) {
                            Notification::make()
                                ->title('No logbook information found for the provided chassis number')
                                ->danger()
                                ->send();

                            return;
                        }

                        Log::info('Logbook info retrieved: ' . json_encode($logbookInfo));

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

                        Log::info('Error uploading file: ' . $th);
                        Notification::make()
                            ->title('Adding New Request Failed')
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Upload Allocation File')
                ->modalSubmitActionLabel('Add Request')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {


        return UploadProcessLog::query()

            ->where('process_type', UploadProcessTypeEnum::ALLOCATION->value);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }
}
