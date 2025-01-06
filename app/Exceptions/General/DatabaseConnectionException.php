<?php

namespace App\Exceptions\General;

use Psr\Log\LogLevel;

class DatabaseConnectionException extends DatabaseException
{
    protected int $statusCode = 503;
    protected string $error = 'Database connection exception.';
    protected string $errorCode = 'E001002';
    protected string $logLevel = LogLevel::ERROR;
}
