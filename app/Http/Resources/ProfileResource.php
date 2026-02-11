<?php

namespace App\Http\Resources;

use App\Http\Helpers\S3Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userLogging = $this->userLogging;
        return [
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => S3Helper::getUrl($this->avatar),
            'ip' => $userLogging?->ip,
            'user_agent' => $userLogging?->user_agent,
            'last_login_at' => $userLogging?->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
