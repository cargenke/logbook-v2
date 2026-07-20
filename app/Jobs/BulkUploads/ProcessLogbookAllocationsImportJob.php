<?php

namespace App\Jobs\BulkUploads;

use App\Exports\ExportLogs;
use App\Imports\ReceivedLogbooksImport;
use App\Jobs\SendLbAllocationsImportErrorNotificationJob;
use App\Jobs\SendLbAllocationsImportSuccessNotificationJob;
use App\Models\Logbook;
use App\Models\LogbookProfile;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessLogbookAllocationsImportJob implements ShouldQueue
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


        $import = new ReceivedLogbooksImport;

        $successfull = [];
        $failed = [];


        $user = User::select('id', 'name', 'email')->find($this->user_id);

        if (!$user) {
            Log::error("User not found for ID: {$this->user_id}. Aborting job.");
            return;
        }

   

        try {

            Excel::import($import, $this->filePath);

            $data = $import->rows;

            foreach ($data as $index => $row) {
                $requiredFields = ['chasis_number', 'reg_number'];

                foreach ($requiredFields as $field) {
                    if (!isset($row[$field]) || trim($row[$field]) === '') {

                        $message = "The field {$field} is required and cannot be empty. Row {$index} blank. Upload aborted.";

                        SendLbAllocationsImportErrorNotificationJob::dispatch($message, $user);

                        UploadProcessLog::where('file_name', $this->filePath)
                            ->update([
                                'status' => 0
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
            }

            foreach ($data as $row) {

                DB::beginTransaction();

                try {

                    // Check if the required keys exist in the row
                    if (isset($row['chasis_number']) && isset($row['reg_number'])) {
                        $chasisNumber = $row['chasis_number'];
                        $numberPlate = $row['reg_number'];

                        $logbook = Logbook::where('chasisNumber', $chasisNumber)->first();

                        if ($logbook) {

                            Logbook::where('chasisNumber', $chasisNumber)->update(
                                [
                                    'regNumber' => $numberPlate,
                                    'allocationsCreatedOn' => now(),
                                    'allocationsCreatedBy' => $this->user_id,
                                ]
                            );

                            LogbookProfile::where('chasisNumber', $chasisNumber)->update(
                                [
                                    'logbook_id' => $logbook->id,
                                    'regNumber' => $numberPlate,
                                    'allocationsCreatedOn' => now(),
                                    'allocationsCreatedBy' => $this->user_id,
                                ]
                            );


                            $successfuluploads = UploadedDataLog::create([
                                'name' => 'Received LogBook/Allocation',
                                'chasisNumber' => $chasisNumber,
                                'regNumber' => $numberPlate,
                                'status' => 'Success',
                                'remarks' => 'Allocation Successfull',
                                'createdOn' => Carbon::now(),
                                'createdBy' => $this->user_id
                            ]);

                            array_push($successfull, $successfuluploads);
                        } else {

                            $faileduploads = UploadedDataLog::create([
                                'name' => 'Received LogBook/Allocation',
                                'chasisNumber' => $chasisNumber,
                                'regNumber' => $numberPlate,
                                'status' => 'Failed',
                                'remarks' => 'Allocation Failed',
                                'createdOn' => Carbon::now(),
                                'createdBy' => $this->user_id
                            ]);

                            array_push($failed, $faileduploads);
                        }
                    } else {
                        throw new Exception('Missing required keys in the row: ' . json_encode($row));
                    }

                    DB::commit();
                } catch (Exception $e) {

                    Log::info("Error Processing Allocations" . $e->getMessage());

                    DB::rollBack();
                }

                continue;
            }
        } catch (Exception $e) {

            UploadProcessLog::where('file_name', $this->filePath)
                ->update([
                    'status' => 0
                ]);

            Log::error('Error importing file: ' . $e->getMessage());
        }

        UploadProcessLog::where('file_name', $this->filePath)
            ->update([
                'status' => 0
            ]);


        $uploadresults = array_merge($successfull, $failed);


        if (!empty($uploadresults)) {

            try {

                // Generate a unique filename
                $fileName = Carbon::now()->format('Y-m-d_H-i-s') . ' Allocation Data Upload Results.xlsx';
                // Log::info("Generated filename: {$fileName}");

                // Use disk method to ensure correct storage
                $disk = Storage::disk('local');
                $storagePath = "public/exports/{$fileName}";
                // Log::info("Generated storage path: {$storagePath}");

                // Store the file
                try {
                    Log::info("Processing Files To Export");
                    Excel::store(new ExportLogs($uploadresults, $user->name), $storagePath, 'local');
                    // Log::info("Processing Files To Export DONE Successfully");
                } catch (\Throwable $th) {
                    //throw $th;
                    Log::info("Processing Files To Export Failed " . $th);
                }

                // Get the absolute file path
                $fullPath = storage_path("app/{$storagePath}");
                // Log::info("Full file path: {$fullPath}");

                // Dispatch the job
                if ($user) {
                    SendLbAllocationsImportSuccessNotificationJob::dispatch($this->user_id, $user->name, $user->email, $fullPath, $storagePath);
                    // Log::info("Job dispatched successfully to user: {$user->email}");
                } else {
                    Log::error("Failed to dispatch job, user is null. User ID: {$this->user_id}");
                }
            } catch (Exception $e) {

                Log::error("Failed to send email to user {$user->email}: " . $e->getMessage());

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
