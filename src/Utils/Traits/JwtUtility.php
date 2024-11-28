<?php

namespace IdQueue\IdQueuePackagist\Utils\Traits;

use Exception;
use Log;

trait JwtUtility
{
    /**
     * Validate a JWT token.
     */
    public static function isJwtValid(string $jwt, string $secret = 'secret'): array
    {
        try {
            $tokenParts = explode('.', $jwt);

            if (count($tokenParts) !== 3) {
                throw new Exception('Invalid JWT format.');
            }

            [$encodedHeader, $encodedPayload, $providedSignature] = $tokenParts;

            $header = json_decode(base64_decode($encodedHeader), true);
            $payload = json_decode(base64_decode($encodedPayload), true);

            if (! $header || ! $payload) {
                throw new Exception('Invalid base64 encoding in JWT.');
            }

            $expiration = $payload['exp'] ?? null;
            if (! $expiration) {
                throw new Exception('Expiration claim (exp) missing in JWT.');
            }

            $isTokenExpired = ($expiration - time()) < 0;

            $validSignature = self::base64UrlEncode(
                hash_hmac('SHA256', "$encodedHeader.$encodedPayload", $secret, true)
            );

            $isSignatureValid = ($providedSignature === $validSignature);

            return [
                'check' => ! $isTokenExpired && $isSignatureValid,
                'details' => $payload,
            ];
        } catch (Exception $e) {
            Log::error('JWT validation failed: '.$e->getMessage());

            return [
                'check' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Base64 URL-safe encoding.
     */
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function generate_jwt($payload, $secret = 'secret'): string
    {
        $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
        $headers_encoded = self::base64UrlEncode(json_encode($headers));

        $payload_encoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = self::base64UrlEncode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }
}
