<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\UserRepository;

class UserController extends BaseApiController
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function index()
    {
        return $this->userRepository->all();
    }
}
