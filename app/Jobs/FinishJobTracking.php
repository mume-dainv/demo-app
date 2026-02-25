<?php

namespace App\Jobs;

use App\Enums\JobStatusEnum;
use App\Models\JobTracking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinishJobTracking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $userId, protected $jobName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        JobTracking::where([
            'user_id' => $this->userId,
            'job_name' => $this->jobName
        ])->update([
            'status' => JobStatusEnum::Complete->value
        ]);
    }

}
