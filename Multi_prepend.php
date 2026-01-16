<?php

/**
 * Multi-Prepend Helper
 *
 * This script allows for the inclusion of multiple PHP files using the auto_prepend_file directive.
 * It reads a comma-separated list of file paths from the 'MULTI_PREPEND' environment variable
 * and requires each specified file.
 *
 * This is useful when you need to prepend more than one file, as `auto_prepend_file` typically
 * only accepts a single file path.
 */
foreach (explode(',', getenv('MULTI_PREPEND')) as $value) {
    $filePath = trim($value);
    if (!empty($filePath)) {
        // Trim whitespace from the file path and require it.
        // If a file does not exist, a fatal error will occur, as expected with require_once.
        require_once($filePath);
    }
}
