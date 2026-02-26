<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\ImportUsersRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\LogImportResource;
use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Mail\RegisterUserMail;
use App\Repositories\LogImportRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class                                                                                                                                                                                                                                                       UserController extends BaseApiController
{
    public function __construct(protected UserRepository $userRepository, protected LogImportRepository $logImportRepository)
    {
    }

    public function index()
    {
        return $this->sendResponse(['users' => UserResource::collection($this->userRepository->all())]);
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

    public function show($id)
    {
        return $this->sendResponse(['user' => new UserResource($this->userRepository->find($id))]);
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
            $user = $this->userRepository->update($id, $data);
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

    public function import(ImportUsersRequest $request)
    {
        try {
            $file = $request->file('users');
            $import = new UsersImport($file->getClientOriginalName() . '_' . date('Y-m-d H-i-s'), auth()->user());
            $import->queue($file);
            return $this->sendResponse([], 'Users importing....');
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }

    public function logImport()
    {
        return $this->sendResponse(LogImportResource::collection(auth()->user()->logImport()->get()));
    }

    public function deleteLogImport($id)
    {
        try {
            DB::beginTransaction();
            $user = $this->logImportRepository->delete($id);
            DB::commit();
            return $this->sendResponse([], 'Log has been deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }
}
