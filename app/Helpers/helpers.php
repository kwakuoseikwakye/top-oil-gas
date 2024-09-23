<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('apiSuccessResponse')) {
      /**
       * Generate a success API response.
       *
       * @param mixed $data
       * @param string $message
       * @param int $statusCode
       * @return JsonResponse
       */
      function apiSuccessResponse(string $message = 'Success',  int $statusCode = 200, $data = null): JsonResponse
      {
            $response = [
                  'status' => true,
                  'code' => 'SUCCESS',
                  'message' => $message,
                  'trace_id' => Str::uuid()->toString(),
            ];

            if (!is_null($data)) {
                  $response['data'] = $data;
            }

            return response()->json($response, $statusCode);
      }
}

if (!function_exists('apiErrorResponse')) {
      /**
       * Generate an error API response.
       *
       * @param string $message
       * @param int $statusCode
       * @param mixed $details
       * @return JsonResponse
       */
      function apiErrorResponse(string $message, int $statusCode = 400, $e = null): JsonResponse
      {
            $errorCodes = [
                  400 => 'BAD_REQUEST',
                  401 => 'UNAUTHORIZED',
                  403 => 'FORBIDDEN',
                  404 => 'NOT_FOUND',
                  422 => 'VALIDATION_ERROR',
                  500 => 'SERVER_ERROR',
            ];

            $response = [
                  'status' => false,
                  'code' => $errorCodes[$statusCode] ?? 'UNKNOWN_ERROR',
                  'message' => $message,
                  'trace_id' => Str::uuid()->toString(),
            ];

            if (!is_null($e)) {
                  Log::error($message, [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'message' => $e->getMessage()
                  ]);
            }

            return response()->json($response, $statusCode);
      }
}

if (!function_exists('logApiError')) {
      /**
       * Log an API error with standardized format.
       *
       * @param string $message
       * @param int $statusCode
       * @param string $traceId
       * @param mixed $details
       */
      function logApiError(string $message, int $statusCode, $details = null): void
      {
            $logData = [
                  'message' => $message
            ];

            if (!is_null($details)) {
                  $logData['details'] = $details;
            }

            $logLevel = $statusCode >= 500 ? 'error' : 'warning';

            Log::$logLevel('API Error', $logData);
      }
}
