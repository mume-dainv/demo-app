<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{

    function getModel()
    {
        return new User();
    }

    public function getListUser($condition)
    {
        $queries = [];
        $limit = $condition['limit'] ?? 10;
        $page = $condition['page'] ?? 1;
        if (isset($condition['name_like'])) {
            $queries[] = ['name', 'like', '%' . $condition['name_like'] . '%'];
        }

        return $this->paginate($queries, ['*'], $page, $limit);
    }

    public function getListJobExport($conditions)
    {
        $user = auth()->user()->logExport();
        $page = $conditions['page'] ?? 1;
        $limit = $conditions['limit'] ?? 10;
        if (isset($conditions['file_path'])) {
            $user->where('file_path', 'like', '%' . $conditions['file_path'] . '%');
        }
        return $user->with('jobTracking')->paginate($limit, ['*'], 'page', $page);
    }

}
