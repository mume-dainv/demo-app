<?php

namespace App\Helpers;

use App\Enums\JobNameEnum;

class JobHelper
{
    static function getJobName($jobname): string
    {
        foreach (JobNameEnum::cases() as $case) {
            if (str_starts_with($jobname, $case->value)) {
                return $case->value;
            }
        }
        return '';
    }

    static function createJobName(string $jobname, $prefix): string
    {
        return $jobname .'_'.$prefix;
    }
}
