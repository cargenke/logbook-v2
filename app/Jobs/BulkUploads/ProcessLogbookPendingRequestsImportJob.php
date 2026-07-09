<?php

namespace App\Jobs\BulkUploads;

use App\Exports\ExportLogs;
use App\Imports\BulkTaskImports;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Models\SystemStatus;
use App\Models\UploadedDataLog;
use App\Models\UploadProcessLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessLogbookPendingRequestsImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath, $user_id)
    {
        $this->filePath = $filePath;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $import = new BulkTaskImports;

        $user = User::where('id', $this->user_id)->first();

        try {

            Excel::import($import, $this->filePath);

            $data = $import->rows;

            foreach ($data as $index => $row) {

                $requiredFields = ['chasis_number', 'reg_number', 'status'];

                foreach ($requiredFields as $field) {
                    if (! isset($row[$field]) || trim($row[$field]) === '') {

                        $message = "The field {$field} is required and cannot be empty. Row {$index} is blank. Upload aborted.";

                        // SendLbPendingRequestsImportErrorNotificationJob::dispatch($message, $user);

                        UploadProcessLog::where('file_name', $this->filePath)
                            ->update([
                                'status' => 0,
                            ]);

                        // Delete the temporary file
                        if (Storage::exists($this->filePath)) {
                            Storage::delete($this->filePath);
                        } else {
                            Log::warning("Temporary file not found for deletion: {$this->filePath}");
                        }

                        return;
                    }
                }

                // Validate status: must be 1 and numeric
                $status = $row['status'];

                if ($status != 1) {

                    $message = "Status must be 1 and numeric. Row {$index} with {$status} found. Upload aborted.";

                    //  SendLbPendingRequestsImportErrorNotificationJob::dispatch($message, $user);

                    // Delete the temporary file
                    if (Storage::exists($this->filePath)) {
                        Storage::delete($this->filePath);
                    } else {
                        Log::warning("Temporary file not found for deletion: {$this->filePath}");
                    }

                    return;
                }
            }

            $successfull = [];
            $failed = [];

            foreach ($data as $index => $row) {

                DB::beginTransaction();

                try {

                    $chasisNumber = $row['chasis_number']; // Adjust this to match your actual column name
                    $RegNumber = $row['reg_number']; // Adjust this to match your actual column name
                    $status = $row['status']; // Adjust this to match your actual column name

                    $logbook = Logbook::where('chasisNumber', $chasisNumber)->first();
                    $systemstatus = SystemStatus::where('id', $status)->first();

                    if ($logbook) {

                        Logbook::where('chasisNumber', $chasisNumber)
                            ->update(
                                [
                                    'status' => $status,
                                    'editedOn' => now(),
                                    'editedBy' => $this->user_id,
                                ]
                            );

                        LogbookProfile::where('chasisNumber', $chasisNumber)
                            ->update(
                                [
                                    'status' => $status,
                                    'editedOn' => now(),
                                    'editedBy' => $this->user_id,

                                ]
                            );

                        $successfuluploads = UploadedDataLog::create([
                            'name' => $systemstatus->name,
                            'chasisNumber' => $chasisNumber,
                            'regNumber' => $RegNumber,
                            'status' => 'Success',
                            'remarks' => 'Pending Request Successfull',
                            'createdOn' => Carbon::now(),
                            'createdBy' => $this->user_id,
                        ]);

                        array_push($successfull, $successfuluploads);

                        // Log::info($successfuluploads);
                    } else {

                        $faileduploads = UploadedDataLog::create([
                            'name' => $systemstatus->name,
                            'chasisNumber' => $chasisNumber,
                            'regNumber' => $RegNumber,
                            'status' => 'Failed',
                            'remarks' => "Logbook with chasisNumber {$chasisNumber} does not exist",
                            'createdOn' => Carbon::now(),
                            'createdBy' => $this->user_id,
                        ]);

                        array_push($failed, $faileduploads);
                    }

                    if (! $logbook) {
                        $data = Logbook::firstOrCreate(
                            [
                                'chasisNumber' => $chasisNumber,
                            ],
                            [

                                'data_source' => 'SCALA',
                                'status' => $status,
                                'createdOn' => now(),
                                'creatededBy' => $this->user_id,
                            ]
                        );

                        $data = LogbookProfile::firstOrCreate(
                            [
                                'chasisNumber' => $chasisNumber,
                            ],
                            [
                                'logbook_id' => $data->id,
                                'data_source' => 'SCALA',
                                'status' => $status,
                                'createdOn' => now(),
                                'creatededBy' => $this->user_id,
                            ]
                        );

                        Log::info('Scala Data Created Successfully'.$data->chasisNumber);

                        $successfuluploadsdata = UploadedDataLog::create([
                            'name' => $systemstatus->name,
                            'chasisNumber' => $chasisNumber,
                            'regNumber' => $RegNumber,
                            'status' => 'Success',
                            'remarks' => 'Pending Request Successfull - SCALA DATA',
                            'createdOn' => Carbon::now(),
                            'createdBy' => $this->user_id,
                        ]);

                        array_push($successfull, $successfuluploadsdata);
                    }

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Error processing requests uploads: '.$e->getMessage());
                }

                continue;
            }
        } catch (Exception $e) {

            UploadProcessLog::where('file_name', $this->filePath)
                ->update([
                    'status' => 0,
                ]);

            Log::error('Error importing file: '.$e->getMessage());
            // throw $e;
        }

        UploadProcessLog::where('file_name', $this->filePath)
            ->update([
                'status' => 0,
            ]);

        $uploadresults = array_merge($successfull, $failed);

        if (count($uploadresults) > 0) {
            // Generate a unique filename
            $fileName = Carbon::now()->format('Y-m-d_H-i-s').' Pending Requests Data Upload Results By '.$user->name.'.xlsx';

            // Use disk method to ensure correct storage
            $disk = Storage::disk('local');
            $storagePath = "public/exports/{$fileName}";

            // Store the file
            Excel::store(new ExportLogs($uploadresults, $user->name), $storagePath, 'local');

            // Get the absolute file path
            $fullPath = storage_path("app/{$storagePath}");

            try {

                //  SendLbPendingRequestsImportSuccessNotificationJob::dispatch($user, $fullPath);
            } catch (Exception $e) {

                Log::error("Failed to send email to user {$user->email}: ".$e->getMessage());

                // Attempt to delete the file even if email fails
                $disk->delete($storagePath);
            }
        }

        // Delete the temporary file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        } else {
            Log::warning("Temporary file not found for deletion: {$this->filePath}");
        }
    }
}
