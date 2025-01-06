<?php

namespace App\Exceptions\General;

use App\Exceptions\GeneralException;
use Psr\Log\LogLevel;

class DatabaseException extends GeneralException
{
    protected int $statusCode = 500;
    protected string $error = 'Database exception.';
    protected string $errorCode = 'E001001';
    protected string $logLevel = LogLevel::ERROR;
}
