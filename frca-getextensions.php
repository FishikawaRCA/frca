<?php
/**
 * FIND AND ESTABLISH INSTALLED EXTENSIONS
 *
 * this function recurively looks for installed Components, Modules, Plugins and Templates
 * it only reads the .xml file to determine installation status and info, some extensions
 * do not have an associated .xml file and wont be displayed (normally core extensions)
 *
 * modified version of the function for the recirsive folder permisisons previously
 */
///if ($instance['instanceFOUND'] == _FPA_Y) { // fix for IIS *shrug*

	// this is a little funky and passes the extension array name bt variable reference
	// (&$arrname refers to each seperate array, which is called at the end) this was
	// depreciated at 5.3 and I couldn't find an alternative, so the fix to a PHP Warning
	// is to simply re-assign the $arrname back to itself inside the function, so it is
	// no-longer a reference
	function getDetails($path, &$arrname, $loc, $level = 0)
	{
		global $component, $module, $plugin, $template, $library;

		// fix for PHP5.3 pass-variable-by-reference depreciation
		$arrname = $arrname;
		// Directories & files to ignore when listing output.
		$ignore = array('.', '..', 'index.htm', 'index.html', '.DS_Store', 'none.xml', 'metadata.xml', 'default.xml', 'form.xml', 'contact.xml', 'edit.xml', 'blog.xml');

		// open the directory to the handle $dh
		$dh = @opendir($path);

		// loop through the directory
		while (false !== ($file = @readdir($dh))) {

			// check that this file is not to be ignored
			if (!in_array($file, $ignore)) {

				// its a directory, so we need to keep reading down...
				if (is_dir("$path/$file")) {

					getDetails("$path/$file", $arrname, $loc, ($level + 1));
					// Re-call this same function but on a new directory.
					// this is what makes function recursive.

				} else {

					if ($path == 'components') {
						$cDir = substr(strrchr($path . '/' . $file, '/'), 1);
					} else {
						$cDir = $path . '/' . $file;
					}

					if (preg_match("/\.xml/i", $file)) { // if filename matches .xml in the name

						$content = file_get_contents($cDir);

						if (preg_match('#<(extension|install|mosinstall)#', $content, $isValidFile)) {
							// $arrname[$loc][$cDir] = '';

							$arrname[$loc][$cDir]['author']         = '-';
							$arrname[$loc][$cDir]['authorUrl']      = '-';
							$arrname[$loc][$cDir]['version']        = '-';
							$arrname[$loc][$cDir]['creationDate']   = '-';
							$arrname[$loc][$cDir]['type']           = '-';


							if (preg_match('#<name>(.*)</name>#', $content, $name)) {
								$arrname[$loc][$cDir]['name']   = strip_tags(substr($name[1], 0, 35));
							} else {
								$arrname[$loc][$cDir]['name']   = _FPA_U . ' (' . $cDir . ') ';
							}


							if (preg_match('#<author>(.*)</author>#', $content, $author)) {
								$arrname[$loc][$cDir]['author'] = strip_tags(substr($author[1], 0, 25));

								if (
									$author[1] == 'Joomla! Project'
									or strtolower($name[1]) == 'joomla admin'
									or strtolower($name[1]) == 'rhuk_milkyway'
									or strtolower($name[1]) == 'ja_purity'
									or strtolower($name[1]) == 'khepri'
									or strtolower($name[1]) == 'bluestork'
									or strtolower($name[1]) == 'atomic'
									or strtolower($name[1]) == 'hathor'
									or strtolower($name[1]) == 'protostar'
									or strtolower($name[1]) == 'isis'
									or strtolower($name[1]) == 'beez5'
									or strtolower($name[1]) == 'beez_20'
									or strtolower($name[1]) == 'cassiopeia'
									or strtolower($name[1]) == 'atum'
									or strtolower(substr($name[1], 0, 4)) == 'beez'
								) {
									$arrname[$loc][$cDir]['type'] = $lang['RCA_EXT_CORE'];
								} else {
									$arrname[$loc][$cDir]['type'] = $lang['RCA_EXT_3PD'];
								}
							} else {
								$arrname[$loc][$cDir]['author']     = '-';
								$arrname[$loc][$cDir]['type']       = '-';
							}

							if (preg_match('#<version>(.*)</version>#', $content, $version)) {
								$arrname[$loc][$cDir]['version'] = substr($version[1], 0, 13);
							} else {
								$arrname[$loc][$path . '/' . $file]['version'] = '-';
							}

							if (preg_match('#<creationDate>(.*)</creationDate>#', $content, $creationDate)) {
								$arrname[$loc][$cDir]['creationDate'] = $creationDate[1];
							} else {
								$arrname[$loc][$cDir]['creationDate'] = '-';
							}

							if (preg_match('#<authorUrl>(.*)</authorUrl>#', $content, $authorUrl)) {
								$arrname[$loc][$cDir]['authorUrl'] = str_replace(array('http://', 'https://'), '', $authorUrl[1]);
							} else {
								$arrname[$loc][$cDir]['authorUrl'] = '-';
							}
						} //isValidFile

					}
				}
			}
		} // while

		@closedir($dh);
	}


	// use the same function (above) to search for each extension type and load the results into it's associated array
	@getDetails('components', $component, 'SITE');
	@getDetails('administrator/components', $component, 'ADMIN');

	@getDetails('modules', $module, 'SITE');
	@getDetails('administrator/modules', $module, 'ADMIN');

	// cater for Joomla! 1.0 differences
	if (@$instance['cmsRELEASE'] == '1.0') {
		@getDetails('mambots', $plugin, 'SITE');
	} else {
		@getDetails('plugins', $plugin, 'SITE');
	}

	@getDetails('templates', $template, 'SITE');
	@getDetails('administrator/templates', $template, 'ADMIN');
	@getDetails('libraries', $library, 'SITE');
/// } // end if instanceFOUND

?>
