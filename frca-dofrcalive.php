<?php
@session_start();
//$_SESSION['latestfrca'];
//unset ($_SESSION['latestfrca']);
//define('_FRCA_DEV', true);

	/**
	 * test for any locally stored frcaversion-data before downloading remotely to avoid
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

function doFRCALIVE() {

	global $_SESSION, $frcaversionARRAY, $frcadevMSG;

	if ( !isset($_SESSION['latestfrca']) and !file_exists('frca-latestfrca-tmp.json') ) {


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$frcadevMSG .= 'no local latestfrca available, trying cURL remote json feed<br />';
		} // end devloper message


		// TODO: change to FRCA URL
		$randCacheBuster	= mt_rand();  // attempt to avoid GitHub CDN/Browser Caching with a randon number added to the request
		$frcacURL     		= 'https://api.github.com/repos/ForumPostAssistant/FPA/releases/latest?'. $randCacheBuster;  // frca github json latest release URL
		$ch          		= curl_init($frcacURL);  // init cURL
		$frcacURLOPT  		= array( CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
									CURLOPT_TIMEOUT => 5,
									CURLOPT_CONNECTTIMEOUT => 5,
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_SSL_VERIFYPEER => false,
									CURLOPT_HTTPHEADER => array('Content-type: application/json'),
								);
		curl_setopt_array( $ch, $frcacURLOPT );

		$frcacURLJSON		 = curl_exec( $ch ); // get json result string


		// if we have acquired the remote json pda-data, try several methods to make it available
		// locally, else post an error and bail out as frca will have nothing to do or to display

		// check the HTTP status code of the request
		$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ( $resultStatus == '200' ) {

			$frcajsonARRAY	= json_decode($frcacURLJSON, true);  // decode json in to an array
			$frcaversionARRAY	= $frcajsonARRAY;                    // only load the version data in to the useable data array


			// DEVELOPER MODE INFO
			if ( defined('_FRCA_DEV') ) {
				@$frcadevMSG .= 'got latestfrca through cURL from remote json feed<br />';
			} // end devloper message


			// if for some reason the session isn't started, start it now so
			// we can attempt store the frcadata array in local session storage
			//@session_start();

			// decode and convert the json pda-data to an array and load it in to local session storage
			$_SESSION['latestfrca']	= $frcaversionARRAY;


			// DEVELOPER MODE INFO
			if ( defined('_FRCA_DEV') ) {

				@$frcadevMSG .= 'attempted to write latestfrca to $_SESSION';

				if ( isset($_SESSION['latestfrca']) ) {
					@$frcadevMSG .= '<br />success - wrote latestfrca to $_SESSION';
				}

			} // end developer message



			// belt n braces, check if the session array is available, if not, make a last ditch attempt to make the
			// latestfrca available locally through a local file (we keep this data in json format for speed and size reasons)
			if ( !isset($_SESSION['latestfrca']) ) {

				$fp	= fopen( 'frca-latestfrca-tmp.json', 'w' );
				fwrite( $fp, $frcacURLJSON );
				fclose( $fp );
				// TODO: drop an error if can't write


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$frcadevMSG .= 'unable to write latestfrca to $_SESSION, attempted to write to file';
					if ( !file_exists('frca-latestfrca-tmp.json') ) {
						@$frcadevMSG .= 'unable to write latestfrca to file<br />';
					} else {
						@$frcadevMSG .= 'success - wrote latestfrca to file<br />';
					}
				} // end devloper message


			}

		} else {
			echo 'FRCAVERCONNECT ERROR';


			// DEVELOPER MODE INFO
			if ( defined('_FRCA_DEV') ) {
				@$frcadevMSG .= 'could not connect through cURL to latestfrca remote json feed and no $_SESSION data available';
			} // end devloper message


		}


	} elseif ( isset($_SESSION['latestfrca']) ) {

		// session latestfrca is available so use that
		$frcaversionARRAY	= $_SESSION['latestfrca'];


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$frcadevMSG .= 'got latestfrca from $_SESSION';
		} // end devloper message


	} elseif ( file_exists('frca-latestfrca-tmp.json') ) {

		// file latestfrca is available so use that
		$get_latestfrca		= file_get_contents( 'frca-latestfrca-tmp.json' );
		$frcajsonARRAY		= json_decode( $get_latestfrca, true );
		$frcaversionARRAY   = $frcajsonARRAY;  // load the file latestfrca in to an array

		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$frcadevMSG .= 'got latestfrca from local file';
		} // end devloper message


	} else {
		echo 'latestfrca DATA ERROR';
		// TODO: disable if in error


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$frcadevMSG .= 'latestfrca not available from any source or invalid';
		} // end devloper message


	}


} // function
?>



<?php

//doFRCALIVE();

//echo '<pre>';
//var_dump( $frcaversionARRAY );
//var_dump( $_SESSION['latestfrca'] );
//echo '</pre>';
?>
