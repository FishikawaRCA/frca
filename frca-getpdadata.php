<?php
@session_start();
//$_SESSION['frca'];

	/**
	 * test for any locally stored pda-data before downloading remotely to avoid
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

	if ( !isset($_SESSION['pdadata']) and !file_exists('frca-pdadata-tmp.json') ) {


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$pdadevMSG .= 'no local pdadata available, trying cURL remote json feed<br />';
		} // end devloper message


		$randCacheBuster	= mt_rand();  // attempt to avoid GitHub CDN/Browser Caching with a randon number added to the request
		$pdacURL			= 'https://hotmangoteam.github.io/Fishikawatest/pdadata/frca-pdadata.json?'.$randCacheBuster;  // frca pda-data json feed URL + github cachebuster
		$ch					= curl_init( $pdacURL );  // init cURL
		$pdacURLOPT			= array ( CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
									  CURLOPT_TIMEOUT => 30,
									  CURLOPT_CONNECTTIMEOUT => 10,
									  CURLOPT_RETURNTRANSFER => true,
									  CURLOPT_SSL_VERIFYPEER => false,
									  CURLOPT_HTTPHEADER => array('Content-type: application/json'),
									);
		curl_setopt_array( $ch, $pdacURLOPT );

		$pdacURLJSON		= curl_exec( $ch ); // get json result string


			// if we have acquired the remote json pda-data, try several methods to make it available
			// locally, else post an error and bail out as frca will have nothing to do or to display

		    // check the HTTP status code of the request
			$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ( $resultStatus == '200' ) {

				$pdajsonARRAY	= json_decode($pdacURLJSON, true);  // decode json in to an array
				$pdadataARRAY	= $pdajsonARRAY['dataset']['pdc'];  // only load the pdc data in to the useable data array


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$pdadevMSG .= 'got pdadata through cURL from remote json feed<br />';
				} // end devloper message


				// if for some reason the session isn't started, start it now so
				// we can attempt store the pda array in local session storage
				//@session_start();

				// decode and convert the json pda-data to an array and load it in to local session storage
				$_SESSION['pdadata']	= $pdadataARRAY;


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$pdadevMSG .= 'attempted to write pdadata to $_SESSION';

					if ( isset($_SESSION['pdadata']) ) {
						@$pdadevMSG .= '<br />success - wrote padadata to $_SESSION';
					}

				} // end developer message


				// belt n braces, check if the session array is available, if not, make a last ditch attempt to make the
				// pda-data available locally through a local file (we keep this data in json format for speed and size reasons)
				if ( !isset($_SESSION['pdadata']) ) {

					$fp	= fopen( 'frca-pdadata-tmp.json', 'w' );
					fwrite( $fp, $pdacURLJSON );
					fclose( $fp );
					// TODO: drop an error if can't write


					// DEVELOPER MODE INFO
					if ( defined('_FRCA_DEV') ) {
						@$pdadevMSG .= 'unable to write pdadata to $_SESSION, attempted to write to file';
						if ( !file_exists('frca-pdadata-tmp.json') ) {
							@$pdadevMSG .= 'unable to write pdadata to file<br />';
						} else {
							@$pdadevMSG .= 'wrote pdadata to file<br />';
						}
					} // end devloper message


				}

			} else {
				echo 'PDACONNECT ERROR';


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$pdadevMSG .= 'could not connect through cURL to pdadata remote json feed and no $_SESSION data available';
				} // end devloper message


			}


	} elseif ( isset($_SESSION['pdadata']) ) {

		// session pda-data is available so use that
		$pdadataARRAY	= $_SESSION['pdadata'];


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$pdadevMSG .= 'got pdadata from $_SESSION';
		} // end devloper message


	} elseif ( file_exists('frca-pdadata-tmp.json') ) {

		// file pda-data is available so use that
		$get_pdadata	= file_get_contents( 'frca-pdadata-tmp.json' );
		$pdajsonARRAY	= json_decode( $get_pdadata, true );
		$pdadataARRAY   = $pdajsonARRAY['dataset']['pdc'];  // load the file pda-data in to an array

		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$pdadevMSG .= 'got pdadata from local file';
		} // end devloper message


	} else {
		echo 'PDA DATA ERROR';
		// TODO: disable if in error


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$pdadevMSG .= 'pdadata not available from any source or invalid';
		} // end devloper message


	}
?>



<?php
///echo '<pre>';
//var_dump( $pdadataARRAY );
///var_dump( $_SESSION['pdadata'] );
///echo '</pre>';
?>
