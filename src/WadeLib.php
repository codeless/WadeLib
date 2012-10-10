<?php

/**
 * Class: WadeLib
 */
class WadeLib {

	/**
	 * Variable: $cli
	 * 
	 * If running/including this script on the
	 * PHP Command line interface, $cli is set to true.
	 * Otherwise (via Browser), $cli will be set to false.
	 */
	public static $cli = null;


	/**
	 * Variable: $mydatafile
	 * 
	 * Holds the path to myself.zip, when it has been
	 * extracted from the current file.
	 */
	public static $mydatafile = null;


	/**
	 * Function: truncateFile
	 *
	 * Truncates the passed file to zero-length.
	 */
	public static function truncateFile($file) {
		self::report('Truncating file ' . $file);
		$handle = fopen($file, 'w+');
		fclose($handle);
	}


	/**
	 * Function: createFile
	 *
	 * Creates a file by running the touch-command.
	 *
	 * Returns:
	 *
	 * 	TRUE - on success
	 * 	FALSE - on failure
	 */
	public static function createFile($file) {
		self::report('Creating file ' . $file);

		$rc = false;

		# If file doesn't exist already
		if (!is_file($file)) {
			# Create it
			$rc = touch($file);
		}

		self::reportReturncode($rc);
		return $rc;
	}


	/**
	 * Function: deleteFile
	 *
	 * Unlinks the passed file.
	 *
	 * Returns:
	 *
	 * 	TRUE - on success
	 * 	FALSE - on failure. Also, if file doesn't exist.
	 */
	public static function deleteFile($file) {
		self::report('Deleting file ' . $file);
		$rc = false;

		if (is_file($file)) {
			$rc = unlink($file);
		}

		self::reportReturncode($rc);
		return $rc;
	}


	/**
	 * Function: changeFilemode
	 *
	 * Attempts to change the mode of the specified file.
	 *
	 * Parameters:
	 *
	 * 	$files - Path to file or folder. Can be either a string
	 * 		or an array holding filepaths.
	 * 	$mode - Mode to change to.
	 * 	$recursive - In case of folder, should all subitems
	 * 		get the new mode too? Can be either true or
	 * 		false. Defaults to false.
	 *
	 * Returns:
	 *
	 * 	TRUE - on success
	 * 	FALSE - on failure
	 */
	public static function changeFilemode($files, $mode, $recursive=false) {
		self::report('Changing filemode of ' . print_r($files, 1));
		$rc = true;

		# If file is no array
		if (!is_array($files)) {
			# Create array
			$files = array($files);
		}

		# Loop through array
		foreach ($files as $f) {
			# If entry is file or folder:
			if (is_file($f) || is_dir($f)) {
				$rc &= chmod($f, $mode);
			}

			# If entry is folder and recursive is set:
			if (is_dir($f) && $recursive) {
				# Collect all subfiles
				$subfiles = glob($f . '/*');

				# Recall this method with subfiles
				$rc &= self::changeFilemode($subfiles, $mode, $recursive);
			}
		}

		self::reportReturncode($rc);
		return $rc;
	}


	public static function report($message) {
		if (!isset(self::$cli)) {
			self::$cli = (php_sapi_name() == 'cli')
				? true
				: false;
		}

		if (self::$cli) {
			echo $message;
		} else {
			echo '<p>',$message,'</p>';
		}

		echo PHP_EOL;
	}


	public static function downloadFile($url, $destination_folder=null) {
		self::report('Downloading file ' . $url . ' to ' . $destination_folder);
		# Compile destination_file:
		$destination_file = $destination_folder . basename($url);

		# Open file handles:
		$extern_handle = fopen($url, 'r');
		$local_handle = fopen($destination_file, 'w+');

		# "Download" extern file:
		if ($extern_handle && $local_handle) {
			while ($line = fgets($extern_handle)) {
				fputs($local_handle, $line);
			}
		}

		# Close handles:
		fclose($extern_handle);
		fclose($local_handle);
	}


	public static function extractZipfile($zipfile, $destination='.') {
		self::report('Extracting zipfile ' . $zipfile . ' to ' . $destination);
		$rc = false;

		# Extract myself.zip:
		if ($zipfile == 'myself.zip') {
			$zipfile = self::$mydatafile;
		}

		# Extract zipfile:
		$zip = new ZipArchive;
		if ($zip->open($zipfile)) {
			$rc = $zip->extractTo($destination);
			$zip->close();
		} else {
			self::report('Could not open zipfile');
		}

		self::reportReturncode($rc);
		return $rc;
	}


	public static function appendToFile($appendage_file, $destination_file) {
		self::report('Appending file ' . $appendage_file . ' to ' . $destination_file);
		$rc = 0;

		# Open file handles:
		$appendage_handle = self::openFile($appendage_file, 'r');
		$destination_handle = self::openFile($destination_file, 'a');

		# Append file:
		if ($appendage_handle && $destination_handle) {
			while ($line = fgets($appendage_handle)) {
				fputs($destination_handle, $line);
			}

			# Close handles
			fclose($appendage_handle);
			fclose($destination_handle);

			$rc = 1;
		}

		self::reportReturncode($rc);
		return $rc;
	}


	private static function openFile($file, $mode) {
		$handle = fopen($file, $mode);

		if (!$handle) {
			syslog(LOG_ERR, 'Cannot open file ' . $file .
				' in mode ' . $mode);
		}

		return $handle;
	}


	public static function redirect() {
	}


	/**
	 * Function: extractAppendedData
	 *
	 * Extracts all the data stored in the current script from
	 * the __COMPILER_HALT_OFFSET__ till the end of the script-file
	 * and stores the binary data into a temporary file.
	 */
	public static function extractAppendedData() {
		self::report('Extracting appended data');

		# Open self:
		$handle = fopen(__FILE__, 'rb');
		if ($handle) {
			# Seek the compiler halt offset:
			if (fseek($handle, __COMPILER_HALT_OFFSET__) == 0) {
				# Create temporary file:
				self::$mydatafile = tempnam(
					sys_get_temp_dir(),
					'wadephp');

				# If tempnam returned false, try it in the local dir:
				if (!self::$mydatafile) {
					self::$mydatafile = 'wadephp';
				}

				self::report('Temporary datafile: ' . self::$mydatafile);
				if (file_put_contents(self::$mydatafile, stream_get_contents($handle))) {
					self::report('Data written to temporary datafile');
				} else {
					self::report('Unable to write data to temporary datafile');
				}
			} else {
				self::report('Could not find halt-offset');
			}
		} else {
			self::report('Could not open myself (' . __FILE__ . ')');
		}
	}


	private static function reportReturncode($rc) {
		self::report('Operation returned ' . $rc);
	}

	private static function startUp() {
		session_start();
		session_regenerate_id();
	}

	public static function cleanUp() {
		# Remove temporary file:
		WadeLib::deleteFile(self::$mydatafile);
	}

	public static function authenticate($username, $password) {
		return 0;
		# If authentication formular has been posted
			# Get username and password

		# If username and password are already set
			# If username and password are valid
				# Continue with next step
		# Else
			# Output authentication formular


		# Username and password already set?
		if ($_SESSION[] && $_SESSION[]) {
		}

		# Output authentication form
		# User provides input
		# User submits form
		# next step is processed
	}

};
