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
		$rc = false;

		# If file doesn't exist already
		if (!is_file($file)) {
			# Create it
			$rc = touch($file);
		}

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
		$rc = false;

		if (is_file($file)) {
			$rc = unlink($file);
		}

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

		return $rc;
	}


	public static function report($message) {
		if (!isset(self::$cli)) {
			self::$cli = (isset($argv)) ? true : false;
		}

		if (self::$cli) {
			echo $message,PHP_EOL;
		}
	}


	public static function downloadFile($url, $destination_folder=null) {
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
		# Extract myself.zip:
		if ($zipfile == 'myself.zip') {
			$zipfile = self::$mydatafile;
		}

		# Extract zipfile:
		$zip = new ZipArchive;
		if ($zip->open($zipfile)) {
			$zip->extractTo($destination);
			$zip->close();
		}
	}


	public static function appendToFile($appendage_file, $destination_file) {
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
		# Open self:
		$handle = fopen(__FILE__, 'rb');
		fseek($handle, __COMPILER_HALT_OFFSET__);

		# Create temporary file:
		self::$mydatafile = tempnam('/tmp', 'wadephp');
		file_put_contents(self::$mydatafile, stream_get_contents($handle));
	}

};
