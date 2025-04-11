<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

abstract class Controller
{
    /**
     * Handle internal errors by logging them and returning a 500 response.
     *
     * @param \Exception $exception
     * @param string|null $customMessage
     * @return JsonResponse
     */
    public function handleInternalError(\Exception $exception, string $customMessage): JsonResponse
    {
        Log::error('Internal Server Error: ' . $exception->getMessage(), [
            'exception' => $exception,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
        $message = $customMessage ?? 'An unexpected error occurred. Please try again later.';
        return response()->json([
            'status' => 'failure',
            'message' => $message
        ], 500);
    }

    /**
     * Handle validation errors by logging them and returning a 422 response.
     *
     * @param ValidationException $exception
     * @return JsonResponse
     */
    public function handleValidationError(ValidationException $exception): JsonResponse
    {
        Log::warning('Validation error: ', [
            'errors' => $exception->errors()
        ]);
        return response()->json([
            'status' => 'failure',
            'message' => 'Validation failed.',
            'errors' => $exception->errors(),
        ], 422);
    }
}
