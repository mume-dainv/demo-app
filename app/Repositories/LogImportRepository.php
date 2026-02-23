<?php

namespace App\Repositories;

use App\Models\LogImport;
use App\Models\UserLogging;

class LogImportRepository extends BaseRepository
{

    function getModel()
    {
        return new LogImport();
    }
}
