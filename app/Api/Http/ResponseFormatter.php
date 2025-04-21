<?php

namespace App\Api\Http;

class ResponseFormatter
{
    public static function formatSuccess(
        Request $request,
        string $message,
        int $statusCode = 200,
        mixed $data = null
    ): array
    {
        $response = [
            'success' => [
                'status'  => self::getStatusText($statusCode),
                'code'    => $statusCode,
                'message' => $message,
                'details' => [
                    'requested_method' => $request::method(),
                    'documentation'    => 'https://http.cat/status/' . $statusCode
                ]
            ],
            'error'    => false,
            'metadata' => [
                'timestamp' => time(),
                'endpoint'  => $request::path()
            ]
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }

    public static function formatError(
        Request $request,
        string $message,
        int $statusCode = 400
    ): array
    {
        return [
            'success' => false,
            'error' => [
                'status'  => self::getStatusText($statusCode),
                'code'    => $statusCode,
                'message' => $message,
                'details' => [
                    'requested_method' => $request::method(),
                    'documentation'    => 'https://http.cat/status/' . $statusCode
                ]
            ],
            'metadata' => [
                'timestamp' => time(),
                'endpoint'  => $request::path()
            ]
        ];
    }

    private static function getStatusText(int $statusCode): string
    {
        $statusTexts = [
            200 => 'OK',
            201 => 'CREATED',
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED'
        ];

        return $statusTexts[$statusCode] ?? 'UNKNOWN_STATUS';
    }
}