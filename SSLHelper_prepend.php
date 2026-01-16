<?php

/**
 * SSL Helper Prepend Script
 *
 * This script is intended to be used with `auto_prepend_file` to initialize
 * and execute the SSLHelper class. It ensures that PHP is aware of SSL termination
 * happening at a reverse proxy.
 */

// Ensure the main SSLHelper class file is included.
require_once('SSLHelper.php');

// Instantiate the SSLHelper and run its logic.
(new SSLHelper())->run();
