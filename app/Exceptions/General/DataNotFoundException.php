<?php

namespace App\Exceptions\General;

use App\Exceptions\GeneralException;
use Psr\Log\LogLevel;

class DataNotFoundException extends GeneralException
{
    protected int $statusCode = 404;
    protected string $error = 'Data not found exception.';
    protected string $errorCode = 'E001004';
    protected string $logLevel = LogLevel::DEBUG;
}
