<?php

namespace App\Repositories;

class BaseRepository {
    protected $model;



    abstract function getModel();

    protected function FunctionName() : Returntype {
        return null;
    }
}
