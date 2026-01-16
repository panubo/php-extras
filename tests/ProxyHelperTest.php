<?php

use PHPUnit\Framework\TestCase;

class ProxyHelperTest extends TestCase {

    private $originalServer;

    protected function setUp(): void {
        parent::setUp();
        // Save the original $_SERVER superglobal state
        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void {
        // Restore the original $_SERVER superglobal state
        $_SERVER = $this->originalServer;
        parent::tearDown();
    }

    public function testRemoteAddrIsSetFromXRealIp() {
        // Arrange
        $_SERVER['HTTP_X_REAL_IP'] = '192.168.1.100';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; // Original value

        $helper = new ProxyHelper();

        // Act
        $helper->run();

        // Assert
        $this->assertEquals('192.168.1.100', $_SERVER['REMOTE_ADDR']);
    }

    public function testRemoteAddrIsNotChangedWhenXRealIpIsMissing() {
        // Arrange
        unset($_SERVER['HTTP_X_REAL_IP']);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; // Original value

        $helper = new ProxyHelper();

        // Act
        $helper->run();

        // Assert - should remain unchanged
        $this->assertEquals('127.0.0.1', $_SERVER['REMOTE_ADDR']);
    }

    public function testSslHeadersAreSetWhenForwardedProtoIsHttps() {
        // Arrange
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        unset($_SERVER['HTTPS']);
        unset($_SERVER['REQUEST_SCHEME']);
        unset($_SERVER['protossl']);

        $helper = new ProxyHelper();

        // Act
        $helper->run();

        // Assert
        $this->assertEquals('on', $_SERVER['HTTPS']);
        $this->assertEquals('https', $_SERVER['REQUEST_SCHEME']);
        $this->assertEquals('s', $_SERVER['protossl']);
    }

    public function testSslHeadersAreNotSetWhenForwardedProtoIsNotHttps() {
        // Arrange
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'http';
        $_SERVER['HTTPS'] = 'off'; // Simulate existing non-https setting
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['protossl'] = 'x';

        $helper = new ProxyHelper();

        // Act
        $helper->run();

        // Assert - should remain unchanged
        $this->assertEquals('off', $_SERVER['HTTPS']);
        $this->assertEquals('http', $_SERVER['REQUEST_SCHEME']);
        $this->assertEquals('x', $_SERVER['protossl']);
    }

    public function testSslHeadersAreNotSetWhenForwardedProtoIsMissing() {
        // Arrange
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['REQUEST_SCHEME']);
        unset($_SERVER['protossl']);

        $helper = new ProxyHelper();

        // Act
        $helper->run();

        // Assert - should remain unset
        $this->assertArrayNotHasKey('HTTPS', $_SERVER);
        $this->assertArrayNotHasKey('REQUEST_SCHEME', $_SERVER);
        $this->assertArrayNotHasKey('protossl', $_SERVER);
    }
}
