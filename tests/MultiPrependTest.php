<?php

use PHPUnit\Framework\TestCase;

/**
 * @runInSeparateProcess
 */
class MultiPrependTest extends TestCase {

    private $tempFiles = [];
    private $originalMultiPrependEnv;

    protected function setUp(): void {
        parent::setUp();
        $this->originalMultiPrependEnv = getenv('MULTI_PREPEND');
    }

    protected function tearDown(): void {
        // Clean up temporary files
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];

        // Restore original MULTI_PREPEND environment variable
        if ($this->originalMultiPrependEnv !== false) {
            putenv("MULTI_PREPEND={$this->originalMultiPrependEnv}");
        } else {
            putenv('MULTI_PREPEND'); // Unset if it wasn't set originally
        }

        parent::tearDown();
    }

    private function createTempFile(string $content): string {
        $filePath = sys_get_temp_dir() . '/' . uniqid('multi_prepend_test_') . '.php';
        file_put_contents($filePath, $content);
        $this->tempFiles[] = $filePath;
        return $filePath;
    }

    public function testMultipleFilesAreIncluded() {
        // Arrange
        $file1Content = '<?php define("MULTI_PREPEND_TEST_CONST_1", "value1"); ?>';
        $file2Content = '<?php $GLOBALS["multi_prepend_test_var_2"] = "value2"; ?>';

        $filePath1 = $this->createTempFile($file1Content);
        $filePath2 = $this->createTempFile($file2Content);

        putenv("MULTI_PREPEND={$filePath1},{$filePath2}");

        // Act
        // Directly including the Multi_prepend.php script will execute its logic
        require __DIR__ . '/../Multi_prepend.php';

        // Assert
        $this->assertTrue(defined('MULTI_PREPEND_TEST_CONST_1'));
        $this->assertEquals('value1', MULTI_PREPEND_TEST_CONST_1);
        $this->assertArrayHasKey('multi_prepend_test_var_2', $GLOBALS);
        $this->assertEquals('value2', $GLOBALS['multi_prepend_test_var_2']);
    }

    public function testNoFilesAreIncludedWhenEnvVarIsEmpty() {
        // Arrange
        $file1Content = '<?php define("MULTI_PREPEND_TEST_CONST_3", "value3"); ?>';
        $filePath1 = $this->createTempFile($file1Content);

        putenv("MULTI_PREPEND="); // Empty environment variable

        // Act
        require __DIR__ . '/../Multi_prepend.php';

        // Assert
        $this->assertFalse(defined('MULTI_PREPEND_TEST_CONST_3'));
    }

    public function testFilesWithWhitespaceInEnvVarAreIncluded() {
        // Arrange
        $file1Content = '<?php define("MULTI_PREPEND_TEST_CONST_4", "value4"); ?>';
        $file2Content = '<?php $GLOBALS["multi_prepend_test_var_5"] = "value5"; ?>';

        $filePath1 = $this->createTempFile($file1Content);
        $filePath2 = $this->createTempFile($file2Content);

        putenv("MULTI_PREPEND= {$filePath1} , {$filePath2} "); // With whitespace

        // Act
        require __DIR__ . '/../Multi_prepend.php';

        // Assert
        $this->assertTrue(defined('MULTI_PREPEND_TEST_CONST_4'));
        $this->assertEquals('value4', MULTI_PREPEND_TEST_CONST_4);
        $this->assertArrayHasKey('multi_prepend_test_var_5', $GLOBALS);
        $this->assertEquals('value5', $GLOBALS['multi_prepend_test_var_5']);
    }
}
