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
                ->url(route('filament.admin.resources.logbook-profiles.index', [
                    'filters[Status][values][0]' => 1
                ]))
                ->openUrlInNewTab()
                ->description('Pednding')
                ->color(LogBookStatusEnum::PENDING->color()),
            Stat::make('Processing', number_format(LogbookProfile::where('status', LogBookStatusEnum::PROCESSING->value)->count()))
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->description('Processing')
                ->url(route('filament.admin.resources.logbook-profiles.index', [
                    'filters[Status][values][0]' => LogBookStatusEnum::PROCESSING->value
                ]))
                ->openUrlInNewTab()
                ->color(LogBookStatusEnum::PROCESSING->color()),
            Stat::make('Pending Acceptance', number_format(LogbookProfile::where('status', LogBookStatusEnum::PENDING_ACCEPTANCE->value)->count()))
                ->descriptionIcon('heroicon-m-clock')
                ->url(route('filament.admin.resources.logbook-profiles.index', [
                    'filters[Status][values][0]' => LogBookStatusEnum::PENDING_ACCEPTANCE->value
                ]))
                ->openUrlInNewTab()
                ->description('Pending Acceptance')
                ->color(LogBookStatusEnum::PENDING_ACCEPTANCE->color()),
            Stat::make('With Issues', number_format(LogbookProfile::where('status', LogBookStatusEnum::WITH_ISSUES->value)->count()))
                ->descriptionIcon('heroicon-m-x-circle')
                ->description('With Issues')
                ->url(route('filament.admin.resources.logbook-profiles.index', [
                    'filters[Status][values][0]' => LogBookStatusEnum::WITH_ISSUES->value
                ]))
                ->openUrlInNewTab()
                ->color(LogBookStatusEnum::WITH_ISSUES->color()),

            Stat::make('Direct Trasfer', number_format(LogbookProfile::where('status', LogBookStatusEnum::DIRECT_REGISTRATION->value)->count()))
                ->descriptionIcon('heroicon-m-arrow-right')
                ->description('Direct Trasfer')
                ->url(route('filament.admin.resources.logbook-profiles.index', [
                    'filters[Status][values][0]' => LogBookStatusEnum::DIRECT_REGISTRATION->value
                ]))
                ->openUrlInNewTab()
                ->color(LogBookStatusEnum::DIRECT_REGISTRATION->color()),

            Stat::make('Dispatched', number_format(LogbookProfile::where('status', LogBookStatusEnum::DISPATCHED->value)->count()))
                ->descriptionIcon('heroicon-m-arrow-right')
                ->description('Direct Trasfer')
                ->url(route('filament.admin.resources.logbook-profiles.index', [
                    'filters[Status][values][0]' => LogBookStatusEnum::DISPATCHED->value
                ]))
                ->openUrlInNewTab()
                ->color(LogBookStatusEnum::DISPATCHED->color()),
        ];
    }
}
