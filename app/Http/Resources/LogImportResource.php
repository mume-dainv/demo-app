<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogImportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $jobTracking = $this->jobTracking()->first();
        return [
            'id' => $this->id,
            'errors' => json_decode($this->errors),
            'file_name' => $this->file_name,
            'job_name' => $jobTracking->job_name,
            'status' => $jobTracking->status,
            'total_rows' => $this->total_row,
            'row_fail' => $this->row_fail,
            'row_success' => $this->row_success,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
