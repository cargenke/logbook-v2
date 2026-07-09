<?php

namespace App\Filament\Resources\LogbookProfiles\Widgets;

use App\Enums\LogBookStatusEnum;
use App\Filament\Resources\LogbookProfiles\Pages\ListLogbookProfiles;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogbookStatOverView extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListLogbookProfiles::class;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }

    protected function getStats(): array
    {
        return [

            Stat::make('Pending', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::PENDING->value)->count()))
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->description('Pednding')
                ->color(LogBookStatusEnum::PENDING->color()),
            Stat::make('Processing', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::PROCESSING->value)->count()))
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->description('Processing')
                ->color(LogBookStatusEnum::PROCESSING->color()),
            Stat::make('Pending Acceptance', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::PENDING_ACCEPTANCE->value)->count()))
                ->descriptionIcon('heroicon-m-clock')
                ->description('Pending Acceptance')
                ->color(LogBookStatusEnum::PENDING_ACCEPTANCE->color()),
            Stat::make('With Issues', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::WITH_ISSUES->value)->count()))
                ->descriptionIcon('heroicon-m-x-circle')
                ->description('With Issues')
                ->color(LogBookStatusEnum::WITH_ISSUES->color()),
        ];
    }
}
