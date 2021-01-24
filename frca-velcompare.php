<?php
// merge discovered/installed extension arrays (from getDetails function)


// @frostmakk 24.01.2021
// Many sites don't have libraries or site components, resulting in errors when the expected arrays are empty.
// This is just a primitive workaround. Feel free to improve the code.
if (!is_array(@$component['ADMIN'])) {$component['ADMIN'] = array(' ',' ');}
if (!is_array(@$component['SITE'])) {$component['SITE'] = array(' ',' ');}
if (!is_array(@$module['SITE'])) {$module['SITE'] = array(' ',' ');}
if (!is_array(@$module['ADMIN'])) {$module['ADMIN'] = array(' ',' ');}
if (!is_array(@$plugin['SITE'])) {$plugin['SITE'] = array(' ',' ');}
if (!is_array(@$library['SITE'])) {$library['SITE'] = array(' ',' ');}
if (!is_array(@$template['SITE'])) {$template['SITE'] = array(' ',' ');}
if (!is_array(@$template['ADMIN'])) {$template['ADMIN'] = array(' ',' ');}


                                                  
$velResult = array_merge($component['ADMIN'], $component['SITE'], $module['SITE'], $module['ADMIN'], $plugin['SITE'], $library['SITE'], $template['SITE'], $template['ADMIN']);

