<?php

namespace App\Filament\Resources\LogbookRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LogbookRequestForm
{
    public static function configure(Schema $schema): Schema
    {

        $canEdit = false;
        return $schema
            ->components([

                Section::make('Logbook Request Details')
                    ->columnSpan('full')
                    ->schema([

                        Section::make('First Owner Details')
                            ->schema([
                                TextInput::make('name1')
                                    ->label('Name')
                                    ->readOnly(!$canEdit)
                                    ->required(),

                                TextInput::make('tel1')
                                    ->label('Phone Number 1')
                                    ->readOnly(!$canEdit)
                                    ->required(),

                                TextInput::make('tel2')
                                    ->label('Phone Number 2')
                                    ->readOnly(!$canEdit)
                                    ->required(),

                                TextInput::make('PinNo1')
                                    ->label('KRA Pin No 1')
                                    ->readOnly(!$canEdit)
                                    ->required(),


                                TextInput::make('email')
                                    ->label('Email')
                                    ->readOnly(!$canEdit)
                                    ->required(),


                                TextInput::make('modeofpayment')
                                    ->label('Mode of Payment'),
                            ])->columns(3),

                        Section::make('Other Owners Details')
                            ->schema([
                                TextInput::make('name2')
                                    ->readOnly(!$canEdit)
                                    ->label('Name'),

                                TextInput::make('PinNo2')
                                    ->readOnly(!$canEdit)
                                    ->label('KRA Pin No 2'),

                                TextInput::make('PinNo3')
                                    ->readOnly(!$canEdit)
                                    ->label('PIN Number 3'),

                                    
                                Toggle::make('is_instant_transfer')
                                    ->readOnly(!$canEdit)
                                    ->label('Is Instant Transfer'),

                            ])->columns(4),

                    ]),

            ]);
    }
}
