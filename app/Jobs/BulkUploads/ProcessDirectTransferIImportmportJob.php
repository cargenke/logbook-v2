<?php
namespace App\Jobs\BulkUploads;

use App\Enums\LogBookStatusEnum;
use App\Imports\BulkTaskImports;
use App\Models\LogbookProfile;
use App\Models\UploadedDataLog;
use App\Models\UploadProcessLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessDirectTransferIImportmportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $filePath;
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected UploadProcessLog $uploadProcessLog)
    {
        $this->uploadProcessLog = $uploadProcessLog;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $uploadProcessLog = $this->uploadProcessLog;

        if (! Storage::disk('s3')->exists($uploadProcessLog->file_name)) {
            dd($uploadProcessLog);
        }

        try {

            $data = Excel::toArray(
                new BulkTaskImports,
                $uploadProcessLog->file_name,
                's3'
            );

        } catch (Exception $e) {

            UploadProcessLog::where('file_name', $this->filePath)
                ->update([
                    'status' => 0,
                ]);
            Log::error('Error importing file: ' . $e);

            return;
            // throw $e;
        }

        foreach ($data[0] as $index => $row) {

            $chasisNumber = $row['frame_no'] ?? null;

            try {
                $logbook = LogbookProfile::withoutGlobalScopes()->where('chasisNumber', $chasisNumber)->first();

                if (! $logbook) {

                    $faileduploads = UploadedDataLog::create([
                        'name'         => 'Direct Transfer Update',
                        'chasisNumber' => $chasisNumber,
                        'regNumber'    => $chasisNumber,
                        'status'       => 'Failed',
                        'remarks'      => "Logbook with chasisNumber {$chasisNumber} does not exist",
                        'createdOn' => Carbon::now(),
                        'createdBy' => $uploadProcessLog->createdBy,
                    ]);
                    continue;
                }

                $successfuluploads = UploadedDataLog::create([
                    'name'         => 'Direct Transfer Update',
                    'chasisNumber' => $chasisNumber,
                    'regNumber'    => $chasisNumber,
                    'status'       => 'Success',
                    'remarks'      => 'Direct Transfer Updated Successfully',
                    'createdOn'    => Carbon::now(),
                    'createdBy'    => $uploadProcessLog->createdBy,
                ]);

                $logbook->update([
                    'groupCode' => 'direct_transfer',
                    'status'    => LogBookStatusEnum::DIRECT_REGISTRATION->value,
                ]);

            } catch (\Throwable $th) {

                $faileduploads = UploadedDataLog::create([
                    'name'         => 'Direct Transfer Update',
                    'chasisNumber' => $chasisNumber,
                    'regNumber'    => $chasisNumber,
                    'status'       => 'Failed',
                    'remarks'      => "{$chasisNumber} : {$th->getMessage()}",
                    'createdOn' => Carbon::now(),
                    'createdBy' => $uploadProcessLog->createdBy,
                ]);

            }

        }

        $this->uploadProcessLog->update([
            'status' => 0,
        ]);

    }
}
