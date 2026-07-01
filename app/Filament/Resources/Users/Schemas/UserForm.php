<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Farm;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                Tabs::make('Role Management')
                    ->tabs([
                        Tab::make('Role Information')
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required(),

                                TextInput::make('password')
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->password(),

                         

                            ]),

                        Tab::make('User Roles')
                            ->schema([
                                CheckboxList::make('roles')
                                    ->label('Assigned Roles')
                                    ->relationship('roles', 'name')
                                    ->options(Role::all()->pluck('name', 'id')->toArray())
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->helperText('Assign roles to this user. Checked roles are currently assigned to this user. Roles provide a collection of permissions.')
                                    ->searchable(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
