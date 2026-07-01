<?php
namespace App\Filament\Pages;

use App\Enums\LogBookStatusEnum;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Models\LogbookRequest;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class Tickets extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.pages.tickets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Pencil;

    protected static string|UnitEnum|null $navigationGroup = 'Tickets Management';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        $isAdmin = auth()->user()?->hasAnyRole(['SuperAdmin', 'Admin']);
        $isOfficer = auth()->user()?->hasAnyRole(['SuperAdmin', 'Admin', 'RegOfficer']);
        return $table
            ->query($this->getBaseQuery())
            ->columns([

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn($state) => LogBookStatusEnum::from($state)->label()
                    )
                    ->color(
                        fn($state) => LogBookStatusEnum::from($state)->color()
                    ),

                TextColumn::make('user.name')
                    ->label('Created On'),

                TextColumn::make('assignto.name')
                    ->label('Assign To'),

                TextColumn::make('createdOn')
                    ->sortable(),

                TextColumn::make('owner_display')
                    ->label('Customer Name')
                    ->getStateUsing(
                        fn($record) =>
                        $record->profile->CustomerName ?? $record->profile->NumAtCard
                    ),

                TextColumn::make('branch_dealer')
                    ->label('Branch/Dealer')
                    ->getStateUsing(
                        fn($record) =>
                        $record->profile?->logbookOwner?->name ?? $record->profile?->Location ?? 'N/A'
                    ),

                TextColumn::make('chasisNumber')
                    ->copyable()
                    ->badge()
                    ->color('indigo')
                    ->searchable(),

                TextColumn::make('profile.regNumber')
                    ->label('Reg Number'),

                TextColumn::make('branch_display')
                    ->label('Branch/Dealer')
                    ->getStateUsing(
                        fn($record) =>
                        $record->profile?->logbookOwner?->name ?? $record->profile?->Location ?? 'N/A'
                    )
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(fn() => $isAdmin),

                TextColumn::make('user.name')
                    ->label('Requested By'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('Status')
                    ->multiple()
                    ->options(LogBookStatusEnum::class)
                    ->default([
                        LogBookStatusEnum::PENDING->value,
                        LogBookStatusEnum::PROCESSING->value,
                        LogBookStatusEnum::PENDING_ACCEPTANCE->value,
                        LogBookStatusEnum::ACCEPTED->value,
                    ])
                    ->label('Logbook Status'),
            ])
            ->actions([

                Action::make('approve')
                    ->label('Assign')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading('Assing Ticket to')
                    ->visible(fn($record) => $record->assign_to == null)
                    ->modalDescription('Are you sure you want to assign this record?')
                    ->modalSubmitActionLabel('Yes, Assign')
                    ->form([
                        Select::make('user_id')
                            ->label('Assign To')
                            ->options(
                                User::role(['SuperAdmin'])
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])

                    // Action when submitted
                    ->action(function (array $data, $record) {
                        $record->update([
                            'assign_to' => $data['user_id'],
                            'status' => LogBookStatusEnum::PROCESSING,
                        ]);

                        Notification::make()
                            ->title('Record approved successfully')
                            ->success()
                            ->send();
                    }),

                Action::make('mark_pending_acceptance')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->modalHeading('Change Status to Pending Acceptance')
                    ->visible(fn($record) => $record->status == LogBookStatusEnum::PROCESSING->value)
                    ->modalDescription('Are you sure you want to change status of  this record?')
                    ->modalSubmitActionLabel('Yes, Change')
                    ->action(function (array $data, $record) {


                        DB::beginTransaction();
                        try {
                        
                            LogbookProfile::where('chasisNumber', $record->chasisNumber)->update([
                                'status' => LogBookStatusEnum::PENDING_ACCEPTANCE,
                            ]);

                            Logbook::where('chasisNumber', $record->chasisNumber)->update([
                                'status' => LogBookStatusEnum::PENDING_ACCEPTANCE,
                            ]);


                            $record->update([
                                'status' => LogBookStatusEnum::PENDING_ACCEPTANCE,
                            ]);


                            Notification::make()
                                ->title('Status changed successfully')
                                ->success()
                                ->send();

                            DB::commit();
                        } catch (\Throwable $th) {

                            Notification::make()
                                ->title('Something went wrong' . $th->getMessage())
                                ->success()
                                ->send();

                            DB::rollBack();
                        }



                    }),
            ])
            ->bulkActions([
                // DeleteBulkAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getBaseQuery()
    {

        if (auth()->user()?->hasAnyRole(['SuperAdmin'])) {
            return LogbookRequest::query()->where('is_instant_transfer', true);
        }

        return LogbookRequest::query()
            ->orWhere('assign_to', auth()->user()->id)
            ->where('is_instant_transfer', true);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }

}
