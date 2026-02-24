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
            'messages' => json_decode($this->messages),
            'file_name' => $this->file_name,
            'date' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
