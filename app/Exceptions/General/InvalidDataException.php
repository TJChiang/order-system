<?php

namespace App\Exceptions\General;

use App\Exceptions\GeneralException;
use Psr\Log\LogLevel;

class InvalidDataException extends GeneralException
{
    protected int $statusCode = 500;
    protected string $error = 'Invalid data exception.';
    protected string $errorCode = 'E001005';
    protected string $logLevel = LogLevel::ERROR;
}