// loop through each discovered/installed extension
foreach ( $velResult AS $extensionkey => $extension ) {

	// loop through each vel entry and compare to the current installed extension data
	foreach ( $veldataARRAY AS $velkey => $velCOMPARE ) {

		// start match validation criteria
		$comparisonVeracity	= 0;
		$compareTested		= 0;


		/**
		 * HACK: try and fix known miss & false-positive candidates
		 *
		 * caused by similar names, developer lazy/poor naming conventions, different xml/manifest
		 * name to public/commom name or name used in VEL report title
		 *
		 */

		// Akeeba to Akeeba Backup
		if ( stristr( $extension['name'], 'akeeba' ) !== FALSE AND strlen($extension['name']) == '6' ) {
			$extension['name'] = 'Akeeba Backup';
		}

		// cleanup inconsistances in the extensions Author URL to make matching easier
		$shortUrl	= str_replace( array( 'http://', 'https://','http://www.', 'https://www.', 'www.' ), '', $extension['authorUrl'] );


		/**
		 * attempt to build some level of veracity/accuracy for the installed item vs the vel item
		 *
		 * due the nature of the vel data and inconsistent developer naming conventions we need to try and test multiple
		 * conditions in an attempt to clarify and confirm that the listed vel item is actually the same item as the
		 * installed extension (this still isn't 100% accurate as the vel data currently (Jan-2021) has three ages of data,
		 * all with varying degrees of data formats and the developers haven't been consistent in their naming conventions
		 * over the years which can cause some positive-misses and false-positives)
		 *
		 * initally we check that the installed extensions name can be found in several vel fields, then the author name & authorUrl can
		 * be matched in multiple locations  and finally check the installed version against the vulnerable and/or fix/patch version
		 *
		 * Match Veracity / Accuracy
		 * each matching test criteria increases $comparisonVeracity +1
		 * only some (important misses) unmatch test criteria reduce $comarisonVeracity -1
		 * - resulting in an accuracy %'age score (displayed as a progress bar on the problemEntry)
		 *
		 */

		// get a rough list of potential matches
		// is the extension name found anywhere in the VEL title element?
		if ( stristr( $velCOMPARE['title'], $extension['name'] ) !== FALSE ) {

			$comparisonVeracity++;
			$compareTested++;

			// now try to improve the match accuracy with some comparisons
			// VEL titles are generally formatted with "<name>, <effected-versions+text>, <vulnerability-type>"
			// using trim, strip_tags & html_entity_encode to cleanup crap the developers add
			$velTitlePieces = explode( ',', trim(strip_tags(html_entity_decode($velCOMPARE['title'], 3))) );

			// clean up the default VEL title to be more poinient/relevant as a title
			$velCOMPARE['title'] = $velTitlePieces[0];

			// if the exploit-type is appended to the end of the title (new in 2020 format)
			if ( isset($velTitlePieces[2]) ) {

				// cleanup inconsistant formatting (some have parenthesis, some do not)
				// then reformat the piece and append it to the title again
				$removeCharacters		= array( "(", ")" );
				$velCOMPARE['title'] 	.= '&nbsp;('. trim( str_replace($removeCharacters, '', $velTitlePieces[2])) .')';

			}

			// look for an exact match of the extension name as the FIRST substring in the velTitlePieces[0]
			// this is an attempt to overcome poor naming conventions between extension name and public name like "Akeeba/Akeeba Backup", "Akeeba CMS Update", "Admin Tools By Akeeba"
			if ( $extension['name'] == substr($velTitlePieces[0], 0, strlen($extension['name'])) ) {

				$comparisonVeracity++;
				$compareTested++;

			} else {

				// no match, remove from any further checks oterwise we get too many false-positives with partial name matches
				unset($velCOMPARE);
				continue;

			}

			// part of the extension name is in the xml file path
			if ( stristr($extensionkey, $extension['name']) !== FALSE ) {

				$comparisonVeracity++;
				$compareTested++;

			}

			// part of the extension name found in the description
			if ( stristr(@$velCOMPARE['description'], $extension['name']) !== FALSE ) {

				$comparisonVeracity++;
				$compareTested++;

			}

			// exact match for vel extension name found in the description
			if ( stristr(@$velCOMPARE['description'], $velTitlePieces[0]) !== FALSE ) {

				$comparisonVeracity++;
				$compareTested++;

			} else {
				$comparisonVeracity--;
			}

			// short AuthorURL found in vel update notice
			if ( stristr(@$velCOMPARE['update_notice'], $shortUrl) !== FALSE ) {

				$comparisonVeracity++;
				$compareTested++;

			}

			// extension name found in vel update notice
			if ( stristr(@$velCOMPARE['update_notice'], $extension['name']) !== FALSE ) {

				$comparisonVeracity++;
				$compareTested++;

			}

			// short AuthorURL found in jed URL
			if ( array_key_exists( 'jed', $velCOMPARE ) ) {

				if ( stristr($velCOMPARE['jed'], $shortUrl) !== FALSE ) {

					$comparisonVeracity++;
					$compareTested++;

				}

			}

			// if extended extension data found in vel entry
			if ( array_key_exists('install_data', $velCOMPARE) ) {

				// extension name found in extended install data name
				if ( stristr(@$velCOMPARE['install_data']['name'], $extension['name']) !== FALSE ) {

					$comparisonVeracity++;
					$compareTested++;

				} else {
					$comparisonVeracity--;
				}

				// extension name found in extended install data description
				if ( stristr(@$velCOMPARE['install_data']['description'], $extension['name']) !== FALSE ) {

					$comparisonVeracity++;
					$compareTested++;

				}

				// extension AuthorURL found in extended install data author URL
				if ( stristr(@$velCOMPARE['install_data']['author'], $extension['author']) !== FALSE ) {

					$comparisonVeracity++;
					$compareTested++;

				}else {
					$comparisonVeracity--;
				}

				// extension short AuthorURL found in extended install data author URL
				if ( stristr(@$velCOMPARE['install_data']['authorUrl'], $shortUrl) !== FALSE) {

					$comparisonVeracity++;
					$compareTested++;

				}else {
					$comparisonVeracity--;
				}

				// extension name found in extended install data author URL
				if ( stristr(@$velCOMPARE['install_data']['authorUrl'], $extension['name']) !== FALSE ) {

					$comparisonVeracity++;
					$compareTested++;

				}

			} // extended instal_data validation


			// calculate a 'Match Accuracy' value from the test veracity value and number of tests performed
			$velAccuracy = number_format( ($comparisonVeracity / $compareTested) * 100, 0 );


			/**
			 * start to map a vel problem entry to the existing $problemList array format
			 *
			 * as the installed extension has been determined to match a vel entry, assign it to the CRITICAL impact-group
			 * as at the very least it is most likely out-of-date, if it has a "risk_level" use that (low = severity 3,
			 * medium = severity 2, high = severity 1), else look to the vel title piece[2] element for the type of exploit,
			 * if an exploit type exists (XSS, SQL Injection, Directory Traversal) assign the "risk_level/severity" as "1"
			 * otherwise assign the entry severity "4"
			 */
			if ( isset($velCOMPARE['risk_level']) ) {

				if ( $velCOMPARE['risk_level'] == 'high' ) {

					$velSeverity	= 1;

				} elseif ( $velCOMPARE['risk_level'] == 'medium' ) {

					$velSeverity	= 2;

				} elseif ( $velCOMPARE['risk_level'] == 'low' OR $velCOMPARE['risk_level'] == 'small' ) {

					$velSeverity	= 3;
				}

			} elseif ( isset($velTitlePieces[2]) and (stristr($velTitlePieces[2], 'XSS') !== FALSE or stristr($velTitlePieces[2], 'Cross Site') !== FALSE or stristr($velTitlePieces[2], 'SQL') !== FALSE or stristr($velTitlePieces[2], 'Traversal') !== FALSE) ) {

				// if no risk_level, look for exploit type in the velTitlePieces[2]
				$velSeverity	= 1;

			} else {

				//finally bailout and default to a severity of '4'
				$velSeverity	= '4';

			} // end risk_level to severity mapping


			/**
			 * reduce the list of potential vulnerable entries to only those where the versions fit the vel entry criteria
			 *
			 * start by checking if the installed extension version is lower than either the patch_version or the vulnerable_version
			 *
			 * - the patch_version may be empty if vulnerability has not been fixed yet, if so lets still keep it on the list, if the
			 *   extension version is <= to the listed vulnerable_version as the installed extension is almost certainly out-of-date
			 *   and potentially vulnerable anyway
			 *
			 * ignore the vel entry if neither criteria can be met
			 *
			 */
			if ( $extension['version'] < @$velCOMPARE['patch_version'] or $extension['version'] <= @$velCOMPARE['vulnerable_version']) {

				/**
				 * apply some small fixes and formating to clarify some entry details
				 *
				 */

				// if patch_version is empty, vel assumes it has not been fixed, so we should as well
				// - using the vel statusText appears to be extremely unreliable and not always updated to 'resolved'
				//   if a fix is available, but the patch_version seems to be more regularly updated
				if ( isset($velCOMPARE['patch_version']) and !empty($velCOMPARE['patch_version']) ) {
					$velCOMPARE['patch_version'] = '-';
				}

				// vel defines an empty start_version as meaning all versions prior to fix or now contain the vulnerability
				if ( empty($velCOMPARE['start_version']) ) {
					$velCOMPARE['start_version'] = $lang['FRCA_VEL_ALLPREV'];
				}

				// let the user know the 'extension type', if it's available
				if ( isset($velCOMPARE['type']) ) {
					$extensionType = $lang['FRCA_TYPE'] .': <b>'. $velCOMPARE['type'] .'</b><br>';

				} elseif ( isset($velCOMPARE['install_data']['type']) ) {
					$extensionType = $lang['FRCA_TYPE'] .': <b>'. $velCOMPARE['install_data']['type'] .'</b><br>';
				}

				// fixup various potential empty elements
				if ( empty($extension['cve_id']) ) {
					$extension['cve_id'] = '-';
				}

				if ( empty($velCOMPARE['cvss30_base_score']) ) {
					$velCOMPARE['cvss30_base_score'] = '-';
				}


				/**
				 * add a CRITICAL entry to the $problemList if matched and meets validation criteria (above)
				 * re-use the default problemList array structure and simply map the vel-data elements to existing element fields
				 *
				 */
				$problemList['CRITICAL']['V'. $velCOMPARE['id']]			= array(
					'heading'		=> trim($velCOMPARE['title']),
					'description'	=> htmlspecialchars_decode($extensionType . $velCOMPARE['description']),
					'category' 		=> 'vel',
					'severity'		=> $velSeverity,
					'symptoms'		=> array(
						'0'	=> $extension['version'],
						'1'	=> $velCOMPARE['vulnerable_version'],
						'2'	=> $velCOMPARE['patch_version'],
						'3'	=> $velCOMPARE['start_version']
					),
					'causes'		=> array(
						'0'	=> '',
						'1'	=> '',
						'2'	=> '',
						'3'	=> ''
					),
					'effects'		=> array(
						'0'	=> '',
						'1'	=> '',
						'2'	=> '',
						'3'	=> ''
					),
					'actions'		=> array(
						'0'	=> $lang['FRCA_UPDATETO'] .': '. $velCOMPARE['patch_version'],
						'1'	=> $lang['FRCA_VEL_CVECVS30'] .': '. @$extension['cve_id'] .' / '. @$velCOMPARE['cvss30_base_score'],
						'2'	=> $lang['FRCA_VEL_AUTHSITE'] .':'.$extension['authorUrl'],
						'3'	=> ''
					),
					'velstatus'		=> $velCOMPARE['statusText'],
					'velaccuracy'	=> $velAccuracy,
					'problemcode'	=> 'V'. $velCOMPARE['id']
				);

			} // end version citeria test and add entry

		} // end vel matching routines

	} // end foreach velCOMPARE

} // end foreach $extension list
?>
