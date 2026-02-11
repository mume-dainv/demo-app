<?php

namespace App\Repositories;

abstract class BaseRepository {
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    abstract  function getModel();

    public function all()  {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findBy($conditions)
    {
        return $this->model->where($conditions);
    }

    public function delete($id)
    {
        return $this->model->find($id)?->delete();
    }

    public function createOrUpdate($attributes) {
        if ($attributes['id']) {
            return $this->find($attributes['id'])->update($attributes);
        }

        return $this->model->create($attributes);
    }
}
