<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoggingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ip' => $this?->ip,
            'user_agent' => $this?->user_agent,
            'login_at' => $this?->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
