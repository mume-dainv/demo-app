<?php

namespace App\Http\Controllers\Api;

use App\Http\Helpers\S3Helper;
use App\Http\Requests\UpdateMeRequest;
use App\Http\Resources\ProfileResource;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class ProfileController extends BaseApiController
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function profile()
    {
        return $this->sendResponse(new ProfileResource(auth()->user()), 'User retrieved successfully.');
    }

    /**
     * @throws \Throwable
     */
    public function updateProfile(UpdateMeRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $file = $request->file('avatar');
            $user = auth()->user();
            $avatarPath = $user->avatar;
            if ($file) {
                if ($avatarPath) {
                    S3Helper::delete($avatarPath);
                }
                $avatarPath = S3Helper::upload($request->file('avatar'));
            }
            $dataUpdate = [...$data, 'avatar' => $avatarPath];
            $this->userRepository->update($user->id, $dataUpdate);
            DB::commit();
            return $this->sendResponse([], 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e);
        }
    }
}
