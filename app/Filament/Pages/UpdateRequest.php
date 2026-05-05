<?php
namespace App\Filament\Pages;

use App\Models\UploadProcessLog;
use App\Models\User;
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
use Illuminate\Support\Facades\Storage;
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
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('creator.name')
                    ->label('Uploaded By')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Chassis Number'),
                TextColumn::make('file_name')
                    ->label('File Name')
                    ->dateTime(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'heroicon-m-check',
                        '1' => 'heroicon-m-x-mark',

                    })
                    ->formatStateUsing(fn(string $state): mixed => match ($state) {
                        '0' => 'Processed',
                        '1' => 'Processing ',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'success',
                        '1' => 'primary',
                    }),

            ])
            ->defaultSort('id', 'desc')
            ->filters([
                // optional filters
            ])
            ->actions([

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn($record) => $record->file_name !== null)
                    ->url(function ($record) {
                        return Storage::disk('s3')->temporaryUrl(
                            $record->file_name,
                            now()->addMinutes(10)
                        );
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload File')
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
                        ->required()
                        ->rules([
                            'max:255',
                        ]),

                ])
                ->action(function (array $data) {

                    try {
                        $data = UploadProcessLog::create([
                            'name'         => $data['name'],
                            'file_name'    => $data['file_name'],
                            'user_id'      => auth()->id(),
                            'status'       => 0,
                            'createdOn'    => now(),
                            'process_type' => 1,
                            'createdBy'    => auth()->id(),
                        ]);

                        Log::info("UploadProcessLog created with ID: " . $data->id);

                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();

                    } catch (\Throwable $th) {
                        Log::info("Error uploading file: " . $th->getMessage());
                        Notification::make()
                            ->title('Failed to start upload process')
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Upload Bulk File')
                ->modalSubmitActionLabel('Upload')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {

        if (auth()->user()?->hasAnyRole(['SuperAdmin'])) {
            return UploadProcessLog::query()->where('process_type', 1);
        }

        return UploadProcessLog::query()
            ->where('user_id', auth()->user()->id)
            ->where('process_type', 1);
    }

}
