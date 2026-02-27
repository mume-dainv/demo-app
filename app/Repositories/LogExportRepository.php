<?php

namespace App\Repositories;

use App\Models\LogExport;

class LogExportRepository extends BaseRepository
{

    function getModel()
    {
        return new LogExport();
    }
}
