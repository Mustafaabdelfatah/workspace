<?php
namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Exception;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if($request->expectsJson()) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['error' => 'Resource not found'], Response::HTTP_NOT_FOUND);
            }
    
            if ($exception instanceof AuthorizationException) {
                return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
            }
    
            if ($exception instanceof ValidationException) {
                return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            if ($exception instanceof HttpException) {
                return response()->json(['error' => 'HTTP Exception'], $exception->getStatusCode());
            }
    
            if ($exception instanceof QueryException) {
                return response()->json(['error' => 'Query Exception'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        return parent::render($request, $exception);
    }
}
