<?php

// get the Problem Determination Aide from the supplied problem-code and the frca-pdadata-tmp.json file
// then update/add the problem record to the discovered problems array
function getPDC( $impactGroup, $problemCode ) {

	global $lang, $pdadataARRAY, $problemList;

	// convert the impactGroup character to a textual name that is used within the $problemList array to group discovered isuses
	if ( $impactGroup == 'F' ) {
		$impactGroup = strtoupper( _RES_FRCA );

	// } elseif ( $impactGroup == '5-9 and A-F' ) {
	// 	$impact = strtoupper(CURRENTLY UNUSED);
	// @RussW - 7th-Jan-2021

	} elseif ( $impactGroup == '4' ) {
		$impact = strtoupper( $lang['FRCA_BESTPRACTICE'] );

	} elseif ( $impactGroup == '3' ) {
		$impact = strtoupper( $lang['FRCA_MINOR'] );

	} elseif ( $impactGroup == '2' ) {
		$impact = strtoupper( $lang['FRCA_MODERATE'] );

	} elseif ( $impactGroup == '1' ) {
		$impact = strtoupper( $lang['FRCA_CRITICAL'] );

	} else {
		$impact = strtoupper( $lang['FRCA_U'] );

	}


	// check that the problem-code exists in the PDA, if not raise '0000' - 'Unexpected Fishikawa Error' problem-code
	if ( array_key_exists($problemCode, $pdadataARRAY) ) {
		$problemCode		= $problemCode;

	} else {
		$impact				= strtoupper( $lang['FRCA_CRITICAL'] );
		$problemError		= $problemCode;
		$problemCode		= '0000';
	}


	// add the problem-code and associated details to the problem array
	$problemList[$impact][$problemCode] = $pdadataARRAY[$problemCode];
	$problemList[$impact][$problemCode]['problemcode'] = $problemCode;


	if ( isset($problemError) ) {
		$problemList[$impact][$problemCode]['severity'] = $lang['FRCA_RISKUC'];
		$problemList[$impact][$problemCode]['symptoms'][1] = $problemList[$impact][$problemCode]['symptoms'][1] .' for PDC: '. $problemError;
	}


} // end getPDC
