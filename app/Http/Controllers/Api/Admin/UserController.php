<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\JobStatusEnum;
use App\Exports\UsersExport;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\ImportUsersRequest;
use App\Http\Requests\JobExportQuery;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserQueryRequest;
use App\Http\Resources\JobExportUserResource;
use App\Http\Resources\LogImportResource;
use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Jobs\FinishExport;
use App\Jobs\FinishImport;
use App\Mail\RegisterUserMail;
use App\Models\LogExport;
use App\Repositories\LogExportRepository;
use App\Repositories\LogImportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class UserController extends BaseApiController
{
    public function __construct(
        protected UserRepository      $userRepository,
        protected LogImportRepository $logImportRepository,
        protected LogExportRepository $logExportRepository,
    )
    {
    }

    public function index(UserQueryRequest $request)
    {
        $users = $this->userRepository->getListUser($request->all());
        return $this->sendResponseWithPaginate(['users' => UserResource::collection($users)], $users, 'List users');
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
            DB::beginTransaction();
            $path = $request->file('users');
            $user = auth()->user();
            $fileName = $path->getClientOriginalName() . '_' . date('Y-m-d H-i-s') . '_' . Str::random(6) . '.' . $user->id;

            $import = new UsersImport($fileName, $user, $fileName);

            $file = new \SplFileObject($path);
            $file->seek(PHP_INT_MAX);
            $this->logImportRepository->create([
                'user_id' => $user->id,
                'file_name' => $fileName,
                'total_row' => $file->key() - 1,
                'status' => JobStatusEnum::Running->value,
            ]);

            $import->queue($path)->chain([new FinishImport($user->id, $fileName)]);
            DB::commit();
            return $this->sendResponse([], 'Users importing....');
        } catch (\Exception $e) {
            DB::rollBack();
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
            $this->logImportRepository->delete($id);
            DB::commit();
            return $this->sendResponse([], 'Log has been deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }

    public function exportUsers(Request $request)
    {
        $conditions = $request->all();
        $user = auth()->user();
        $filePath = 'exports/users_' . date('d-m-Y-H-i-s') . '_' . Str::random(6) . $user->id . '.csv';
        $this->logExportRepository->create([
            'user_id' => $user->id,
            'file_path' => $filePath,
            'status' => JobStatusEnum::Running->value,
        ]);
        (new UsersExport($conditions, $user, $filePath))->queue($filePath)->chain([
            new FinishExport($user->id, $filePath)
        ]);
        return $this->sendResponse([], 'User exporting...');
    }

    public function jobExportUsers(JobExportQuery $request)
    {
        $jobs = $this->userRepository->getListJobExport($request->all());
        return $this->sendResponseWithPaginate(JobExportUserResource::collection($jobs), $jobs);
    }

    public function downloadExportUsers(Request $request)
    {
        try {
            $path = $request->get('path');
            return Storage::disk(config('filesystems.default'))->download($path);
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e, $e->getMessage() || 'File not found.');
        }
    }

    public function deleteExportUsers($id)
    {
        try {
            DB::beginTransaction();
            LogExport::find($id)?->delete();
            DB::commit();
            return $this->sendResponse([], 'Deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e, $e->getMessage());
        }
    }
}
