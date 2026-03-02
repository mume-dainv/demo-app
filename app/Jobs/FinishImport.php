<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Models\LogImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinishImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $userId, protected $filePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        LogImport::where([
            'user_id' => $this->userId,
            'file_name' => $this->filePath
        ])->update([
            'status' => JobStatusEnum::Complete->value
        ]);
    }

}
