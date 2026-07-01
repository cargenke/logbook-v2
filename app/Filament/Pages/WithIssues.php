<?php
namespace App\Filament\Pages;

use App\Actions\LogbookActions\GetChasisInfoAction;
use App\Actions\LogbookActions\UpdateLogbookInfoAction;
use App\Enums\UploadProcessTypeEnum;
use App\Exports\TemplateExports\LogbooksPendingRequestTemplateExport;
use App\Models\UploadProcessLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class WithIssues extends Page implements HasTable
{

    use InteractsWithTable;
    protected string $view = 'filament.pages.with-issues';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowRight;

    protected static string|UnitEnum|null $navigationGroup = 'Bulk Operations';

    protected static ?int $navigationSort = 3;

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
                ->tooltip('Download hatching summary')
                ->action(function () {

                    return Excel::download(
                        new LogbooksPendingRequestTemplateExport([
                            [
                                'chasis_number' => '',
                                'reg_number' => '',
                                'status' => '',
                            ]
                        ]),
                        'Direct Transfer Template.xlsx'
                    );

                }),



            Action::make('Add New Request')
                ->label('Upload Direct Transfer')
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
                        $record = UploadProcessLog::create([
                            'name' => "Direct Transfer Upload",
                            'file_name' => $filePath,
                            'user_id' => auth()->id(),
                            'status' => 0,
                            'createdOn' => now(),
                            'process_type' => UploadProcessTypeEnum::ISSUES->value,
                            'createdBy' => auth()->id(),
                        ]);


            
                        Notification::make()
                            ->title('Upload started successfully')
                            ->success()
                            ->send();


                    } catch (\Throwable $th) {

                        Log::info("Error uploading file: " . $th);
                        Notification::make()
                            ->title('Adding New Request Failed')
                            ->danger()
                            ->send();
                    }

                })
                ->modalHeading('Upload With Issues File')
                ->modalSubmitActionLabel('Add Request')
                ->modalWidth('lg'),
        ];
    }

    protected function getBaseQuery()
    {

        if (auth()->user()?->hasAnyRole(['SuperAdmin'])) {
            return UploadProcessLog::query()->where('process_type', UploadProcessTypeEnum::ISSUES->value);
        }

        return UploadProcessLog::query()
            ->where('user_id', auth()->user()->id)
            ->where('process_type', UploadProcessTypeEnum::ISSUES->value);
    }



    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }

}
