<?php

namespace App\Exceptions\General;

use App\Exceptions\GeneralException;
use Psr\Log\LogLevel;

class InvalidArgumentException extends GeneralException
{
    protected int $statusCode = 422;
    protected string $error = 'Invalid argument exception.';
    protected string $errorCode = 'E001003';
    protected string $logLevel = LogLevel::DEBUG;
}
