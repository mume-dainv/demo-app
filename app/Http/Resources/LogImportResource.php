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
        return [
            'id' => $this->id,
            'errors' => json_decode($this->errors),
            'file_name' => $this->file_name,
            'status' => $this->status,
            'total_rows' => $this->total_row,
            'fail_count' => $this->fail_count,
            'success_count' => $this->success_count,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
