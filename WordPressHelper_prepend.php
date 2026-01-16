<?php

/**
 * WordPress Helper Prepend Script
 *
 * This script is designed to be used with `auto_prepend_file` to initialize
 * and execute the WordPressHelper class.
 *
 * The WordPressHelper class is responsible for defining WordPress constants
 * based on environment variables, which is a common pattern in containerized
 * WordPress deployments.
 */

// Ensure the main WordPressHelper class file is included.
require_once('WordPressHelper.php');

// Instantiate the WordPressHelper and run its initialization logic.
// This will define various WordPress constants from environment variables.
(new WordPressHelper)->run();
