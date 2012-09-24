<?php

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
		# Create a directory
		$dir = sys_get_temp_dir() . '/wadelib';
		$rc = @mkdir($dir);

		# Create a bunch of subfiles
		$files = array();
		for ($i=0; $i<3; $i++) {
			$files[] = tempnam($dir, 'wl');
		}

		# Switch modes
		$newperm = 0777;
		$rc = WadeLib::changeFilemode($dir, $newperm, true);
		$this->assertEquals(true, $rc);

		# Loop through created files to check filemode
		foreach ($files as $f) {
			$rc = fileperms($f);
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

};
