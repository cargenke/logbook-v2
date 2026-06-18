<?php
namespace App\Console\Commands\SyncCommands;

use App\Actions\LogbookActions\SyncChasisSalesDataAction;
use App\Actions\LogbookActions\UpdateLogbookInfoAction;
use App\Enums\UploadProcessTypeEnum;
use App\Models\UploadProcessLog;
use Carbon\CarbonPeriod;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Signature('app:sync-sales-for-period {start_date} {end_date}')]
#[Description('Sync Sales For Period')]
class SyncSalesForPeriodCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = Auth::loginUsingId(1);

        $startDate = $this->argument('start_date');
        $endDate   = $this->argument('end_date');

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {

            $this->info("Syncing Sales For: " . $date);

            $dateFormatted = $date->format('Ymd');

            $record = UploadProcessLog::create([
                'name'         => 'Sync Sales Data For: ' . $dateFormatted,
                'file_name'    => $dateFormatted,
                'user_id'      => auth()->id(),
                'status'       => -1,
                'createdOn'    => now(),
                'process_type' => UploadProcessTypeEnum::SYNC_SALES->value,
                'createdBy'    => auth()->id(),
            ]);

            try {
                $logbooks = (new SyncChasisSalesDataAction($dateFormatted))->handle();

                foreach ($logbooks as $key => $logbookInfo) {
                    (new UpdateLogbookInfoAction($logbookInfo))->handle();
                }

                $record->update([
                    'status' => 1,
                    'name'   => 'Sync Sales Data For: ' . $dateFormatted . " Total Sync: " . count($logbooks),
                ]);

            } catch (\Throwable $th) {

                Log::info("Failed  " . $th);

                $this->info($th->getMessage());

                $record->update([
                    'status' => 0,
                    'name'   => "Failed Syncing for: " . $dateFormatted . ", Total Pulled Sync: " . count($logbooks),
                ]);
                continue;
            }

            $this->comment("Finished Syncing Sales For: " . $date);
        }

    }
}
