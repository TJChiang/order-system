<?php

use App\Exceptions\General\DatabaseConnectionException;
use App\Exceptions\General\DatabaseException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (PDOException $e) {
            // FYI: https://github.com/sysown/proxysql/blob/2d6c897339239f06afb244bc542b9ced62fb9a97/include/proxysql_structs.h#L459
            if (!empty($e->errorInfo) && !empty(array_intersect(range(9001, 9020), $e->errorInfo))) {
                throw (new DatabaseConnectionException('Real db connection error.'))
                    ->setExtraError([
                        'error.type' => $e->getMessage(),
                    ]);
            }

            if ($e->getCode() == 2002) {
                throw (new DatabaseConnectionException('Connection error.'))
                    ->setExtraError([
                        'error.type' => $e->getMessage(),
                    ]);
            }

            return (new DatabaseException("PDOException : {$e->getMessage()}"))->render();
        });

        $exceptions->renderable(function (QueryException $e) {
            if ($e->getCode() == 2002) {
                throw (new DatabaseConnectionException('Connection error.'))
                    ->setExtraError([
                        'db.namespace' => $e->getConnectionName(),
                        'db.query.text' => $e->getSql(),
                        'error.type' => $e->getPrevious()->getMessage(),
                    ]);
            }

            return (new DatabaseException("PDOException : {$e->getMessage()}"))->render();
        });
    })->create();
