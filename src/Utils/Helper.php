<?php

namespace IdQueue\IdQueuePackagist\Utils;

use Exception;
use Log;

class Helper
{
    /**
     * Validate a JWT token.
     */
    public static function isJwtValid(string $jwt, string $secret = 'secret'): array
    {
        try {
            // Split the JWT into its components
            $tokenParts = explode('.', $jwt);

            // Ensure the JWT has exactly three parts
            if (count($tokenParts) !== 3) {
                throw new Exception('Invalid JWT format.');
            }

            // Decode the header and payload
            $header = base64_decode($tokenParts[0], true);
            $payload = base64_decode($tokenParts[1], true);

            if ($header === false || $payload === false) {
                throw new Exception('Invalid base64 encoding in JWT.');
            }

            $signatureProvided = $tokenParts[2];

            // Check if the expiration claim exists and is valid
            $expiration = json_decode($payload)->exp ?? null;
            if ($expiration === null) {
                throw new Exception('Expiration claim (exp) missing in JWT.');
            }

            $isTokenExpired = ($expiration - time()) < 0;

            // Build a signature based on the header and payload using the secret
            $base64UrlHeader = self::base64UrlEncode($header);
            $base64UrlPayload = self::base64UrlEncode($payload);
            $signature = hash_hmac('SHA256', $base64UrlHeader.'.'.$base64UrlPayload, $secret, true);
            $base64UrlSignature = self::base64UrlEncode($signature);

            // Verify it matches the signature provided in the JWT
            $isSignatureValid = ($base64UrlSignature === $signatureProvided);

            // Return the result
            $res = [
                'details' => $payload,
            ];

            if ($isTokenExpired || ! $isSignatureValid) {
                $res['check'] = false;
            } else {
                $res['check'] = true;
            }

            return $res;

        } catch (Exception $e) {
            // Log or handle the error gracefully
            Log::error('JWT validation failed: '.$e->getMessage());

            return [
                'check' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Helper method to encode base64 URL-safe
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
