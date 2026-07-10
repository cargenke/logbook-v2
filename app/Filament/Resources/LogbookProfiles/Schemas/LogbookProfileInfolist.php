<?php

namespace App\Filament\Resources\LogbookProfiles\Schemas;

use Filament\Schemas\Schema;

class LogbookProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            ])->columns(3);
    }
}
