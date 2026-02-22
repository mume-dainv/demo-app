<?php

namespace App\Repositories;

use App\Models\UserLogging;

class UserLoggingRepository extends BaseRepository
{

    function getModel()
    {
        return new UserLogging();
    }

    public function createOrUpdateByUserId($attributes)
    {
        $userLogging = $this->findBy(['user_id' => $attributes['user_id']], 'updated_at')->get();
        if ($userLogging->count() == 5) {
            return $userLogging->first()->update($attributes);
        }

        return $this->model->create($attributes);
    }
}
