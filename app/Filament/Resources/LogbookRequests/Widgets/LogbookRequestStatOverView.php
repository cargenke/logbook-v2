<?php

namespace App\Filament\Resources\LogbookRequests\Widgets;

use App\Enums\LogBookStatusEnum;
use App\Filament\Resources\LogbookRequests\Pages\ListLogbookRequests;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogbookRequestStatOverView extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListLogbookRequests::class;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }

    protected function getStats(): array
    {
        
        return [

            Stat::make('Processing/WIP', number_format($this->getPageTableQuery()->select('')->whereIn('status',[LogBookStatusEnum::PENDING->value,  LogBookStatusEnum::PROCESSING->value])->count()))
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->description('Processing/WIP')
                ->color(LogBookStatusEnum::PROCESSING->color()),
            Stat::make('Pending Acceptance', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::PENDING_ACCEPTANCE->value)->count()))
                ->descriptionIcon('heroicon-m-clock')
                ->description('Pending Acceptance')
                ->color(LogBookStatusEnum::PENDING_ACCEPTANCE->color()),

            Stat::make('With Issues', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::WITH_ISSUES->value)->count()))
                ->descriptionIcon('heroicon-m-x-circle')
                ->description('With Issues')
                ->color(LogBookStatusEnum::WITH_ISSUES->color()),

            Stat::make('Dispatched', number_format($this->getPageTableQuery()->where('status', LogBookStatusEnum::DISPATCHED->value)->count()))
                ->descriptionIcon('heroicon-m-check-circle')
                ->description('Dispatched')
                ->color(LogBookStatusEnum::DISPATCHED->color()),

        ];
    }
}
