<?php

namespace App\Filament\Resources\LogbookProfiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LogbookProfileForm
{
    public static function configure(Schema $schema): Schema
    {

        $canEdit = false;

        return $schema
            ->components([

                Section::make('Logbook Profile')

                    ->schema([
                        TextInput::make('chasisNumber')->label('Chasis Number'),
                        TextInput::make('regNumber')->label('Registration Number'),
                        TextInput::make('logBookFee')->label('Logbook Fee'),
                        TextInput::make('CardCode')->label('Customer Code'),
                        TextInput::make('CustomerName')->label('Customer Name'),
                        TextInput::make('DocNum')->label('Document Number'),
                        TextInput::make('Location')->label('Location'),
                        TextInput::make('PinNo')->label('PIN Number'),
                        TextInput::make('IDNo')->label('ID Number'),

                    ])
                    ->columnSpan('full')
                    ->columns(4),
                Section::make('Logbook Request Details')
                    ->columnSpan('full')
                    ->schema([

                        Section::make('First Owner Details')
                            ->schema([
                                TextInput::make('name1')
                                    ->label('Name')
                                    ->readOnly(! $canEdit)
                                    ->required(),

                                TextInput::make('tel1')
                                    ->label('Phone Number 1')
                                    ->readOnly(! $canEdit)
                                    ->required(),

                                TextInput::make('tel2')
                                    ->label('Phone Number 2')
                                    ->readOnly(! $canEdit)
                                    ->required(),

                                TextInput::make('PinNo1')
                                    ->label('KRA Pin No 1')
                                    ->readOnly(! $canEdit)
                                    ->required(),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->readOnly(! $canEdit)
                                    ->required(),

                                TextInput::make('modeofpayment')
                                    ->label('Mode of Payment'),
                            ])->columns(3),

                        Section::make('Other Owners Details 2')
                            ->schema([
                                TextInput::make('name2')
                                    ->readOnly(! $canEdit)
                                    ->label('Name'),

                                TextInput::make('PinNo2')
                                    ->readOnly(! $canEdit)
                                    ->label('KRA Pin No 2'),

                                TextInput::make('PinNo3')
                                    ->readOnly(! $canEdit)
                                    ->label('PIN Number 3'),

                            ])->columns(4),

                    ]),

            ]);
    }
}
