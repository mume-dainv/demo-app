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

    public function getListJobExport()
    {
        $user = auth()->user()->logExport()->with('jobTracking');
        return $user->paginate();
    }

}
