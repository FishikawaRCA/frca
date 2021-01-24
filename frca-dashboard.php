<?php
	$frca_config = array ();


	/**
	 * initial issue category config and settings
	 *
	 * assume no issues found to begin with
	 */
	$frca_status = array ();
	$frca_status[ $lang['FRCA_CRITICAL'] ]['config']['issueCount']			= 0;
	$frca_status[ $lang['FRCA_CRITICAL'] ]['config']['statusIcon']			= 'minus-circle';
	$frca_status[ $lang['FRCA_CRITICAL'] ]['config']['statusColour']		= 'danger';

	$frca_status[ $lang['FRCA_MODERATE'] ]['config']['issueCount']			= 0;
	$frca_status[ $lang['FRCA_MODERATE'] ]['config']['statusIcon']			= 'exclamation-circle';
	$frca_status[ $lang['FRCA_MODERATE'] ]['config']['statusColour']		= 'warning';

	$frca_status[ $lang['FRCA_MINOR'] ]['config']['issueCount']				= 0;
	$frca_status[ $lang['FRCA_MINOR'] ]['config']['statusIcon'] 			= 'info-circle';
	$frca_status[ $lang['FRCA_MINOR'] ]['config']['statusColour'] 			= 'info';

	$frca_status[ $lang['FRCA_BESTPRACTICE'] ]['config']['issueCount']		= 0;
	$frca_status[ $lang['FRCA_BESTPRACTICE'] ]['config']['statusIcon']		= 'plus-circle';
	$frca_status[ $lang['FRCA_BESTPRACTICE'] ]['config']['statusColour']	= 'frca';

?>
