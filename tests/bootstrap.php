<?php
// tests/bootstrap.php

// Manually require the class files for testing.
// This ensures they are available to PHPUnit test cases.
require_once __DIR__ . '/../SSLHelper.php';
require_once __DIR__ . '/../ProxyHelper.php';
require_once __DIR__ . '/../WordPressHelper.php';

// Composer autoloader for PHPUnit and other dependencies
require_once __DIR__ . '/../vendor/autoload.php';
