<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Models\LogExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinishExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $userId,protected $filePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        LogExport::where([
            'user_id' => $this->userId,
            'file_path' => $this->filePath
        ])->update([
            'status' => JobStatusEnum::Complete->value
        ]);
    }
}
