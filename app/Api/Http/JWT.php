<?php

namespace App\Api\Http;

use Random\RandomException;

/**
 * JSON Web Token (JWT) implementation for authentication and data exchange.
 *
 * This class provides methods to generate, verify, and refresh JWT tokens
 * using HMAC SHA-256 algorithm.
 */
class JWT
{
    /** @var string|null The secret key used for signing tokens. */
    private static $secretKey = null;

    /**
     * Generates a new JWT token with the provided data.
     *
     * @param array $data Additional claims to include in the token payload
     * @return string The generated JWT token
     * @throws RandomException If token generation fails due to random bytes issue
     */
    public static function generateToken(array $data = []): string
    {
        $decodedHeader = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);

        try {
            $decodedPayload = json_encode(
                array_merge([
                    'sub' => $data['id'] ?? null,
                    'iat' => time(),
                    'exp' => time() + (60 * 60),
                    'jti' => bin2hex(random_bytes(16)),
                    'iss' => 'phast-auth',
                    'aud' => 'phast-auth-client'
                ], $data)
            );
        } catch (RandomException $e) {
            throw new RandomException('Token generation failed', 0, $e);
        }

        $encodedHeader = self::base64UrlEncode($decodedHeader);
        $encodedPayload = self::base64UrlEncode($decodedPayload);
        $encodedSignature = self::signature($encodedHeader, $encodedPayload);

        return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
    }

    /**
     * Verifies the integrity and validity of a JWT token.
     *
     * @param string $token The JWT token to verify
     * @return array|bool The token payload if valid, false otherwise
     */
    public static function verifyToken(string $token): array|bool
    {
        $tokenPartials = explode('.', $token);

        if (count($tokenPartials) != 3) {
            return false;
        }

        [$encodedHeader, $encodedPayload, $signature] = $tokenPartials;

        if ($signature !== self::signature($encodedHeader, $encodedPayload)) {
            return false;
        }

        $decodedPayload = json_decode(self::base64UrlDecode($encodedPayload), true);

        if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
            return false;
        }

        return $decodedPayload;
    }

    /**
     * Refreshes an expired JWT token by generating a new one with extended expiration.
     * Preserves all original claims except timestamps and JTI.
     *
     * @param string $oldToken The existing JWT token to refresh
     * @return string|bool The new JWT token if refresh is successful, false if invalid signature
     * @throws RandomException If token generation fails
     */
    public static function refreshToken(string $oldToken): string|bool
    {
        $tokenParts = explode('.', $oldToken);

        if (count($tokenParts) !== 3) {
            return false;
        }

        [$encodedHeader, $encodedPayload, $oldSignature] = $tokenParts;

        if ($oldSignature !== self::signature($encodedHeader, $encodedPayload)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($encodedPayload), true);

        if (!is_array($payload)) {
            return false;
        }

        unset($payload['exp'], $payload['iat'], $payload['jti']);

        return self::generateToken($payload);
    }

    /**
     * Generates the signature part of the JWT token.
     *
     * @param string $encodedHeader Base64Url encoded header
     * @param string $encodedPayload Base64Url encoded payload
     * @return string The generated signature
     */
    private static function signature(
        string $encodedHeader,
        string $encodedPayload
    ): string
    {
        return self::base64UrlEncode(
            hash_hmac(
                algo: 'sha256',
                data: $encodedHeader . '.' . $encodedPayload,
                key: self::loadSecretKey(),
                binary: true
            )
        );
    }

    /**
     * Encodes data using URL-safe Base64 encoding.
     *
     * @param string $data The data to encode
     * @return string The encoded data
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodes data from URL-safe Base64 encoding.
     *
     * @param string $data The data to decode
     * @return string The decoded data
     */
    private static function base64UrlDecode(string $data): string
    {
        $padding = strlen($data) % 4;

        $padding !== 0 && $data .= str_repeat('=', 4 - $padding);

        $data = strtr($data, '-_', '+/');

        return base64_decode($data);
    }

    /**
     * Loads the secret key from configuration file.
     *
     * @return string The secret key
     */
    private static function loadSecretKey(): string
    {
        if (self::$secretKey === null) {
            self::$secretKey = require __DIR__ . '/../../../config/jwt.php';
        }

        return self::$secretKey['secret_key'];
    }
}