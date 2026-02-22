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
        $userLogging = $this->userLogging()->orderBy('updated_at', 'DESC')->get();
        return [
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'avatar_url' => S3Helper::getUrl($this->avatar),
            'role' => $this->role,
            'user_logging' => UserLoggingResource::collection($userLogging),

        ];
    }
}
