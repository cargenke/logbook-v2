<?php

namespace App\Filament\Pages;

use App\Enums\LogBookStatusEnum;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Models\LogbookRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class MyTickets extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.my-tickets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Pencil;

    protected static string|UnitEnum|null $navigationGroup = 'Logbook Management';

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
                        fn ($state) => LogBookStatusEnum::from($state)->label()
                    )
                    ->color(
                        fn ($state) => LogBookStatusEnum::from($state)->color()
                    ),

                TextColumn::make('user.name')
                    ->label('Created On'),

                TextColumn::make('createdOn')
                    ->sortable(),

                TextColumn::make('owner_display')
                    ->label('Customer Name')
                    ->getStateUsing(
                        fn ($record) => $record->profile->CustomerName ?? $record->profile->NumAtCard
                    ),

                TextColumn::make('branch_dealer')
                    ->label('Branch/Dealer')
                    ->getStateUsing(
                        fn ($record) => $record->profile?->logbookOwner?->name ?? $record->profile?->Location ?? 'N/A'
                    ),

                TextColumn::make('chasisNumber')
                    ->copyable()
                    ->badge()
                    ->color('indigo')
                    ->searchable(),

                TextColumn::make('profile.regNumber')
                    ->label('Reg Number'),

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

            ])
            ->actions([

                Action::make('mark_pending_acceptance')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->modalHeading('Change Status to accepted')
                    ->visible(fn ($record) => $record->status == LogBookStatusEnum::PENDING_ACCEPTANCE->value)
                    ->modalDescription('Are you sure you want to change status of  this record?')
                    ->modalSubmitActionLabel('Yes, Change')
                    ->action(function (array $data, $record) {

                        DB::beginTransaction();
                        try {

                            LogbookProfile::where('chasisNumber', $record->chasisNumber)->update([
                                'status' => LogBookStatusEnum::ACCEPTED,
                            ]);

                            Logbook::where('chasisNumber', $record->chasisNumber)->update([
                                'status' => LogBookStatusEnum::ACCEPTED,
                            ]);

                            $record->update([
                                'status' => LogBookStatusEnum::ACCEPTED,
                            ]);

                            Notification::make()
                                ->title('Status changed successfully')
                                ->success()
                                ->send();

                            DB::commit();
                        } catch (\Throwable $th) {

                            Notification::make()
                                ->title('Something went wrong'.$th->getMessage())
                                ->success()
                                ->send();

                            DB::rollBack();
                        }

                    }),

            ])
            ->bulkActions([

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getBaseQuery()
    {
        return LogbookRequest::query()
            ->where('createdBy', auth()->user()->id)
            ->where('is_instant_transfer', true);
    }
}
