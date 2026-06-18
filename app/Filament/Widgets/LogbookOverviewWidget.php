<?php

namespace App\Filament\Widgets;

use App\Enums\LogBookStatusEnum;
use App\Models\LogbookProfile;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogbookOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $logbookQuery = LogbookProfile::query();
        return [
            Stat::make('Pending', number_format($logbookQuery->where('status', LogBookStatusEnum::PENDING->value)->count()))
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->description('Pednding')
                ->color(LogBookStatusEnum::PENDING->color()),
            Stat::make('Processing', number_format(LogbookProfile::where('status', LogBookStatusEnum::PROCESSING->value)->count()))
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->description('Processing')
                ->color(LogBookStatusEnum::PROCESSING->color()),
            Stat::make('Pending Acceptance', number_format(LogbookProfile::where('status', LogBookStatusEnum::PENDING_ACCEPTANCE->value)->count()))
                ->descriptionIcon('heroicon-m-clock')
                ->description('Pending Acceptance')
                ->color(LogBookStatusEnum::PENDING_ACCEPTANCE->color()),
            Stat::make('With Issues', number_format(LogbookProfile::where('status', LogBookStatusEnum::WITH_ISSUES->value)->count()))
                ->descriptionIcon('heroicon-m-x-circle')
                ->description('With Issues')
                ->color(LogBookStatusEnum::WITH_ISSUES->color()),

              Stat::make('Direct Trasfer', number_format(LogbookProfile::where('status', LogBookStatusEnum::DIRECT_REGISTRATION->value)->count()))
                ->descriptionIcon('heroicon-m-arrow-right')
                ->description('Direct Trasfer')
                ->color(LogBookStatusEnum::DIRECT_REGISTRATION->color()),
        ];
    }
}
