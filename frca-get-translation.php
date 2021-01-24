<?php
@session_start();
//$_SESSION['frca'];

	/**
	 * test for any locally stored lang-data before downloading remotely to avoid
	 * hitting the remote resources everytime frca is run
	 *
	 * 1st run :
	 * - if no session or file
	 * -- download json data
	 * -- unencode json and add to array
	 * -- dump the array to a session variable
	 * -- if the session fails or empty
	 * --- try write it to a file
	 * --- if 'write' error or empty
	 * ---- report an error
	 *
	 * subsequent run(s) :
	 * - check for session or file data
	 * -- if no session or file, load remotely again
	 * - elseif session found, use that
	 * - elseif no session, look for, and use file
	 * - else report an error
	 *
	 *
	 */

//unset( $_SESSION['frca'] );

	if ( !isset($_SESSION['translation']) and !file_exists('frca-translation-tmp.ini') ) {


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$langdevMSG .= 'no local translation available, trying cURL remote service<br />';
		} // end devloper message


		$randCacheBuster	= mt_rand();  // attempt to avoid GitHub CDN/Browser Caching with a randon number added to the request
		$langcURL			= 'https://hotmangoteam.github.io/Fishikawatest/translation/frca-'. $browserLanguage .'.ini?'. $randCacheBuster;
		$ch					= curl_init( $langcURL );  // init cURL
		$langcURLOPT		= array ( CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
									  CURLOPT_TIMEOUT => 5,
									  CURLOPT_CONNECTTIMEOUT => 5,
									  CURLOPT_RETURNTRANSFER => true,
									  CURLOPT_HTTPHEADER => array('Content-type: text/html'),
									);
		curl_setopt_array( $ch, $langcURLOPT );

		$langcURLJSON		= curl_exec( $ch ); // get json result string


			// if we have acquired the remote json lang-data, try several methods to make it available
			// locally, else post an error and bail out as frca will have nothing to do or to display

		    // check the HTTP status code of the request
			$resultStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			if ( $resultStatus == '200' ) {

				$updatelang = parse_ini_string ( $langcURLJSON );


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$langdevMSG .= 'got translation through cURL from remote service<br />';
				} // end devloper message


				// if for some reason the session isn't started, start it now so
				// we can attempt store the lang array in local session storage
				//@session_start();

				// add the ini file and load it in to local session storage
				$_SESSION['translation']	= $langcURLJSON;


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$langdevMSG .= 'attempted to write translation to $_SESSION';

					if ( isset($_SESSION['translation']) ) {
						@$langdevMSG .= '<br />success - wrote language to $_SESSION';
					}

				} // end developer message


				// belt n braces, check if the session array is available, if not, make a last ditch attempt to make the
				// translation available locally through a local file
				if ( !isset($_SESSION['translation']) ) {

					$fp	= fopen( 'frca-'. $browserLanguage .'-tmp.ini', 'w' );
					fwrite( $fp, $langcURLJSON );
					fclose( $fp );
					// TODO: drop an error if can't write


					// DEVELOPER MODE INFO
					if ( defined('_FRCA_DEV') ) {
						@$langdevMSG .= 'unable to write translation to $_SESSION, attempted to write to file';
						if ( !file_exists('frca-translation-tmp.ini') ) {
							@$langdevMSG .= 'unable to write translation to file<br />';
						} else {
							@$langdevMSG .= 'wrote translation to file<br />';
						}
					} // end devloper message


				}

			} else {

				// post a problemList entry manually as we dont have remote access to the users language file
				$problemList['MINOR']['0004'] = array(
					'heading'		=> 'Fishikawa was unable to access a desired remote translation resources',
					'description'	=> 'FRCA was unable to download the desired language resources for your language',
					'category' 		=> 'Fishikawa',
					'severity'		=> '4',
					'symptoms'		=> array(
						'0'	=> 'Language is set to the default (Engish)'
					),
					'actions'		=> array(
						'0'	=> 'try resetting FRCA back to defaults',
						'1'	=> 'continue using FRCA in English'
					),
					'problemcode'	=> '0004'
				);

				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$langdevMSG .= '<strong>could not connect through cURL to translation remote service or '. $browserLanguage .' not available and no $_SESSION data available</strong>';
				} // end devloper message


			}


	} elseif ( isset($_SESSION['translation']) ) {

		// session translation is available so use that
		$updatelang = parse_ini_string ( $_SESSION['translation'] );


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$langdevMSG .= 'got translation from $_SESSION';
		} // end devloper message


	} elseif ( file_exists('frca-translation-tmp.ini') ) {

		// file tranlsation is available so use that
		$updatelang = parse_ini_file( 'frca-translation-tmp.ini' );

		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$langdevMSG .= 'got translation from local file';
		} // end devloper message


	} else {
		//echo 'LANG DATA ERROR';
		// TODO: disable if in error


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$langdevMSG .= 'translation not available from any source or invalid';
		} // end devloper message


	}
?>



<?php
//echo '<pre>';
//var_dump( $langdataARRAY );
//var_dump( $_SESSION['langdata'] );
//echo $_SESSION['translation'];
//echo '</pre>';
?>
