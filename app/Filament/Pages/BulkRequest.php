<?php
namespace App\Filament\Pages;

use App\Models\UploadProcessLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use UnitEnum;

class BulkRequest extends Page implements HasTable
{

    use InteractsWithTable;
    protected string $view = 'filament.pages.bulk-request';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Logbook Management';

    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('creator.name')
                    ->label('Uploaded By')
                    ->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('createdOn')->dateTime(),

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

                    FileUpload::make('file')
                        ->required()
                        ->disk('s3')
                        ->rules([
                            'mimes:xls,xlsx',
                        ])
                        ->directory('bulk-uploads'),
                ])
                ->action(function (array $data) {

                

                    $filePath = $data['file'];

                    Log::info("File uploaded to: " . $filePath);

                    try {
                     $data = UploadProcessLog::create([
                            'name'      => "Request Upload",
                            'file_name' => $filePath,
                            'user_id'   => auth()->id(),
                            'status'    => 1, // Processing
                            'createdOn' => now(),
                            'createdBy' => auth()->id(),
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
            return UploadProcessLog::query();
        }

        return UploadProcessLog::query()
            ->where('user_id', auth()->user()->id);
    }

}
