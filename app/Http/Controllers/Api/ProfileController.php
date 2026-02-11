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

    public function me()
    {
        return $this->sendResponse(new ProfileResource(auth()->user()), 'User retrieved successfully.');
    }

    /**
     * @throws \Throwable
     */
    public function updateMe(UpdateMeRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $avatarPath = S3Helper::upload($request->file('avatar'));
            $dataUpdate = [...$data, 'avatar' => $avatarPath, 'id' => auth()->user()->id];
            $this->userRepository->update($dataUpdate);
            DB::commit();
            return $this->sendResponse([], 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e);
        }
    }


}
