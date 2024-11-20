<?php

namespace IdQueue\IdQueuePackagist\Utils;

class Helper
{
    /**
     * Validate a JWT token.
     *
     * @param  string  $jwt
     * @param  string  $secret
     * @return array
     */
    public static function isJwtValid($jwt, $secret = 'secret')
    {
        // Split the JWT into its components
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);

        $signatureProvided = $tokenParts[2];

        // Check the expiration time
        $expiration = json_decode($payload)->exp;
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
    }

    /**
     * Helper method to encode base64 URL-safe
     *
     * @param  string  $data
     */
    private static function base64UrlEncode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
