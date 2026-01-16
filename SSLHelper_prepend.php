<?php

/**
 * SSL Helper Prepend Script
 *
 * This script is intended to be used with `auto_prepend_file` when the PHP application
 * is running behind a reverse proxy that handles SSL termination.
 * It adjusts the `$_SERVER` superglobal to reflect that the original request
 * was made over HTTPS, based on the `HTTP_X_FORWARDED_PROTO` header.
 *
 * This helps applications correctly generate HTTPS URLs and behave as if
 * they are directly serving SSL traffic.
 *
 * WARNING: This script trusts the `HTTP_X_FORWARDED_PROTO` header unconditionally.
 * Ensure that your web server or proxy is configured to set this header reliably
 * and that requests not originating from a trusted proxy do not reach this application
 * directly, as this could lead to incorrect SSL status detection.
 */

// Set SSL Status correctly when being proxied:
// Checks if the request was originally made over HTTPS, as indicated by the proxy
// in HTTP_X_FORWARDED_PROTO. If this header is present and its value is 'https',
// it sets various $_SERVER variables (HTTPS, REQUEST_SCHEME, protossl)
// to reflect an HTTPS connection. Many PHP applications and frameworks rely on these
// variables to determine if the current connection is secure.
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_SCHEME'] = 'https';
    $_SERVER['protossl'] = 's'; // Some legacy applications or custom setups might use this non-standard key
}
