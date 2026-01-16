<?php
/**
 * WordPress Helper Class
 *
 * This class facilitates the configuration of WordPress by defining core constants
 * from environment variables. This is particularly useful in containerized environments
 * (like Docker) where configuration is often managed via environment variables.
 */
class WordPressHelper
{
    /**
     * @var array $env_keys A list of WordPress constants that can be defined from
     *                      corresponding environment variables.
     */
    private $env_keys = [
        "DB_HOST",
        "DB_NAME",
        "DB_USER",
        "DB_PASSWORD",
        "DB_CHARSET",
        "WP_DEBUG",
        "WP_HOME",
        "WP_SITEURL",
        "AUTH_KEY",
        "SECURE_AUTH_KEY",
        "LOGGED_IN_KEY",
        "NONCE_KEY",
        "AUTH_SALT",
        "SECURE_AUTH_SALT",
        "LOGGED_IN_SALT",
        "NONCE_SALT",
    ];

    /**
     * Executes the helper logic.
     * Iterates through the predefined list of environment variable keys and
     * attempts to define corresponding WordPress constants.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->env_keys as $key) {
            $this->define_from_env($key);
        }
    }

    /**
     * Defines a WordPress constant from an environment variable.
     *
     * @param string $key The name of the environment variable and the constant to define.
     * @return void
     */
    private function define_from_env($key)
    {
        $value = getenv($key);
        // Check if the environment variable exists and has a value.
        // `false` is returned by getenv() if the variable is not set.
        if ($value !== false && !defined($key)) {
            // Define the constant only if it's not already defined.
            // This prevents warnings/errors if WordPress or another plugin
            // has already defined the constant.
            define($key, $value);
        }
    }
}
