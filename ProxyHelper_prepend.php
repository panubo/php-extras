<?php

/**
 * Proxy Helper Prepend Script
 *
 * This script is intended to be used with `auto_prepend_file` when the PHP application
 * is running behind a reverse proxy. It adjusts the `$_SERVER` superglobal
 * to reflect the original client's IP address and the correct SSL status.
 *
 * WARNING: This script trusts `HTTP_X_REAL_IP` and `HTTP_X_FORWARDED_PROTO` headers
 * unconditionally. Ensure that your web server or proxy is configured to set these
 * headers reliably and that requests not originating from a trusted proxy
 * do not reach this application directly, as this could lead to IP spoofing
 * or incorrect SSL status detection.
 */

// Set IP correctly when being proxied:
// Overwrites REMOTE_ADDR with the client IP provided by the proxy in HTTP_X_REAL_IP.
// This assumes the proxy is configured to send the correct client IP in this header.
$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];

// Set SSL Status correctly when being proxied:
// Checks if the request was originally made over HTTPS, as indicated by the proxy
// in HTTP_X_FORWARDED_PROTO. If so, it sets various $_SERVER variables
// to reflect an HTTPS connection, which many applications use for internal logic.
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_SCHEME'] = 'https';
    $_SERVER['protossl'] = 's'; // Some applications might use this non-standard key
}
