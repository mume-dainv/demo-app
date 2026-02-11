<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\RegisterUserMail;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class UserController extends BaseApiController
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function index()
    {
        return $this->userRepository->all();
    }

    /**
     * @throws Throwable
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $password = Str::random(8);
            $data['password'] = Hash::make($password);
            $newUser = $this->userRepository->create($data);
            $data['password'] = $password;
            Mail::to($data['email'])->queue(new RegisterUserMail($data));
            DB::commit();
            return $this->sendResponse($newUser, 'User has been created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function update($id, UpdateUserRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            if (!$this->userRepository->find($id)) {
                return $this->sendErrorResponse([], 'User not found.', ResponseAlias::HTTP_NOT_FOUND);
            }
            $user = $this->userRepository->update($id,$data);
            DB::commit();
            return $this->sendResponse($user, 'User has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $user = $this->userRepository->delete($id);
            DB::commit();
            return $this->sendResponse($user, 'User has been deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }
}
