<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    abstract function getModel();

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findBy($conditions, $oder = 'id', $sort = 'ASC')
    {
        return $this->model->where($conditions)->orderBy($oder, $sort);
    }

    public function delete($id)
    {
        return $this->model->find($id)?->delete();
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        return $this->find($id)->update($data);
    }
}
