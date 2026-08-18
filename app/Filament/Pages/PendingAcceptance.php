<?php

namespace App\Filament\Pages;

use App\Enums\UploadProcessTypeEnum;
use App\Exports\TemplateExports\AllUploadTemplateExport;
use App\Exports\TemplateExports\LogbooksPendingRequestTemplateExport;
use App\Jobs\BulkUploads\ProcessLogbookPendingAcceptanceImportJob;
use App\Models\UploadProcessLog;
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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class PendingAcceptance extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.pending-acceptance';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowRight;

    protected static string|UnitEnum|null $navigationGroup = 'Bulk Operations';

    protected static ?int $navigationSort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('id')
                    ->label('#'),
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
                        '0' => 'heroicon-m-arrow-path',
                        '1' => 'heroicon-m-check',

                    })
                    ->formatStateUsing(fn(string $state): mixed => match ($state) {
                        '0' => 'Processing',
                        '1' => 'Processed',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'info',
                        '1' => 'success',
                    }),

            ])
            ->defaultSort('id', 'desc')
            ->filters([

            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => Storage::disk('s3')->temporaryUrl(
                        $record->file_name,
                        now()->addMinutes(5),
                        [
                            'ResponseContentDisposition' => 'attachment; filename="' . basename($record->file) . '"',
                        ]
                    ))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([

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
                            'chasis_number',
                            'reg_number',
                            'application_number',
                            'status',
                        ]),
                        now()->format('Y-m-d_H-i-s') . '-pending_acceptance_template.xlsx'
                    );

                }),

            Action::make('Add New Request')
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

                    try {
                        $pendingAcceptanceUpload = UploadProcessLog::create([
                            'name' => 'Pending Acceptance Upload',
                            'file_name' => $filePath,
                            'user_id' => auth()->id(),
                            'status' => 0, // Processing
                            'createdOn' => now(),
                            'process_type' => UploadProcessTypeEnum::PENDING_ACCEPTANCE->value,
                            'createdBy' => auth()->id(),
                        ]);

                        (new ProcessLogbookPendingAcceptanceImportJob($pendingAcceptanceUpload))->dispatch();

                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();

                    } catch (\Throwable $th) {
                        Log::info('Error uploading file: ' . $th->getMessage());
                        Notification::make()
                            ->title('Failed to start upload process')
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Upload Pending Acceptance File')
                ->modalSubmitActionLabel('Add Request')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {
        return UploadProcessLog::query()
            ->where('process_type', UploadProcessTypeEnum::PENDING_ACCEPTANCE->value)
            ->orWhere('name', 'Pending Acceptance Upload');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }
}
