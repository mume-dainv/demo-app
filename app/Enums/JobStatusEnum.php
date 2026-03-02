<?php

namespace App\Enums;

enum JobStatusEnum : string
{
 case Running = 'Running';
 case Complete = 'Complete';
 case Failed = 'Failed';
}
