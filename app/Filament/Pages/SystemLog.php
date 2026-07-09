<?php

namespace App\Filament\Pages;

use App\Models\UploadedDataLog;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class SystemLog extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.system-log';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery()) // your model here
            ->columns([
                TextColumn::make('createdOn')
                    ->label('Date'),
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('user.name')
                    ->label('Actioned By'),

                TextColumn::make('chasisNumber')
                    ->searchable()
                    ->label('Chasis Number'),
                TextColumn::make('regNumber')
                    ->searchable()
                    ->label('Reg Number'),
                TextColumn::make('status')
                    ->label('Status'),
                TextColumn::make('remarks')
                    ->searchable()
                    ->label('Remarks'),

            ])
            ->defaultSort('id', 'desc')
            ->filters([

            ])
            ->actions([

            ])
            ->bulkActions([

            ]);
    }

    protected function getBaseQuery()
    {

        return UploadedDataLog::query();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }
}
