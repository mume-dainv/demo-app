<?php

namespace App\Repositories;

use App\Models\UserLogging;

class UserLoggingRepository extends BaseRepository {

    function getModel()
    {
        return new UserLogging();
    }

    public function createOrUpdateByUserId($attributes)
    {
        $userLogging = $this->findBy(['user_id' => $attributes['user_id']])->first();
        if ($userLogging) {
            return $userLogging->update($attributes);
        }
        return $this->model->create($attributes);
    }
}
