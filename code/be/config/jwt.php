<?php
// Simple JWT Implementation for PHP without external dependencies

class JWT {
    private static $secret = 'QuanLyThuPhi_SuperSecretKey_2026!'; // Ensure matching in dev/prod

    private static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload['exp'] = time() + (24 * 60 * 60); // 1 day expiration
        $payload_json = json_encode($payload);

        $base64UrlHeader = self::base64url_encode($header);
        $base64UrlPayload = self::base64url_encode($payload_json);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);
        $base64UrlSignature = self::base64url_encode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function decode($jwt) {
        $tokenParts = explode('.', $jwt);
        if(count($tokenParts) !== 3) return false;
        
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $base64UrlHeader = self::base64url_encode($header);
        $base64UrlPayload = self::base64url_encode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);
        $base64UrlSignature = self::base64url_encode($signature);

        if ($base64UrlSignature === $signature_provided) {
            $payloadObj = json_decode($payload, true);
            if (isset($payloadObj['exp']) && $payloadObj['exp'] < time()) {
                return false; // Expired
            }
            return $payloadObj;
        }
        return false;
    }

    public static function getBearerToken() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { 
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}

/**
 * API Permission Checker
 */
function api_check_permission($allowed_roles = []) {
    $token = JWT::getBearerToken();
    if (!$token) {
        api_response(401, false, 'Unauthorized - No Token Provided');
    }

    $payload = JWT::decode($token);
    if (!$payload) {
        api_response(401, false, 'Unauthorized - Invalid or Expired Token');
    }

    $user_role = $payload['role_name'] ?? '';

    if (!empty($allowed_roles) && !in_array($user_role, $allowed_roles)) {
        api_response(403, false, 'Forbidden - Do not have permission');
    }

    // Set global current user info like sessions did
    global $api_user;
    $api_user = $payload;
    return $payload;
}

/**
 * Send JSON API response.
 */
function api_response($code, $success, $message) {
    if (isset($GLOBALS['response'])) {
        $GLOBALS['response']->sendJson($code, $success, $message);
    } else {
        $response = new Http\Response();
        $response->sendJson($code, $success, $message);
    }
}

