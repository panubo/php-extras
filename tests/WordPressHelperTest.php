<?php

use PHPUnit\Framework\TestCase;

/**
 * @runInSeparateProcess
 */
class WordPressHelperTest extends TestCase {

    private $originalEnv = [];
    private $definedConstants = [];

    protected function setUp(): void {
        parent::setUp();
        // Save original environment variables for relevant keys
        $envKeys = ["DB_HOST", "DB_NAME", "WP_DEBUG"]; // Example keys
        foreach ($envKeys as $key) {
            $this->originalEnv[$key] = getenv($key);
            // Clear relevant env vars for a clean slate for each test
            putenv("{$key}"); // Unset the environment variable
        }

        // Save already defined constants
        foreach ($this->getConstantsToTest() as $const) {
            if (defined($const)) {
                $this->definedConstants[$const] = constant($const);
            }
        }
    }

    protected function tearDown(): void {
        // Restore original environment variables
        foreach ($this->originalEnv as $key => $value) {
            putenv("{$key}={$value}");
        }
        // Unset any constants defined by the test itself
        foreach ($this->getConstantsToTest() as $const) {
            if (defined($const) && !isset($this->definedConstants[$const])) {
                // Constants cannot be undefined in PHP, so we just ensure
                // that subsequent tests clean up their own defined constants
                // and don't rely on previous tests' definitions.
                // For PHPUnit, each test runs in isolation regarding globals
                // if process isolation is enabled, but for constants, it's global.
                // We mainly ensure we don't pollute the next test's assumption.
            }
        }
        parent::tearDown();
    }

    private function getConstantsToTest(): array {
        // This list should match the $env_keys in WordPressHelper
        return ["DB_HOST", "DB_NAME", "DB_USER", "DB_PASSWORD",
            "DB_CHARSET", "WP_DEBUG", "WP_HOME", "WP_SITEURL", "AUTH_KEY",
            "SECURE_AUTH_KEY", "LOGGED_IN_KEY", "NONCE_KEY", "AUTH_SALT",
            "SECURE_AUTH_SALT", "LOGGED_IN_SALT", "NONCE_SALT"];
    }

    public function testConstantsAreDefinedFromEnvironmentVariables() {
        // Arrange
        putenv('DB_HOST=my_test_host');
        putenv('DB_NAME=wordpress_db_new');
        putenv('WP_DEBUG=false');

        $helper = new WordPressHelper();

        // Act
        $helper->run();

        // Assert
        $this->assertTrue(defined('DB_HOST'));
        $this->assertEquals('my_test_host', DB_HOST);
        $this->assertTrue(defined('DB_NAME'));
        $this->assertEquals('wordpress_db_new', DB_NAME);
        $this->assertTrue(defined('WP_DEBUG'));
        $this->assertEquals('false', WP_DEBUG);
    }

    public function testConstantsAreNotDefinedWhenEnvironmentVariablesAreMissing() {
        // Arrange - ensure env vars are NOT set
        putenv('DB_HOST'); // Unset DB_HOST env var
        putenv('DB_NAME');
        putenv('WP_DEBUG');

        // Capture initial state of DB_HOST (expected to be 'localhost' due to environment)
        $initialDbHost = defined('DB_HOST') ? DB_HOST : null;
        $initialDbName = defined('DB_NAME') ? DB_NAME : null;
        $initialWpDebug = defined('WP_DEBUG') ? WP_DEBUG : null;

        $helper = new WordPressHelper();

        // Act
        $helper->run();

        // Assert that constants retain their initial state (helper should not define them if env var is missing)
        // If DB_HOST was 'localhost' initially, it should remain 'localhost'.
        // If other constants were not defined, they should remain undefined.
        if ($initialDbHost !== null) {
            $this->assertTrue(defined('DB_HOST'));
            $this->assertEquals($initialDbHost, DB_HOST);
        } else {
            $this->assertFalse(defined('DB_HOST'));
        }

        if ($initialDbName !== null) {
            $this->assertTrue(defined('DB_NAME'));
            $this->assertEquals($initialDbName, DB_NAME);
        } else {
            $this->assertFalse(defined('DB_NAME'));
        }

        if ($initialWpDebug !== null) {
            $this->assertTrue(defined('WP_DEBUG'));
            $this->assertEquals($initialWpDebug, WP_DEBUG);
        } else {
            $this->assertFalse(defined('WP_DEBUG'));
        }
    }

    public function testExistingConstantsAreNotOverwritten() {
        // Arrange
        // Capture initial state of DB_HOST (expected to be 'localhost' or some other pre-defined value)
        $initialDbHost = defined('DB_HOST') ? DB_HOST : null;
        if ($initialDbHost === null) {
             // If DB_HOST is not defined at all initially, define it for the purpose of this test.
             // This might happen if the global environment does not define DB_HOST.
             define('DB_HOST', 'original_host_from_test');
             $initialDbHost = 'original_host_from_test';
        }

        // Set an environment variable for DB_HOST (this value should be ignored by the helper)
        putenv('DB_HOST=new_value_from_env');

        $helper = new WordPressHelper();

        // Act
        $helper->run();

        // Assert that the constant's initial value is preserved.
        // The helper should not overwrite an already defined constant.
        $this->assertTrue(defined('DB_HOST'));
        $this->assertEquals($initialDbHost, DB_HOST, "DB_HOST constant should not have been overwritten by helper.");
    }
}
