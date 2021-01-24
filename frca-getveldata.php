<?php
@session_start();
//$_SESSION['frca'];

	/**
	 * test for any locally stored vel-data before downloading remotely to avoid
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

//unset( $_SESSION['veldata'] );

	if ( !isset($_SESSION['veldata']) and !file_exists('frca-veldata-tmp.json') ) {


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$veldevMSG .= 'no local veldata available, trying cURL remote json feed<br />';
		} // end devloper message


		$velcURL			= 'https://extensions.joomla.org/index.php?option=com_vel&format=json';
		$ch					= curl_init( $velcURL );  // init cURL
		$velcURLOPT			= array ( CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
									  CURLOPT_TIMEOUT => 5,
									  CURLOPT_CONNECTTIMEOUT => 5,
									  CURLOPT_RETURNTRANSFER => true,
									  CURLOPT_SSL_VERIFYPEER => false,							 
									  CURLOPT_HTTPHEADER => array('Content-type: application/json'),
									);
		curl_setopt_array( $ch, $velcURLOPT );

		$velcURLJSON		= curl_exec( $ch ); // get json result string


			// if we have acquired the remote json vel-data, try several methods to make it available
			// locally, else post an error and bail out as frca will have nothing to do or to display

		    // check the HTTP status code of the request
			$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ( $resultStatus == '200' ) {

				$veljsonARRAY	= json_decode($velcURLJSON, true);  // decode json in to an array
				$veldataARRAY	= $veljsonARRAY['data']['items'];   // only load the item data in to the useable data array


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$veldevMSG .= 'got veldata through cURL from remote json feed<br />';
				} // end devloper message


				// if for some reason the session isn't started, start it now so
				// we can attempt store the vel array in local session storage
				//@session_start();

				// decode and convert the json vel-data to an array and load it in to local session storage
				$_SESSION['veldata']	= $veljsonARRAY['data']['items'];


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$veldevMSG .= 'attempted to write veldata to $_SESSION';

					if ( isset($_SESSION['veldata']) ) {
						@$veldevMSG .= '<br />success - wrote veldata to $_SESSION';
					}

				} // end developer message


				// belt n braces, check if the session array is available, if not, make a last ditch attempt to make the
				// vel-data available locally through a local file (we keep this data in json format for speed and size reasons)
				if ( !isset($_SESSION['veldata']) ) {

					$fp	= fopen( 'frca-veldata-tmp.json', 'w' );
					fwrite( $fp, $velcURLJSON );
					fclose( $fp );


					// DEVELOPER MODE INFO
					if ( defined('_FRCA_DEV') ) {
						@$veldevMSG .= 'unable to write veldata to $_SESSION, attempted to write to file';
						if ( !file_exists('frca-veldata-tmp.json') ) {
							@$veldevMSG .= 'unable to write veldata to file<br />';
						} else {
							@$veldevMSG .= 'wrote veldata to file<br />';
						}
					} // end devloper message


				}

			} else {
				echo 'VELCONNECT ERROR';


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$veldevMSG .= 'could not connect through cURL to veldata remote json feed and no $_SESSION data available';
				} // end devloper message


			}


	} elseif ( isset($_SESSION['veldata']) ) {

		// session vel-data is available so use that
		$veldataARRAY	= $_SESSION['veldata'];


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$veldevMSG .= 'got veldata from $_SESSION';
		} // end devloper message


	} elseif ( file_exists('frca-veldata-tmp.json') ) {

		// file vel-data is available so use that
		$get_veldata	= file_get_contents( 'frca-veldata-tmp.json' );
		$veljsonARRAY	= json_decode( $get_veldata, true );
		$veldataARRAY   = $veljsonARRAY['data']['items'];  // load the file vel-data in to an array


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$veldevMSG .= 'got veldata from local file';
		} // end devloper message


	} else {
		echo 'VEL DATA ERROR';
		// TODO: disable if in error


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$veldevMSG .= 'veldata not available from any source or invalid';
		} // end devloper message


	}
?>



<?php
///echo '<pre>';
///echo 'IS VEL SESSION DATA<hr>';
//var_dump( $veldataARRAY );
///var_dump( $_SESSION['veldata'] );
///echo '</pre>';
?>
