<?php

namespace App\Filament\Resources\LogbookRequests\Tables;

use App\Enums\LogBookStatusEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LogbookRequestsTable
{
    public static function configure(Table $table): Table
    {

        $isAdmin = auth()->user()?->hasAnyRole(['SuperAdmin', 'Admin']);
        $isOfficer = auth()->user()?->hasAnyRole(['SuperAdmin', 'Admin', 'RegOfficer']);

        return $table
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

                   TextColumn::make('createdOn')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),


                TextColumn::make('profile.DocDate')
                    ->date('Y-m-d')
                    ->visible(fn() => $isAdmin)
                    ->label('Doc Date'),

                TextColumn::make('owner_display')
                    ->label('Customer Name')
                    ->getStateUsing(
                        fn($record) => $record->profile->CustomerName ?? $record->profile->NumAtCard
                    ),

                TextColumn::make('owner_display')
                    ->label('Customer Name')
                    ->getStateUsing(
                        fn($record) => $record->profile->CustomerName ?? $record->profile?->NumAtCard ?? 'N/A'
                    ),

                TextColumn::make('chasisNumber')
                    ->copyable()
                    ->badge()
                    ->color('indigo')
                    ->searchable(),

                TextColumn::make('profile.regNumber')
                    ->searchable()
                    ->label('Reg Number'),

                TextColumn::make('profile.isAvailable')
                    ->label('LB Status')
                    ->tooltip(fn($state) => $state ? 'Logbook is available' : 'Logbook is not available')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->color(fn($state) => $state ? 'success' : 'warning'),

                TextColumn::make('branch_display')
                    ->label('Branch/Dealer')
                    ->getStateUsing(
                        fn($record) => $record->profile?->logbookOwner?->name ?? $record->profile?->Location ?? 'N/A'
                    )
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(fn() => $isAdmin),

                TextColumn::make('sap_location')
                    ->label('SAP Location')
                    ->getStateUsing(
                        fn($record) => $record->profile?->Location ?? 'N/A'
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
            ->defaultSort('createdOn', 'desc')
            ->filters([
                SelectFilter::make('profile_status')
                    ->label('Logbook Status')
                    ->multiple()
                    ->options(
                        collect(LogBookStatusEnum::cases())
                            ->mapWithKeys(fn($case) => [
                                $case->value => $case->label(),
                            ])
                            ->toArray()
                    )
                    ->default([
                        LogBookStatusEnum::PENDING,
                        LogBookStatusEnum::PROCESSING,
                        LogBookStatusEnum::PENDING_ACCEPTANCE,
                        LogBookStatusEnum::WITH_ISSUES,
                    ])
                    ->query(function ($query, array $data) {
                        if (!filled($data['values'])) {
                            return;
                        }

                        $query->whereHas('profile', function ($q) use ($data) {
                            $q->whereIn('status', $data['values']);
                        });
                    }),
                    
                 Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')

                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        DatePicker::make('created_until')

                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('createdOn', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('createdOn', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Requested From ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Requested To ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->hidden(fn($record) => $record->isClosed),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                ]),
            ]);
    }
}
