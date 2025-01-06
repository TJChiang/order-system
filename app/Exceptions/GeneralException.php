<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;

class GeneralException extends Exception
{
    protected int $statusCode = 500;
    protected string $error = 'General exception.';
    protected string $errorCode = 'E001000';
    protected string $logLevel = LogLevel::ERROR;
    protected string $wrap = 'data';

    private array $extraError = [];
    private array $extraData = [];

    /**
     * 設定放在 log 裡面的 context 資料
     */
    public function setExtraError(array $data): self
    {
        $this->extraError = $data;
        return $this;
    }

    /**
     * 設定要放在return 裡額外的資訊
     */
    public function setExtraData(array $data): self
    {
        $this->extraData = array_merge($this->extraData, $data);
        return $this;
    }

    public function setLogLevel(string $logLevel): self
    {
        $this->logLevel = $logLevel;
        return $this;
    }

    public function setCriticalLevel(): self
    {
        return $this->setLogLevel(LogLevel::CRITICAL);
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function report(): void
    {
        Log::log($this->logLevel, $this->error, array_merge([
            'exception.message' => $this->getMessage(),
        ], $this->extraError));
    }

    public function render(): JsonResponse
    {
        $responseData = (!empty($this->extraData) && !empty($this->wrap))
            ? [$this->wrap => $this->extraData]
            : $this->extraData;

        return response()->json(
            array_merge([
            'error' => $this->error,
            'error_code' => $this->errorCode,
            'error_description' => empty($this->getMessage()) ? $this->error : $this->getMessage(),
            ], $responseData),
            $this->statusCode
        );
    }
}
