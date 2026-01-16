<?php
/**
 * SSL Helper Class
 *
 * This class adjusts the `$_SERVER` superglobal to reflect that the original request
 * was made over HTTPS, based on the `HTTP_X_FORWARDED_PROTO` header. This is useful
 * when the application is behind a reverse proxy that terminates SSL.
 */
class SSLHelper {

	/**
	 * Executes the helper logic.
	 *
	 * Checks for the `HTTP_X_FORWARDED_PROTO` header and, if it's 'https',
	 * modifies the `$_SERVER` superglobal to make the application aware of the
	 * original SSL connection.
	 *
	 * @return void
	 */
	public function run() {
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$_SERVER['HTTPS'] = 'on';
			$_SERVER['REQUEST_SCHEME'] = 'https';
			$_SERVER['protossl'] = 's';
		}
	}

}
