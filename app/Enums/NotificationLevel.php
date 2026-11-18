<?php

namespace App\Enums;

enum NotificationLevel: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Failure = 'failure';
}
