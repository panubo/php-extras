<?php

// Set SSL Status correctly when being proxied
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_SCHEME'] = 'https';
    $_SERVER['protossl'] = 's';
}
