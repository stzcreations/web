<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

function google_client_id() { return setting('google_client_id', GOOGLE_CLIENT_ID); }
function google_client_secret() { return setting('google_client_secret', GOOGLE_CLIENT_SECRET); }
function google_redirect_uri() { return setting('google_redirect_uri', GOOGLE_REDIRECT_URI); }

function google_auth_url() {
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;
    $params = [
        'client_id' => google_client_id(),
        'redirect_uri' => google_redirect_uri(),
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'access_type' => 'online',
        'prompt' => 'select_account',
    ];
    return GOOGLE_AUTH_URL . '?' . http_build_query($params);
}

function google_exchange_code($code) {
    $post = [
        'code' => $code,
        'client_id' => google_client_id(),
        'client_secret' => google_client_secret(),
        'redirect_uri' => google_redirect_uri(),
        'grant_type' => 'authorization_code',
    ];
    $ch = curl_init(GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true);
}

function google_verify_id_token($idToken) {
    // Fetch JWKS
    $jwksJson = @file_get_contents(GOOGLE_JWKS_URL);
    if (!$jwksJson) return null;
    $jwks = json_decode($jwksJson, true);
    try {
        $keys = JWK::parseKeySet($jwks);
        $decoded = JWT::decode($idToken, $keys);
        $claims = (array)$decoded;
        // Validate issuer
        if (!in_array($claims['iss'] ?? '', ['https://accounts.google.com', 'accounts.google.com'])) return null;
        // Validate audience
        if (($claims['aud'] ?? '') !== google_client_id()) return null;
        // Validate expiry
        if (($claims['exp'] ?? 0) < time()) return null;
        // Validate email_verified
        if (empty($claims['email_verified'])) return null;
        return $claims;
    } catch (Exception $e) {
        return null;
    }
}

function google_get_userinfo($accessToken) {
    $ch = curl_init(GOOGLE_USERINFO_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true);
}
