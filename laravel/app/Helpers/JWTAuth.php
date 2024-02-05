<?php

namespace App\Helpers;

class JWTAuth
{

    public static function encodeJWT($payload, $secret) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    public static function decodeJWT($jwt, $secret) {
        if (!$jwt) {
            return false;
        }

        // Split the token
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
    
        // Check the signature
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Compare it to the provided signature
        if ($base64UrlSignature !== $signatureProvided) {
            return false;
        }
        
        return json_decode($payload);
    }

    public static function createTokenJwt($user) {
        $payload = [
            'iss' => "jwt-issuer", // Issuer
            'sub' => $user->id, // Subject
            'iat' => time(), // Issued At
            'exp' => time() + 60*60 // Expiration Time (1 hour)
        ];

        
        $jwtAuth = new self; // Create an instance of the current class
        return $jwtAuth->encodeJWT($payload, env('JWT_SECRET'));
    }
}
