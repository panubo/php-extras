<?php

/**
 * Proxy Helper Prepend Script
 *
 * This script is intended to be used with `auto_prepend_file` to initialize
 * and execute the ProxyHelper class. It adjusts server variables to account for
 * a reverse proxy setup.
 */

// Ensure the main ProxyHelper class file is included.
require_once('ProxyHelper.php');

// Instantiate the ProxyHelper and run its logic.
(new ProxyHelper())->run();
