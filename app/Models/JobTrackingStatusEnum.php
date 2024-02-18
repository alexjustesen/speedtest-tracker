<?php

namespace App\Models;

enum JobTrackingStatusEnum: string
{
    case Queued = 'queued';
    case Complete = 'complete';
    case Failed = 'failed';
    case Pending = 'pending';

}
