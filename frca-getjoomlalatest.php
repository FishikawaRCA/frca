<?php
@session_start();
//$_SESSION['latestfrca'];
//unset ($_SESSION['latestfrca']);
//define('_FRCA_DEV', true);

/**
 * test for any locally stored joomlaversion-data before downloading remotely to avoid
 * hitting the remote resources everytime frca is run
 *
 * 1st run :
 * - if no session or file
 * -- download XML data
 * -- add latest version to $latestJVER variable
 * -- dump the variable to a session variable
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
function doJOOMLALIVE($thisJVER) {

	global $_SESSION, $latestJVER, $joomladevMSG, $error;

	if ( !isset($_SESSION['latestjoomla']) and !file_exists('frca-latestjoomla-tmp.xml') ) {


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$joomladevMSG .= 'no local latestjoomla available, trying simpleXML remote xml file<br />';
		} // end devloper message


		libxml_use_internal_errors(true);
		$jupdateURL  = 'https://update.joomla.org/core/list.xml';
		$jupdateXML  = @simpleXML_load_file( $jupdateURL, 'SimpleXMLElement', LIBXML_NOCDATA );


		if ($jupdateXML !==  false) {

			$latestJATTR  = $jupdateXML->extension[count($jupdateXML->extension) - 1];
			$latestJVER   = $latestJATTR->attributes()->version->__toString();
			//$thisJVER     = '3.9.18';


			// DEVELOPER MODE INFO
			if ( defined('_FRCA_DEV') ) {
				@$joomladevMSG .= 'got latestjoomla through simpleXML from remote xml file<br />';
			} // end devloper message


			// if for some reason the session isn't started, start it now so
			// we can attempt store the latestjoomla in local session storage
			//@session_start();

			// add $latestJVER variable to local session storage
			$_SESSION['latestjoomla']	= $latestJVER;


			// DEVELOPER MODE INFO
			if ( defined('_FRCA_DEV') ) {

				@$joomladevMSG .= 'attempted to write latestjoomla to $_SESSION';

				if ( isset($_SESSION['latestjoomla']) ) {
					@$joomladevMSG .= '<br />success - wrote latestjoomla to $_SESSION';
				}

			} // end developer message


			// belt n braces, check if the session array is available, if not, make a last ditch attempt to make the
			// latestfrca available locally through a local file (we keep this data in json format for speed and size reasons)
			if ( !isset($_SESSION['latestjoomla']) ) {

				$fp	= fopen( 'frca-latestjoomla-tmp.xml', 'w' );
				fwrite( $fp, $jupdateXML );
				fclose( $fp );
				// TODO: drop an error if can't write


				// DEVELOPER MODE INFO
				if ( defined('_FRCA_DEV') ) {
					@$joomladevMSG .= 'unable to write latestjoomla to $_SESSION, attempted to write to file';
					if ( !file_exists('frca-latestjoomla-tmp.xml') ) {
						@$joomladevMSG .= 'unable to write latestjoomla to file<br />';
					} else {
						@$joomladevMSG .= 'success - wrote latestjoomla to file<br />';
					}
				} // end devloper message


			}


			/*
			if (version_compare($thisJVER, $latestJVER) < 0) {
				$joomlaVersionCheckStatus   = 'warning';
				$joomlaVersionCheckIcon     = 'exclamation';
				$joomlaVersionCheckMessage  = _VER_CHECK_ATOLD . ' (' . $thisJVER . ')';
				$joomlaVersionCheckDownload = 'https://downloads.joomla.org/';
			} elseif (version_compare($thisJVER, $latestJVER) > 0) {
				$joomlaVersionCheckStatus   = 'info';
				$joomlaVersionCheckIcon     = 'question';
				$joomlaVersionCheckMessage  = _VER_CHECK_ATDEV . ' (' . $thisJVER . ')';
				$joomlaVersionCheckDownload = '';
			} else {
				$joomlaVersionCheckStatus   = 'success';
				$joomlaVersionCheckIcon     = 'check';
				$joomlaVersionCheckMessage  = _VER_CHECK_ATCUR . ' (' . $thisJVER . ')';
				$joomlaVersionCheckDownload = '';
			}

			echo '<div class="w-100 p-2 bg-white small border border-' . $joomlaVersionCheckStatus . ' text-' . $joomlaVersionCheckStatus . '">';
			echo '<i class="fas fa-' . $joomlaVersionCheckIcon . '-circle fa-fw"></i>&nbsp;';
			echo 'Joomla! ' . $joomlaVersionCheckMessage;

			if (!empty($joomlaVersionCheckDownload)) {
				echo '<a class="mt-1 py-1 badge badge-' . $joomlaVersionCheckStatus . ' d-block w-75 mx-auto d-print-none" data-html2canvas-ignore="true" href="' . $joomlaVersionCheckDownload . '" rel="noreferrer noopener" target="_blank">Download Latest Joomla! (v' . $latestJVER . ')</a>';
			}
			echo '</div>';
			*/

		} else {


			// DEVELOPER MODE INFO
			if ( defined('_FRCA_DEV') ) {
				foreach(libxml_get_errors() as $xmlError) {
					@$joomladevMSG .= $xmlError->message .'<br />';
				}
				@$joomladevMSG .= 'could not connect through simpleXML to latestjoomla remote xml file and no $_SESSION data available';
			} // end devloper message


		}


	} elseif ( isset($_SESSION['latestjoomla']) ) {

		// session latestjoomla is available so use that
		$latestJVER	= $_SESSION['latestjoomla'];


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$joomladevMSG .= 'got latestjoomla from $_SESSION';
		} // end devloper message


	} elseif ( file_exists('frca-latestjoomla-tmp.xml') ) {

		// file latestjoomla is available so use that
		$jupdateXML		= @simpleXML_load_file( 'frca-latestjoomla-tmp.xml', 'SimpleXMLElement', LIBXML_NOCDATA );
		$latestJATTR	= $jupdateXML->extension[count($jupdateXML->extension) - 1];
		$latestJVER		= $latestJATTR->attributes()->version->__toString();


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$joomladevMSG .= 'got latestjoomla from local file';
		} // end devloper message


	} else {
		echo 'latestjoomla DATA ERROR';
		// TODO: disable if in error


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$joomladevMSG .= 'latestjoomla not available from any source or invalid';
		} // end devloper message


	}


} // function doJOOMLACHECK
?>
