<?php

require('src/WadeLib.php');

class WadeLibTest extends PHPUnit_Framework_TestCase {

	public function testChangeFilemode() {
		# Name of the testfile:
		$file = '/tmp/wadelib.test';

		# Delete file if it already exists:
		WadeLib::deleteFile($file);

		# Create file:
		$rc = WadeLib::createFile($file);
		$this->assertEquals(true, $rc);

		# Change filemode
		$rc = WadeLib::changeFilemode($file, 0666);
		$this->assertEquals(true, $rc);
	}

};
