<?php
namespace App\Filament\Pages;

use App\Actions\LogbookActions\GetChasisInfoAction;
use App\Actions\LogbookActions\UpdateLogbookInfoAction;
use App\Enums\UploadProcessTypeEnum;
use App\Models\UploadProcessLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use UnitEnum;

class UpdateRequest extends Page implements HasTable
{

    use InteractsWithTable;
    protected string $view = 'filament.pages.update-request';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Pencil;

    protected static string|UnitEnum|null $navigationGroup = 'Logbook Management';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
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
                    ->tooltip(fn($record) => new HtmlString("
        <strong>Remarks:</strong><br>
        " . ($record->remarks ?: 'No remarks available')
                    ))
                    ->icon(fn(string $state): string => match ($state) {
                        '2' => 'heroicon-m-x-mark',
                        '1' => 'heroicon-m-x-mark',
                        '0' => 'heroicon-m-check',
                    })
                    ->formatStateUsing(fn(string $state): mixed => match ($state) {
                        '2' => 'Failed',
                        '1' => 'Processing',
                        '0' => 'Processed',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '2' => 'danger',
                        '1' => 'primary',
                        '0' => 'success',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('id', 'desc')
            ->filters([

            ])
            ->actions([

            ])
            ->bulkActions([
                // DeleteBulkAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Add New Request')
                ->label('New Request')
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
                            'process_type' => UploadProcessTypeEnum::UPDATE_REQUEST->value,
                            'createdBy' => auth()->id(),
                        ]);



                        $logbookInfo = (new GetChasisInfoAction($record['name']))->handle();

                        if (!$logbookInfo) {
                            Notification::make()
                                ->title('No logbook information found for the provided chassis number')
                                ->danger()
                                ->send();



                            $record->update([
                                'status' => 2,
                                'remarks' => 'No logbook information found for the provided chassis number',
                            ]);

                            return;
                        }




                        (new UpdateLogbookInfoAction($logbookInfo))->handle();

                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();


                        $record->update([
                            'status' => 0,
                        ]);



                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();



                    } catch (\Throwable $th) {


                        $record->update([
                            'status' => 1,
                        ]);

                        Log::info("Error uploading file: " . $th);
                        Notification::make()
                            ->title('Adding New Request Failed')
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Upload Update Request File')
                ->modalSubmitActionLabel('Add Request')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {

        if (auth()->user()?->hasAnyRole(['SuperAdmin'])) {
            return UploadProcessLog::query()->where('process_type', UploadProcessTypeEnum::UPDATE_REQUEST->value);
        }

        return UploadProcessLog::query()
            ->where('user_id', auth()->user()->id)
            ->where('process_type', UploadProcessTypeEnum::UPDATE_REQUEST->value);
    }

    public static function canAccess(): bool
    {

        return true;
        return auth()->user()->hasRole('SuperAdmin');
    }

}
