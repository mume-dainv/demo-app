<?php

namespace App\Repositories;

use App\Models\LogImport;

class LogImportRepository extends BaseRepository
{

    function getModel()
    {
        return new LogImport();
    }
}
