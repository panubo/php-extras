<?php
/**
 * Proxy Helper Class
 *
 * This class adjusts the `$_SERVER` superglobal to reflect the original client's
 * IP address and the correct SSL status. It is intended for use when the
 * application is running behind a trusted reverse proxy.
 */
class ProxyHelper {

	/**
	 * Executes the helper logic.
	 *
	 * It corrects the client's IP address by using the `HTTP_X_REAL_IP` header,
	 * and it corrects the SSL status by using the `HTTP_X_FORWARDED_PROTO` header.
	 *
	 * @return void
	 */
	public function run() {
		// Set IP correctly when being proxied.
		// Overwrites REMOTE_ADDR with the client IP provided by the proxy in HTTP_X_REAL_IP.
		if (isset($_SERVER['HTTP_X_REAL_IP'])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		}

		// Set SSL Status correctly when being proxied.
		// Checks if the request was originally made over HTTPS.
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$_SERVER['HTTPS'] = 'on';
			$_SERVER['REQUEST_SCHEME'] = 'https';
			$_SERVER['protossl'] = 's';
		}
	}

}
