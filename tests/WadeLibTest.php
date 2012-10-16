<?php

require('vendor/autoload.php');
use \Heartsentwined\FileSystemManager\FileSystemManager;
require('src/WadeLib.php');

class WadeLibTest extends PHPUnit_Framework_TestCase {

	public static $file;


	public function setUp() {
		# Create name for the testfile:
		self::$file = sys_get_temp_dir() . '/wadelib.test';

		# Delete file if it already exists:
		WadeLib::deleteFile(self::$file);
	}


	public function testChangeFilemode() {
		# Create file:
		$rc = WadeLib::createFile(self::$file);
		$this->assertEquals(true, $rc);

		# Change filemode
		$rc = WadeLib::changeFilemode(self::$file, 0666);
		$this->assertEquals(true, $rc);
	}


	/**
	 * Tests the recursively changing of filemodes.
	 */
	public function testRecursiveChmod() {
		# Compile a directory name
		$dir = sys_get_temp_dir() . '/wadelib';

		# Delete directory from earlier runs:
		FileSystemManager::rrmdir($dir);

		# Create a directory
		$rc = mkdir($dir);

		# Create a bunch of subfiles
		$files = array();
		for ($i=0; $i<3; $i++) {
			$files[] = tempnam($dir, 'wl');
		}

		# Switch modes
		$newperm = 0777;
		$rc = WadeLib::changeFilemode($dir, $newperm, true);
		$this->assertEquals(true, $rc);

		# Clear file status cache.
		# Otherwise, during some testruns 
		# problems may occur with invalid
		# file-permissions cached by PHP:
		clearstatcache();

		# Loop through created files to check filemode
		foreach ($files as $f) {
			$rc = fileperms($f);
			echo $f,PHP_EOL;
			printf('Expecting %s, got %s' . PHP_EOL,
				substr(sprintf('%o', $newperm), -4),
				substr(sprintf('%o', $rc), -4));
			$this->assertEquals(
				substr(sprintf('%o', $newperm), -4),
				substr(sprintf('%o', $rc), -4));

			# Unlink files
			unlink($f);
		}
	}


	public function testTruncateFile() {
		$cases = 2;
		do {
			# Create file with some data:
			self::fillfile();

			# Change filemode in case 2:
			if ($cases == 1) {
				WadeLib::changeFilemode(self::$file, 0777);
			}

			# Truncate file:
			WadeLib::truncateFile(self::$file);

			# Check if file got truncated:
			$rc = filesize(self::$file);
			$this->assertEquals($rc, 0);

			--$cases;
		} while($cases);
	}


	public function fillFile() {
		if (!file_put_contents(self::$file, 'Lorem ipsum dolor sit amet.')) {
			die('Couldnt create file!!!');
		}
	}


	public function testExtractAppendedData()
	{
		# Try to extract appended data of a file where
		# no data has been appended:
		$this->assertEquals(
			false,
			WadeLib::extractAppendedData());
	}

	public function testAppendToFile() {
		# Appending two files file1 and file2;
		# result should be the word "App\nend\n".
		# For this test to don't destroy file1, it gets
		# copied to a temporary directory:
		$pseudoFile1 = tempnam(sys_get_temp_dir(), 'wadelib');
		copy('tests/file1', $pseudoFile1);
		WadeLib::appendToFile('tests/file2', $pseudoFile1);

		$this->assertEquals("App\nend\n", file_get_contents($pseudoFile1));
	}

	public function testAppendDataToScriptfile() {
		$pseudoFile1 = tempnam(sys_get_temp_dir(), 'wadelib');
		copy('tests/file1', $pseudoFile1);
		WadeLib::appendDataToScriptfile('tests/file2', $pseudoFile1);

		$this->assertEquals("App\n__halt_compiler();end\n", file_get_contents($pseudoFile1));
	}

	public function testPhingInstallation()
	{
		$tmpDir = sys_get_temp_dir();

		WadeLib::installPhingManually(
			$tmpDir,
			$tmpDir . '/phing');

		# Start Phing with a test-buildfile
		$phingDir = $tmpDir . '/phing';
		$phingInclude = $phingDir . '/classes/';
		ini_set('include_path', $phingInclude);
		require($phingInclude . 'phing/Phing.php');
		Phing::startup(); # Sets up the phing environment
		Phing::start(array('-buildfile', 'tests/build.xml'));

		# Check results of test-buildfile
		$resultFile = __DIR__ . '/wadelib_testbuild';
		$this->assertEquals(PHP_OS, file_get_contents($resultFile));

		# Delete local files:
		unlink($resultFile);

		# Remove extracted Phing library:
		FileSystemManager::rrmdir($phingDir);
	}

};
