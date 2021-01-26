<?php


/**
 * getDirectory FUNCTION TO RECURSIVELY READ THROUGH LOOKING FOR PERMISSIONS
 *
 * this is used to read the directory structure and return a list of folders with 'elevated'
 * mode-sets ( -7- or --7 ) ignoring the first position as defaults folders are normally 755.
 * $dirCount is applied when the folder list is excessive to reduce unnecessary processing
 * on really sites with 00's or 000's of badly configured folder modes. Limited to displaying
 * the first 10 only.
 */
if (@$showElevated == '1') {

	$dirCount = 0;

	function getDirectory($path = '.', $level = 0)
	{
		global $elevated, $dirCount;

		// directories to ignore when listing output. Many hosts
		$ignore = array('.', '..');

		// open the directory to the handle $dh
		if (!$dh = @opendir($path)) {
			// Bernard: if a folder is NOT readable, without this check we get endless loop
			echo '<div class="alert" style="padding:25px;"><span class="alert-text" style="font-size:x-large;">' . $lang['FRCA_DIR_UNREADABLE'] . ': <b>' . $path . '</b></span></div>';
			return FALSE;
		}


		// loop through the directory
		while (false !== ($file = readdir($dh))) {

			// check that this file is not to be ignored
			if (!in_array($file, $ignore)) {

				if ($dirCount < '10') { // 10 or more folder will cancel the processing

					// its a directory, so we need to keep reading down...
					if (is_dir("$path/$file")) {

						$dirName = $path . '/' . $file;
						$dirMode = substr(sprintf('%o', fileperms($dirName)), -3, 3);

						// looking for --7 or -7- or -77 (default folder permissions are usually 755)
						if (substr($dirMode, 1, 1) == '7' or substr($dirMode, 2, 1) == '7') {
							$elevated['' . str_replace('./', '', $dirName) . '']['mode'] = $dirMode;

							if (is_writable($dirName)) {
								$elevated['' . str_replace('./', '', $dirName) . '']['writable'] = $lang['FRCA_Y'];
							} else {  // custom ownership or setUiD/GiD in-effect
								$elevated['' . str_replace('./', '', $dirName) . '']['writable'] = $lang['FRCA_N'];
							}
							$dirCount++;
						}

						// re-call this same function but on a new directory.
						getDirectory("$path/$file", ($level + 1));
					}
				}
			}
		}
		// Close the directory handle
		closedir($dh);
	}

	// Fixed Warning: Illegal string offset 'mode' on line 1476
	// Warning: Illegal string offset 'writable' on line 1477 - @PhilD 20-Sep-2012
	if (isset($dirCount) == '0') {
		$elevated['None'] = $lang['FRCA_NONE'];
		$elevated['None']['mode'] = '-';
		$elevated['None']['writable'] = '-';
	}

	// now call the function to read from the selected folder ( '.' current location of FPA script )
	getDirectory('.');
	ksort($elevated);
} // end showElevated


?>
