<?php
/**
 * @version 0.0.42
 * @package Joomla!
 * @subpackage Fishikawa
 * @category  Root Cause Analysis
 * @author @RussW
 * @author @Frostmakk
 * @author @mandville
 * @copyright Fishikawa Project. 2020-present, GNU GPLv3 or later license
 * @link https://github.com/FishikawaRCA/frca Github
 * @link https://fishikawarca.github.io/frca/ Docs
 * @link https://github.com/FishikawaRCA/frca/pulls?q=is%3Apr+is%3Aclosed Changelog
 * @internal Supports: Joomla! 2.5 and above
 * @internal Contributors: @RussW, @mandville, @Frostmakk, @AlisonAMG
 * @internal Attribution: Portions of the FRCA are copyright of the Forum Post Assistant (FPA) project and members
 * @since 20-July-2020
 *
 *
 * Fishikawa Conventions
 * please remember to version, DocBlock (above) and constant (below) (v0.0.42 denotes a pre~alpha and test version/branch)
 * default language is "en-GB" (can we please try to avoid American'isms where possible)
 * comment/note date reference format : d-mmmm-yyyy (eg: 1-january-1970)
 *   - seeing as Github tracks previous commits, overwrite last date reference with your own dated comment block
 *     unless the previous comment is descriptive or relevant to the following code block still
 * binary resource calculations use the "base-2" format (eg: 1024b/1Kib, 1024Kib/1Mib)
 * decimal resource calculations us the "base-10" format & no more than 2 decimal places (eg: 95.0, 0.75/75%)
 * white-space positioning with "tabs", (tab = 4 spaces)
 * where possible and where makes sense, soft-wrap long comments/statements on to multiple lines before 80 or 120 characters
 * complex or long statements should include additional white-spacing (1x space, before & after) to improve readability
 *   eg: if ( $testVariable = substr( '$stringVariable', 0, 5 ) ) { ... }
 *       echo 'this text '. $appendedVariable .' has an appended variable';
 * variable naming should use camelCase (1st character lowercase, word-breaks uppercase) where possible, for action/control variables use extended lowercaseUPPERCASE format
 *   eg: $camelCaseVariable  or  $candoCONTROLVARIABLE
 * function naming should also use camelCase but in the extended lowercaseUPPERCASE format
 *   eg: getSOMEDATA();  or  doSOMEACTION();
 *
 *
 * Fishikawa Custom Colour Palette & Typography
 * Primary     : #593196 (dark-purple)
 * Secondary   : #9e005d (mauve-maroon)
 * Tertiary    : #fbb03b (light-orange)
 * Font-Family : Open Sans, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto,Helvetica Neue, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol
 * Font-Size   : 0.98rem
 *
 */

/**
 * NOTE: FUNCTION ORDERING
 *
 * define DEV & DBG
 *  - do DBG
 *
 * define Self~Destruct, Auto~SSL, REMOTEs, LiveChecks
 *
 * GZip output
 *
 * doRESET
 * SESSIONS
 * PRG Pattern
 *
 * localhost check
 * self~destruct routine
 * auto~SSL routine
 *
 * candoLIVE/REMOTE (checkcURL)
 * else post a manual pda
 *
 * list top-level arrays
 *
 * test for low php values (memory_limit, max_execution_time)
 * - if low, simply double them
 *
 * if $canDOREMOTE
 * get LIVE and REMOTE data (frca-version, joomla-version, lang, pda, vel)
 * else post a manual pda(s)
 *
 * if joomla $instance present test
 * - is configured?
 * -- if YES, test valid/sane config & load config
 * --- do config & DB tests
 * --- do CONFIDENCE server, php & joomla tests
 * --- do joomla version, config perms,
 * -- if NO, only do CONFIDENCE server,php tests
 * (post PDA problems to $problemList array)
 *
 * if canDOLIVE
 * - do liveCheck (frca & joomla version)
 *   (post PDA problems to $problemList array)
 *
 * - do other (non-confidence) tests
 * server, php, (joomla - elevated permissions)
 * (post PDA problems to $problemList array)
 *
 * if $instance, canDOREMOTE & canDOVEL
 * - get EXTENSIONS
 * - do velCOMPARE
 *   (post PDA problems to $problemList array)
 * else post a manual pda
 *
 * page start HTML
 * page start BODY
 * page start HEADER
 * - try and move some heavy lifting inside body, so the loading progress bar is more acurate
 * -- maybe the $velCOMPARE
 * page start messages & DEV/DBG area
 * page start MAIN
 *
 * confidence
 *
 * count $problemList
 * problem TABS
 *
 * copyright
 *
 */

//TESTING nmespace
// not strictly necessary now, but if frca grows in time, might need the use of
// subnamespaces in functions to avoid name clashes
// https://www.php.net/manual/en/language.namespaces.basics.php
// if problems with functions etc, remove namespace below and "\" in front of PHP functions on lines;
// 674, 675, 740, 752, 3257, 3582, 3586  eg: \PDO
namespace FRCA;

/**
 * Fishikawa CORE RESOURCE CONSTANTS
 *
 */
define('_RES_FRCA', 'Fishikawa');
define('_RES_FRCA_GRAB', 'Root Cause Analysis');
define('_RES_FRCA_SHORT', 'FRCA');
define('_RES_FRCA_VERSION', '0.0.42');                                         // Changes : Major = substantial/architectural code, Minor = new features/functions, Patch = fixes/patches
define('_RES_FRCA_RELEASE', 'Pre~Alpha');                                               // can be Alpha, Beta, RC or empty
define('_RES_FRCA_CODENAME', 'radix');                                         // only change on major releases
define('_RES_FRCA_COPYRIGHT_STMT', ' Copyright &copy; 2020-' . @date("Y") . ' Russell Winter, Claire Mandville, Sveinung Larsen.');



/**
 * ENABLE/DISABLE THE FRCA DEVELOPMENT & DEBUG FUNCTIONS
 * comment (#, disable) or uncomment (enable) the desired frca functions
 *
 * - Development & Debug Functions
 *
 * 15th-December-2020 - @RussW
 *
 */
define ( '_FRCA_DEV', true );                                                  // developer-mode, displays raw frca & array data
#define ( '_FRCA_DBG', true );                                                  // debug-mode, enables php display_errors



/**
 * ENABLE/DISABLE THE FRCA SPECIAL FEATURES
 * comment (#, disable) or uncomment (enable) the desired frca features
 *
 * FRCA Auto~ Self Destruct
 *  - enabled by default, deletes FRCA if _FRCA_SELF_DESTRUCT_AGE exceeded
 *    if localhost or _FRCA_DEV or _FRCA_DBG are defined/true then self-destruction is auto-disabled later in frca
 *
 * FRCA Auto~ SSL Redirect
 * - enabled by default, redirects to the SSL site if available
 *   if localhost, non-routable/reserved IP or, in some cases, if behind a frontend-server (EG: proxy, firewall,
 *   load-balancer) or if _FRCA_DEV or _FRCA_DBG are defined/true then redirection is auto-disabled later in frca
 *
 * FRCA Auto~ Language Detection (only language code, not country/variation code)
 * - enabled by default, downloads remote language/translation from github project based on the users browser language
 *   if no language code availble, uses in-built "en" by default
 *
 * FRCA Use~ VEL (Vulnerable Extension List
 * - enabled by default, downloads remote VEL json file and compares installed extensions against known vulnerability reports
 *
 * FRCA & Joomla! Live Checks require cURL to function (tested below)
 * - enabled by default, each LiveCheck also has it's own resource requirement criteria to run, this criteria is tested for
 *   within each unique LiveCheck function (Joomla Update, FRCA Update)
 *
 */
#define( '_FRCA_SELF_DESTRUCT', true );											// enable self-destruct, attempt self-delete on next run if frca older than configured age
define( '_FRCA_SSL_REDIRECT', true );											// enable SSL Redirect, when possible & if valid SSL certificate, attempt to redirect to SSL
define( '_FRCA_LANG_DETECT', true );											// enable language detection for translations
define( '_FRCA_USE_VEL', true );												// enable using the Vulernable Extension List checks
define( '_LIVE_CHECK_FRCA', true );												// enable live latest FRCA version check
define( '_LIVE_CHECK_JOOMLA', true );											// enable live latest Joomla! version check

// nothing to do here, these are permanent fixtures
define( '_FRCA_SELF_DESTRUCT_AGE', 5 );											// age of frca file before _FRCA_SELF_DESTRUCT runs (in days from last modified/uploaded)
define( '_FRCA_SELF', basename($_SERVER['PHP_SELF']) );							// DONT DISABLE - SEVERAL FUNCTIONS RELY ON THIS : take in to account renamed FRCA, ensure all local links work

// external resource URLs
define( '_RES_URL_PDA', 'https://hotmangoteam.github.io/Fishikawatest/pdadata/frca-pdadata.json' );		// URL for problem determination aide (.json) database download
define( '_RES_URL_LANG', 'https://hotmangoteam.github.io/Fishikawatest/translation/' );					// URL for translation (.ini) file downloads
define( '_RES_URL_VEL', 'https://extensions.joomla.org/index.php?option=com_vel&format=json' );			// URL for vulnerable extension list (.json) downloads
define( '_RES_URL_FRCA', 'https://api.github.com/repos/ForumPostAssistant/FPA/releases/latest' );		// URL for frca version (.json) check
define( '_RES_URL_JOOMLA', 'https://update.joomla.org/core/list.xml' );									// URL for joomla version (.xml) check



/** DEBUG Function
 * when _FRCA_DBG defined (true/uncommented above) php error_reporting is set to maximum
 * - php errors will be displayed on screen
 * - else default = 0 (disabled)
 *
 */
if ( defined('_FRCA_DBG') ) {

	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1 );
	ini_set( 'error_reporting', -1 );

} else {

	ini_set( 'display_errors', 0 );
	ini_set( 'display_startup_errors', 0 );
	ini_set( 'error_reporting', 0 );

}



/**
 * COMPRESS THE PAGE OUTPUT
 * attempt to compress the page output for better performance
 * - test for native php zlib being enabled before calling for GZip to do the compression to avoid fatal 500 error
 *
 */
if ( ini_get('zlib.output_compression') != '1' and substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {

	ob_start('ob_gzhandler');

} else {

	ob_start();

}



/**
 * Keyboard Access Keys:
 * a screan reader accessible only note regarding this information is located just
 * inside the body so it is read to the user before any menu or page informatin is.
 *
 * d = delete, g = generate post,  o = FPA options, n = night mode, l = light mode, v = run VEL, f = re-run default FPA
 *
 * Chrome
 * Windows/Linux - [alt]+ accesskey
 * Mac/OSX - [control]+[alt]+ accesskey
 * Firefox
 * Windows/Linux - [alt]+[shift]+ accesskey
 * Mac/OSX - [control]+[alt]+ accesskey
 * Safari
 * [control]+[alt]+ accesskey
 * Edge/IE
 * [alt]+ accesskey
 *
 */



/**
 * Problem Determination Code
 * a consistent name/number convention for reporting/listing/registering/displaying errors and messages in the frca-pdadata.json file
 *
 * Use: as a problem determination test is added, a matching (next logical sequence) code and entry needs to be added to
 * the frca-pdadata.json file so errors and messages can be directly referenced (and potentially re-used) instead of always
 * looping through what might become a very large json database of issues and fixes in the future
 *
 * Purpose: although a little convoluted this allows the frca-pdadata.json file to then also be used for other purposes
 * and creates a uniquely referenceable and semi~self-describing entry for each individual problem, error or message
 * (similar to the Apache "Status & Response Code" methodology - https://en.wikipedia.org/wiki/List_of_HTTP_status_codes)
 *
 *
 * <impact-group>      <problem-code>
 *    [ 0-F ]      [ 0-F ][ 0-F ][ 0-F ][ 0-F ]
 * --------------------------------------------------------------------
 *       1            0      0      1      5   = PDC: 10015
 *       2            0      0      A      C   = PDC: 200AC
 *       3            0      0      1      5   = PDC: 30015
 *       4            0      0      A      C   = PDC: 400AC
 * --------------------------------------------------------------------
 *
 * <impact-group> : valid problem group alpha-numeric (0-F, provides up to 16 top-level groups)
 * manually adding this to the "getPDC" function (and not hardcoded in json file) allows any problem to be moved
 * between <impact-groups> by simply changing the first character of the <problem-code>
 * EG: getPDC('10023') would set the impact group to "1" (Critical) and get the problem-code "0023" from the json file.
 * If, at a later date problem-code "0023" is no-longer considered "Critical" we can easily update the getPDC function
 * to getPDC(20023) re-classifying it as "Moderate" (2)
 *
 *  0: Unclassified/Generic/Unknown errors or messages
 *  1: Critical/Fatal/Danger errors or messages
 *  2: Moderate/Serious/Warning errors or messages
 *  3: Minor/Notice/Information errors or messages
 *  4: Best Practice/Recommendation errors or messages
 *  5: (unused, future expansion)
 *  6: (unused, future expansion)
 *  7: (unused, future expansion)
 *  8: (unused, future expansion)
 *  9: (unused, future expansion)
 *  A: (unused, future expansion)
 *  B: (unused, future expansion)
 *  C: (unused, future expansion)
 *  D: (unused, future expansion)
 *  E: (unused, future expansion)
 *  F: Fishikawa internal errors or messages
 *
 *
 * <problem-code> : valid alpha-numeric problem-code (0-F + 0-F + 0-F + 0-F, provides around 65k possible problem-codes)
 * usage: starting in reverse order : 0001, 0002, 0003 etc (if, at a later date it is determined that more granular
 * "categorisation" is required, spcific numbers or letters could be "reserved" for certain types or groups of errors in
 * a similar manner to the impact-groups)
 * [W] 0-F alpha-numeric
 * [X] 0-F alpha-numeric
 * [Y] 0-F alpha-numeric
 * [Z] 0-F alpha-numeric
 *
 * Reserved problem-codes:
 * 0000 : reserved for Unknown/Unclassified/Invalid entries in to the $problemList
 * FFFF : reserved for VEL entries in to the $problemList
 *
 * <problem-category> : valid problem-category
 * Unknown, Fishikawa, Server, Web Server, Network, Browser, Database, PHP, Javascript, jQuery, Ajax, Joomla,
 * Joomla Global Configuration, Joomla Administration, Joomla Site, Library, Component, Module, Plugin, Template,
 * Remote Service, Optimisation, Security, Performance, Coding Practice, Known Bug (+automated; VEL/Vulnerable Extension List)
 *
 * Q: as a test routine can we load the site somehow and get the headers? so we can get the server/header error code?
 *
 */



/**
 * INITIALISE ESSENTIAL (EARLY) ARRAYS
 * initialise minimum data resource arrays as soon as possible
 *
 */
$problemList						= array();
$problemList['CRITICAL']			= array();
$problemList['MODERATE']			= array();
$problemList['MINOR']				= array();
$problemList['BESTPRACTICE']		= array();
$userMessages						= array();
$instance							= array();
$confidence							= array();
$confidence['SERVER']				= array();
$confidence['PHP']					= array();
$confidence['JOOMLA']				= array();


/**
 * FRCA HOUSE-KEEPING
 * the following sections are about clearing, cleaning and (re)setting Fishikawa defaults and options
 *  - setting users options through $_POST from button/form submissions in menus
 *  - subsequently clearing $_POST headers to avoid form resubmissions or on-going resubmit messages
 *
 * @RussW - 10-January-2021
 *
 */


/**
 * RESET TO DEFAULTS
 * clears all session data (dark/light mode, compact/expanded layout view choice,
 * including any pda-data and vel-data held in the user session or tmp files
 *  - forces new pda and vel json data downloads
 *  -- reloads FRCA with fresh headers to avoid doRESET loops
 *
 * @RussW - 10-January-2021
 *
 */
if ( isset($_POST['doRESET']) and $_POST['doRESET'] == '1' ) {

	// start the session so we can access any set session variables
	@session_start();

	// unset any session data so the defaults will be set again later
	unset( $_SESSION );

	// belt n braces, destroy any initialised session
	@session_destroy();

	// also delete any tmp data files that may have been created to ensure new datasets are
	// downloaded from the Fishikawa project site (Github) & the VEL feed (extensions.joomla.org)
	$tmpFiles = array( 'frca-pdadata-tmp.json', 'frca-veldata-tmp.json', 'frca-latestfrca-tmp.json', 'frca-latestjoomla-tmp.xml', 'frca-translation.ini' );

	foreach( $tmpFiles as $deleteTmpFile ) {

		if ( file_exists($deleteTmpFile) ) {

			// attempt to set a mode (permissions) that should ensure frca can unlink the file
			// we dont set 0777, as many servers protect against world, execute rights and will
			// cause a server 500 fatal error or white screen
			@chmod( $deleteTmpFile, 0776 );
			@unlink( $deleteTmpFile );

		} // delete file

		// check if the file still exists, if yes, the delete failed so try and set it back to
		// a sane/default mode and post a message to the user
		if( file_exists($deleteTmpFile) ) {

			@chmod( $deleteTmpFile, 0644 );

		} // file not deleted

	} // end foreach $tmpFiles


	// after the reset, simply reloading frca or unsetting $_POST['doRESET'] does not clear the existing http-headers
	// causing "resubmit form" messages and effectively generating a doRESET loop because the existing headers still
	// contain the previous $_POST['doRESET'] data of "1", so to avoid this condition we send a '303 - See Other' header
	// and redirect frca back to itself generating clean http-headers (PRG Pattern technique)
	header( 'HTTP/1.1 303 See Other' );
	header( 'location:'. $_SERVER['PHP_SELF'] );
    exit();

} // doRESET



/**
 * COLLECT $_POST VARIABLES & SET EQUIVALENT SESSION VARIABLES
 * use php session variables to maintain user choices state or set defaults
 *
 * - light/dark mode Template
 *   use the BS4 bootswatch cyborg (dark) theme instead of the Yeti (light, default) theme
 *
 * - problem grid layoutview
 *   "c" - compact view (default) - 2 or 3 cards in a scrollable single column / per problem
 *   "e" - expanded view - 2 or 3 cards / problem / row
 *
 * - force "en" language
 *   just incase any localisation/translation is that bad, in-error or for the RTL folks if we don't implement that feature
 *
 * note: "reset to defaults" is handled seperately (above)
 *
 * @RussW - 10-January-2021
 *
 */
@session_start();

// light/dark mode theme
if ( isset($_POST['darkmode']) and $_POST['darkmode'] == '0' ) {
	$_SESSION['darkmode'] = '0';
	$darkmode             = '0';

} elseif ( isset($_POST['darkmode']) and $_POST['darkmode'] == '1' ) {
	$_SESSION['darkmode'] = '1';
	$darkmode             = '1';

} elseif ( isset($_SESSION['darkmode']) ) {
	$_SESSION['darkmode'] = $_SESSION['darkmode'];
	$darkmode             = $_SESSION['darkmode'];

} elseif ( !isset($_SESSION['darkmode']) or ($_SESSION['darkmode'] != '1' or @$_POST['darkmode'] != '1') ) {
	$_SESSION['darkmode'] = '0';
	$darkmode             = '0';
}

// problem grid layout view
if ( isset($_POST['layoutview']) and $_POST['layoutview'] == 'e' ) {
	$_SESSION['layoutview']	= 'e';
	$layoutview         	= 'e';
	$layoutEXPANDCHECKED	= 'CHECKED';
	$layoutCOMPACTCHECKED	= '';

} elseif ( isset($_POST['layoutview']) and $_POST['layoutview'] == 'c' ) {
	$_SESSION['layoutview']	= 'c';
	$layoutview				= 'c';
	$layoutEXPANDEDCHECKED	= '';
	$layoutCOMPACTCHECKED	= 'CHECKED';

} elseif ( isset($_SESSION['layoutview']) ) {
	$_SESSION['layoutview']	= $_SESSION['layoutview'];
	$layoutview				= $_SESSION['layoutview'];

} elseif ( !isset($_SESSION['layoutview']) or ($_SESSION['layoutview'] != 'e' or !isset($_POST['layoutview']) or @$_POST['layoutview'] != 'e' or $layoutview != 'e')) {
	$_SESSION['layoutview']	= 'c';
	$layoutview				= 'c';
	$layoutEXPANDCHECKED	= '';
	$layoutCOMPACTCHECKED	= 'CHECKED';

} else {
	$layoutview				= 'c';
	$_SESSION['layoutview']	= 'c';
	$layoutEXPANDCHECKED	= '';
	$layoutCOMPACTCHECKED	= 'CHECKED';
}

// force "en" language
// get & set the user browser language
$browserLanguage = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );  // eg: "en" of en-AU, "zh" of zh-CN

// TESTING:
//echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
//echo '['.$browserLanguage.']';
//$browserLanguage = 'nn';
//$browserLanguage = 'fr';
//$browserLanguage = 'zh';


if ( isset($_POST['force_en']) and $_POST['force_en'] == '0' ) {
	$_SESSION['force_en']	= '0';
	$force_en				= '0';

} elseif ( isset($_POST['force_en']) and $_POST['force_en'] == '1' ) {
	$_SESSION['force_en']	= '1';
	$force_en				= '1';
	$browserLanguage		= "en";

} elseif ( isset($_SESSION['force_en']) ) {
	$_SESSION['force_en'] = $_SESSION['force_en'];
	$force_en             = $_SESSION['force_en'];

} elseif ( !isset($_SESSION['force_en']) or ($_SESSION['force_en'] != '1' or @$_POST['force_en'] != '1') ) {
	$_SESSION['force_en'] = '0';
	$force_en             = '0';
}

// TESTING
//echo $_SESSION['darkmode'];
//unset($_SESSION['darkmode']);
//echo $_SESSION['layoutview'];
//unset($_SESSION['layoutview']);
//unset($_SESSION);
//session_destroy();



/**
 * PREVENT REPEATED FORM SUBMISSIONS (PRG Pattern technique)
 * avoid "Resubmit Form" messages following user preference choices (light/dark mode & layoutview)
 * after reloading frca to set an option its not possible to unset the $_POST from the existing page http-headers
 * causing "resubmit form data" messages and effectively generating an annoying loop if the page is simply refreshed
 * so to avoid this condition we send a '303 - See Other' header and redirect frca back to itself with clean http-headers
 *
 * note: this must be performed AFTER collecting any $_POST variables (above) and setting a session variable for each
 * otherwise frca will never see the $_POST variable to set the session variable from
 *
 * @RussW - 10-January-2021
 *
 */
if ( isset($_POST['darkmode']) or isset($_POST['layoutview']) or isset($_POST['force_en']) ) {

	header( 'HTTP/1.1 303 See Other' );
	header( 'location:'. $_SERVER['PHP_SELF'] );
	exit();

}


/**
 * Check for a localhost before doing anything
 *
 * if on a reserved subnet, then don't use _FRCA_AUTO_DESTRUCT or _FRCA_SSL_REDIRECT functions
 * due to never changing "modified" date on copy n paste and not all local environments being able to have SSL
 *
 * if a windows development environment is "localhost" then default permisisons are always classed as
 * elevated (777) and may produce false-positives
 *
 * updated 16-january-2021 - @RussW
 *
 */
$isLOCALHOST 		= 0; // dont perform certain tasks if is localhost
$isWINLOCAL  		= 0; // dont perform certain tasks if is windows localhost
$candoSELFDESTRUCT	= 0; // dont perform (even if enabled) Auto Destruct on localhosts
$candoSSLREDIRECT	= 0; // dont perform (even if enabled) SSL Redirect on localhosts (not all localhosts can utilise SSL)

$maskLOCAL = array( '127.0',
					'10.',
					'192.168.',
					'172.16',
					'172.17.',
					'172.18.',
					'172.19.',
					'172.20.',
					'172.21.',
					'172.22.',
					'172.23.',
					'172.24.',
					'172.25.',
					'172.26.',
					'172.27.',
					'172.28.',
					'172.29.',
					'172.30.',
					'172.31.',
					'::1'
				);

foreach ($maskLOCAL as $checkLOCALHOST) {

	if ( strpos( $_SERVER['REMOTE_ADDR'], $checkLOCALHOST, 0 ) !== false ) {

		// found one of the reserved ip addresses
		$isLOCALHOST		= 1;
		$candoSELFDESTRUCT	= 0;
		$candoSSLREDIRECT	= 0;

		// now we know its a localhost, check for windows as several tests show incorrect or differing
		// info due to its architecture (compared to un*x)
		if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) {

			$isWINLOCAL			= 1;

		}

		break;

	}  else {

		$candoSELFDESTRUCT	= 1;
		$candoSSLREDIRECT	= 1;

	}

} // end foreach reserved ip & localhost check

// post an informational userMessage
if ( isset($isLOCALHOST) and $isLOCALHOST == 1 ) {

	$thisMessage	= '';

	if ( isset($isWINLOCAL) and $isWINLOCAL == 1 ) {
		$thisMessage	.= 'Windows ';
	}

	$thisMessage	.= 'localhost detected. ';

	if ( isset($candoSELFDESTRUCT) and $candoSELFDESTRUCT == 0 ) {
		$thisMessage	.= 'Self-Destruct feature disabled. ';
	}

	if ( isset($candoSSLREDIRECT) and $candoSSLREDIRECT == 0 ) {
		$thisMessage	.= 'SSL-Redirect feature disabled. ';
	}
	$userMessages[]	= $thisMessage;
	unset ( $thisMessage );

	if ( isset($isWINLOCAL) and $isWINLOCAL == 1 ) {
		$userMessages[]	= 'Windows localhost development environment detected. Due to Windows architecture, permissions will always show as "elevated" in FRCA.';
	}


} // end localhost userMessage



/**
 * FPA Self Destruct
 * comment-out _FRCA_SELF_DESTRUCT in Default Settings to disable
 * (there is no need to comment-out the _FPA_SELF_DESTRUCT_AGE constant)
 *
 * if enabled, checks the FRCA file date and if over _FRCA_SELF_DESTRUCT_AGE days old then run the self-delete routine
 *
 * - if $candoSELFDESTRUCT = 0 : don't even access the _FRCA_SELF_DESTRUCT routine
 *   as local file modified dates are not udpated when copied and will keep being deleted (thanks @sozzled)
 *
 * - if _FRCA_DEV or _FRCA_DBG enabled, disable Self Destruct, as obviously testing
 *
 * CONSTANTS are used throughout this feature as a security measure because they cannot be overriden at runtime
 * added @RussW 30-May-2020
 *
 */
if ( defined('_FRCA_SELF_DESTRUCT') and $candoSELFDESTRUCT == 1 and ( !defined('_FRCA_DEV') or !defined('_FRCA_DBG') ) ) {

	if ( file_exists( _FRCA_SELF ) ) {

		$fileinfo = stat( _FRCA_SELF );

	}

	// only try and delete the file if we can get the 'last modified' date
	if ( !empty($fileinfo) ) {

		$fileMTime = @date( 'd-m-Y', $fileinfo['mtime'] );
		$today     = @date( 'd-m-Y' );

		$thisDate = new \DateTime( $today );
		$fileDate = new \DateTime( $fileMTime );
		$interval = $thisDate->diff( $fileDate );
		$fileAge  = $interval->days;
		//var_dump($interval);

		// if all the criteria satisfied, define the _FRCA_SELF_DESTRUCT_DOIT constant
		if ( $fileAge > _FRCA_SELF_DESTRUCT_AGE and $interval->invert == 1 ) {

			define ('_FRCA_SELF_DESTRUCT_DOIT', true);

		} else {

			$fpaEXPIRENOTICE = '<span class="d-print-none d-inline-block mx-auto small text-center text-info" data-html2canvas-ignore="true">As a security measure, this copy of FPA will expire and be deleted in <strong>'. ( (int)_FPA_SELF_DESTRUCT_AGE - $fileAge) .'</strong> days.</span>';

		}

	}

} else {

	$fpaEXPIRENOTICE = '';

} // if _FRCA_SELF_DESTRUCT defined



/**
 * is a joomla instance present?
 *
 * this is a two-fold sanity check, we look two pairs of known folders, only one pair need exist
 * this caters for the potential of missing folders, but is not exhaustive or too time consuming
 *
 */
if ( (file_exists('components/') and file_exists('modules/'))
      or (file_exists('administrator/components/') and file_exists('administrator/modules/')) ) {

	$instance['instanceFOUND'] = 1;

} else {

	$instance['instanceFOUND'] = 0;

}


/**
 * if a Joomla instance is found, attempt to load the configuration
 *
 * check the standard (root "/" folder) location
 * - if found, and file size is ok (normal filesize is around J3 = 4Kib/4000b & J4 = 5Kib/5000b)
 *   load the configuration in to an array for use throughout the frca routines and testing
 *   (note: by default windows logically assigns file space by multiples of 4Kib/4096b blocks,
 *   so even an empty file can be 4096b on the file-system, so we use filesize instead of stat
 *   to get the physical block size of the file instead of the file-system (pretty-string) size.)
 *
 *   therefore, as rational test size to be valid is considered to be 1.5Kib/1536b
 *
 * - else, check the defines file to see if the user has setup a custom location
 *
 * - if not found, set $instance['instanceCONFIGURED'] to 0
 *
 */
if ( file_exists('configuration.php') and filesize('configuration.php') > '1536' ) {

		require_once ( 'configuration.php' );
		$jConfig = new \JConfig();

		$instance['instanceCONFIGURED'] = 1;

} elseif ( file_exists('includes/defines.php') ) {

	$jconfigOverride = file_get_contents( 'includes/defines.php' );
	preg_match ( '#JPATH_CONFIGURATION\s*\S*\s*[\'"](.*)[\'"]#', $jconfigOverride, $jconfigOVERRIDEPATH );

	if ( file_exists( @$jconfigOVERRIDEPATH[1] .'\configuration.php' ) and filesize( @$jconfigOVERRIDEPATH[1] .'\configuration.php') > '1536' ) {

		require_once ( @$jconfigOVERRIDEPATH[1] . '\configuration.php' );
		$jConfig = new \JConfig();

		$instance['instanceCONFIGURED'] = 1;

	}

} else {

	$instance['instanceCONFIGURED'] = 0;

} // end joomla configuration detection



/**
 * see if we can redirect the the site to SSL
 *
 * determine if an SSL certificate is available, and if its valid and can be used
 * redirects to the SSL version of the site if not ualready using SSL and is SSL capable
 *
 * criteria
 * - if $candoSSLREDIRECT = 1
 * - if _FRCA_DEV or _FRCA_DBG disabled
 * - if sslloopPROTECT = 0
 * - if SSL is not already used
 *
 * sslloopPROTECT
 * first check for an upstream server/proxy/cdn/load balancer to avoid potential SSL redirect loops
 * - a redirect loop condition may arise if the hosting server & upstream server use differing protocols
 *   to the public outbound protocol (http => http => https), if the FRCA auto-SSL feature redirects the
 *   hosting server to https, there is the potential for the upstream server begin a protocol redirection
 *   itself back to http causing a server redirection loop ("too many redirects" fatal: 500 error)
 *
 * using the previously loded (if found) Joomla! configuration ($jconfig) we check;
 * - if $force_ssl is enabled (1, 2), let the auto-redirect occur anyway as SSL is already in use
 * - if $proxy_enable is enabled AND $force_ssl is not (0), don't redirect
 * - else lets check if we have any headers from an upstream server, if so, don't auto-redirect and let
 *   the upstream server handle the protocol
 *
 * - if we haven't detected any upstream server(s), attempt to then discover if the users hosting
 *   server is SSL capable ($checkSSL has_ssl function) with a valid certificate and allow the auto-redirect
 *
 * 4-Dec-2020 - @RussW
 * FPA GitHub Issue #108
 *
 */
$sslloopPROTECT		= 1;  // assume there might be a problem first
$canuseSSL			= 0;  // assume no-SSL capability first
if ( defined('_FRCA_SSL_REDIRECT') and $candoSSLREDIRECT == 1 and ( !defined('_FRCA_DEV') or !defined('_FRCA_DBG') ) ) {

	if ( isset($jconfig->force_ssl) and $jconfig->force_ssl > '0' ) {

		// force_ssl is enabled(1,2) then assume it's working and just redirect to https
		$sslloopPROTECT	= 0;

	} elseif ( isset($jconfig->force_ssl) and $jconfig->force_ssl == '0'
			   and isset($jconfig->proxy_enable) and $jconfig->proxy_enable == '1' ) {

		// proxy_enable has been manually set, don't redirect and let the proxy manage the protocol
		$sslloopPROTECT	= 1;

	} else {

		// check for a multitude of upstream/proxy/cdn/load balancer headers
		$proxyHeaders = array( 'CLIENT_IP',
							   'FORWARDED',
							   'FORWARDED_FOR',
							   'FORWARDED_FOR_IP',
							   'VIA',
							   'X_FORWARDED',
							   'X_FORWARDED_FOR',
							   'HTTP_CLIENT_IP',
							   'HTTP_FORWARDED',
							   'HTTP_FORWARDED_FOR',
							   'HTTP_FORWARDED_FOR_IP',
							   'HTTP_PROXY_CONNECTION',
							   'HTTP_VIA',
							   'HTTP_X_FORWARDED',
							   'HTTP_X_FORWARDED_FOR'
							);

		foreach ( $proxyHeaders as $header ) {

			if ( isset($_SERVER[ $header ]) ) {

				// if upstream server found, don't redirect and let the proxy manage the protocol
				$sslloopPROTECT	= 1;

			}

			// TODO: dump in DEV mode
			echo 'TESTING: '. htmlspecialchars(@$header.": ".@$_SERVER[$header], ENT_QUOTES, 'UTF-8')."<br />";

		} // end $headers foreach

	} // end sslloopPROTECT


    /**
	 * see if we can redirect the the site to SSL
	 *
     * determine if an SSL certificate is available, and if its valid and can be used
     * redirects to the SSL version of the site if not ualready using SSL and is SSL capable
	 *
	 * criteria
	 * - if $candoSSLREDIRECT = 1
	 * - if _FRCA_DEV or _FRCA_DBG disabled
	 * - if sslloopPROTECT = 0
	 * - if SSL is not already used
	 *
     */
	// TODO: need to test online with CA Certifcates, MAMP Certificate fails and does not redirect
    function has_ssl( $domain ) {

		$res		= false;

		$stream		= @stream_context_create( array( 'ssl' => array( 'capture_peer_cert' => true ) ) );
		$socket		= @stream_socket_client( 'ssl://' . $domain . ':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream );

		// if we got an ssl certificate we check here, if the certificate domain matches the website domain.
		if ( $socket ) {

			$cont			= stream_context_get_params( $socket );
			$cert_ressource	= $cont['options']['ssl']['peer_certificate'];
			$cert			= openssl_x509_parse( $cert_ressource );

			// expected name has format "/CN=*.yourdomain.com"
			$namepart		= explode( '=', $cert['name'] );

			// we want to correctly confirm the certificate even for subdomains like "www.yourdomain.com"
			if ( count( $namepart ) == 2 ) {

				$cert_domain  = trim( $namepart[1], '*. ' );
				$check_domain = substr( $domain, -strlen( $cert_domain ) );
				$res          = ( $cert_domain == $check_domain );

			} // end cert confirmation

		} // end $socket

		return $res;

	}

	// check SSL status
	$checkSSL	= has_ssl($_SERVER['HTTP_HOST']);

	$pageURL	= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // do the rediect if SSL is available and site is not using it
	if ( is_bool(@$checkSSL) === true and @$_SERVER['HTTPS'] != 'on' and $sslloopPROTECT == 0 ) {

         if ( @$checkSSL ) {
			header( 'HTTP/1.1 303 See Other' );
            header( 'Location: https://'.$pageURL );
			exit;

		 }

	} // end do SSL Redirect


} // end if _FRCA_SSL_REDIRECT & canadoSSLREDIRECT



/**
 * CHECK FOR cURL & allow_url_fopen AVAILABILITY
 * minimum requirement to complete pda-data, vel-data, translation download and use any "Live Check" features
 *
 * - check there is a cURL module loaded
 * - check the curl_exec function is available
 * - check cURL is not in php disabled_functions
 * - check allow_url_fopen php is enabled
 *
 * - seperately check for simpleXML for latest joomla
 *
 * @RussW - 27-May-2020
 *
 */
if ( extension_loaded( 'curl' )
	   and function_exists( 'curl_exec' )
	   and stristr(ini_get( 'disable_functions' ), 'curl') == false
	   and ini_get( 'allow_url_fopen' ) == true ) {

	$candoREMOTE		= 1; // generic check status
	$candoGETPDA		= 1;
	$candoGETLANG		= 1;
	$candoGETVEL		= 1;
	$candoFRCACHECK		= 1;

} else {

	$candoREMOTE		= 0;
	$candoGETPDA		= 0;
	$candoGETLANG		= 0;
	$candoGETVEL		= 0;
	$candoFRCACHECK		= 0;

	// post a problemList entry manually as we dont have remote access to PDA/PDC
	$problemList['CRITICAL']['0001'] = array(
		'heading'		=> 'Fishikawa was unable to access any required remote resources',
		'description'	=> 'cURL is either not available or has been disabled by your host, FRCA was unable to download the required resources',
		'category' 		=> 'Fishikawa',
		'severity'		=> '1',
		'symptoms'		=> array(
			'0'	=> 'No problem determination tests performed',
			'1'	=> 'No vulnerable extension tests performed',
			'2' => 'FRCA & Joomla version checks are not available',
			'3' => "No translations are available"
		),
		'actions'		=> array(
			'0'	=> 'enable cURL in PHP',
			'1'	=> 'try reloading FRCA again'
		),
		'problemcode'	=> '0001'
	);

} // end candoREMOTE


// joomla update server uses xml
if ( extension_loaded( 'simplexml' ) and ini_get( 'allow_url_fopen' ) == true ) {

	$candoJOOMLACHECK	= 1;

} else {

	$candoJOOMLACHECK	= 0;

	// post a problemList entry manually as we dont have remote access to simpleXML for Joomla version checks
	$problemList['CRITICAL']['0002'] = array(
		'heading'		=> 'Fishikawa was unable to access a desired remote resource',
		'description'	=> 'simpleXML is either not available or has been disabled by your host, FRCA was unable to download the desired resource',
		'category' 		=> 'Fishikawa',
		'severity'		=> '2',
		'symptoms'		=> array(
			'0'	=> 'Joomla version checks are not available'
		),
		'actions'		=> array(
			'0'	=> 'enable simpleXML in PHP',
			'1'	=> 'try reloading FRCA again'
		),
		'problemcode'	=> '0002'
	);

}



/**
 * LANGUAGE DETECTION OR USE DEFAULT "en-GB"
 * if _FRCA_LANG_DETECT enabled
 * - attempt to automatically get the users primary/first accepted broswer language
 *   - load $lang with "en"
 *   - if force_en not set & non~en detected
 *     then check if frca has a translation and download from github & merge $updatelang over $lang
 *   - else, just use the in-built "en" language
 *
 * note: we use the ISO Alpha-2 standard for language definition (xx-YY) for non~ "en" languages
 *       except for any "en-YY", we treat all english languages as "en-GB" to avoid adversly effecting
 *       frca performance by needlessly downloading minor english language difference files.
 *       eg: any detected "en-YY" language will always use the internal "en-GB" language strings.
 *
 * note: intentionally un-translated strings/constants;
 *       - Fishikawa core resource (_RES_XXX_YYY_ZZZ) strings/constants
 *       - development (_FRCA_DEV) notice and message strings/constants
 *
 * @RussW - 12-Jan-2021
 *
 * TODO: workout RTL language requirements?
 */
$translationError	= '0';

// check if force_en is set
/*
if ( isset($_SESSION['force_en']) and $_SESSION['force_en'] == 1 ) {

	$browserLanguage = "en";

} else {

	$browserLanguage = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );  // eg: "en" of en-AU

}

 // TESTING:
//echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
//$browserLanguage = 'nn-NO';
//$browserLanguage = 'fr-FR';
//$browserLanguage = 'zh-CN';
*/


// always load the $lang Array with "en-GB", then;
// - overwrite existing elements with the translated strings
//   this caters for any missing translation strings or errors in the translation files and means that if
//   the translation is incomplete, the "en-GB" element will be displayed regardless and not an error
$lang = parse_ini_string ('

	; Internal Resources
	FRCA_DOREMOTE_ERROR = "cURL is not available. FRCA could not access or download necessary remote resources"
	FRCA_TRANSLATE_ERROR = "could not get or could not find a translation for '. $browserLanguage .'"
	FRCA_DEVENA = "Developer Mode Enabled"
	FRCA_DEVDSC = "troubleshooting information has been made available"
	FRCA_DEVMI = "developer-mode-information"
	FRCA_DBGENA = "Debug Mode Enabled"
	FRCA_DBGDSC = "php error_reporting and display has been enabled"
	FRCA_SSLDSC = "Unable to redirect to the SSL version of the site, do you have a valid SSL certificate?"
	FRCA_PRXDSC = "A Caching Proxy, Load Balancer or CDN Server was detected, SSL status may not be local"

	; Confidence Dashboard
	FRCA_CONFDASH_HEADING = "Confidence Status"
	FRCA_CONFDASH_SERVER_HEADING = "Server Setup"
	FRCA_CONFDASH_SERVER_TEXT = "How well your hosting server is configured"
	FRCA_CONFDASH_PHP_HEADING = "PHP Settings"
	FRCA_CONFDASH_PHP_TEXT = "How well your php environment is setup"
	FRCA_CONFDASH_JOOMLA_HEADING = "Joomla!<sup>&reg;</sup> Settings"
	FRCA_CONFDASH_JOOMLA_TEXT = "How well your Joomla instance is configured"
	FRCA_CONFDASH_AGGREGATE_HEADING = "Confidence"
	FRCA_CONFDASH_AGGREGATE_TEXT = "The aggregate rating of all confidence tests"

	; Problem Status Categories
	FRCA_CRITICAL = "Critical"
	FRCA_CRITICAL_DESC = "Showstopper issues that will cause fatal errors and installation or upgrade failures"
	FRCA_MODERATE = "Moderate"
	FRCA_MODERATE_DESC = "Potential issues that may cause difficulties with installations, updates or specific features and functions"
	FRCA_MINOR = "Minor"
	FRCA_MINOR_DESC = "Minor issues that may reduce or limit feature functionality or administration and maintenace tasks"
	FRCA_BESTPRACTICE = "Best Practice"
	FRCA_BESTPRACTICE_DESC = "Recommendations that could potentially enhance performance, security, productivity or rankings"
	FRCA_NOPROBS_DESC = "Good News<br />No known problems were detected"

	; Problem Severity/Risk
	FRCA_RISKHIGH = "High"
	FRCA_RISKMEDIUM = "Medium"
	FRCA_RISKLOW = "Low"
	FRCA_RISKUC = "UC"

	; Problem Type Category
	FRCA_SERVER = "Server"
	FRCA_PHP = "PHP"
	FRCA_WEBSERVER = "Web Server"
	FRCA_DATABASE = "Database"
	FRCA_PERMISSIONS = "Permissions"
	FRCA_OWNERSHIP = "Ownership"
	FRCA_PERFORMANCE = "Performance"
	FRCA_FILESYSTEM = "File-System"
	FRCA_NETWORK = "Network"
	FRCA_CACHE = "Cache"
	FRCA_JS = "Javascript/jQuery/Ajax"
	FRCA_JOOMLA = "Joomla"
	FRCA_LIBRARY = "Library"
	FRCA_COMPOENT = "Component"
	FRCA_MODULE = "Module"
	FRCA_PLUGIN = "Plugin"
	FRCA_TEMPLATE = "Template"
	FRCA_REMOTESVC = "Remote Service"
	FRCA_OPTIMISATION = "Optimisation"
	FRCA_SECURITY = "Security"
	FRCA_CODEPRACTICE = "Coding Practice"
	FRCA_KNOWNBUG = "Known Bug"

	; Unique Problem Symptoms
	FRCA_WHITESCREEN = "White Screen"
	FRCA_FATALERRORS = "Fatal Errors"
	FRCA_WARNINGS = "Warnings"
	FRCA_WRITEERRORS = "Write Errors"
	FRCA_UPLOADERRORS = "Upload Errors"
	FRCA_TIMEOUTERRORS = "Timeout Errors"
	FRCA_BADPERFORMANCE = "Poor Performance"
	FRCA_POSSEXPLOITS = "Possible Exploits"

	; Unique Problem Effects
	FRCA_INSTALLATION = "Installation"
	FRCA_DEVELOPMENT = "Development"
	FRCA_MAINTENANCE = "Maintenance"
	FRCA_ADMINISTRATION = "Administration"
	FRCA_OPERATION = "Operation"
	FRCA_SEOSMOSEM = "SEO, SMO & SEM"

	; Unique VEL Strings
	FRCA_VEL = "VEL"
	FRCA_VEL_FIXED = "Fixed"
	FRCA_VEL_NOTFIXED = "Not Fixed"
	FRCA_VEL_ALLPREV = "All Previous"
	FRCA_VEL_CVECVS30 = "CVE ID/CVS30 Score"
	FRCA_VEL_AUTHSITE = "Author Site"

	; Tranlsation Strings
	; generally not used/needed in "en" language(s), only a placeholder for other translations
	FRCA_TRANSLATION_ERROR_FOUND = "Found a translation error? Report it on the <a href=\"https://github.com/hotmangoTeam/Fishikawatest/issues\" target=\"_blank\">Fishikawa Project</a>"

	; Test Routine/Results
	FRCA_PMISS = "Password missing"
	FRCA_SERV = "Server"
	FRCA_WRITABLE = "Writable"
	FRCA_SUPPHP = "PHP Supports"
	FRCA_SUPSQL = "Databse Supports"
	FRCA_BADPHP = "Known Buggy PHP"
	FRCA_BADZND = "Known Buggy Zend"

	; General Strings
	FRCA_Y = "Yes"
	FRCA_N = "No"
	FRCA_M = "Maybe"
	FRCA_ENA = "Enabled"
	FRCA_DIS = "Disabled"
	FRCA_NA = "Not Applicable"
	FRCA_U = "Unknown"
	FRCA_UC = "Unclassified"
	FRCA_CATEGORY = "Category"
	FRCA_TYPE = "Type"
	FRCA_UPDATETO = "Update To"
	VER_CHECK_DWNLD = "New version available"
	VER_CHECK_ATOLD = "is out of date"
	VER_CHECK_RELEASE = "Release"
	VER_CHECK_IMPROVEMENTS = "Improvements"
	VER_CHECK_ATCUR = "is up to date"
	VER_CHECK_ATDEV = "is a development version"

	; Legal & Footer
	LICENSE_FOOTER = "The FRCA comes with ABSOLUTELY NO WARRANTY.<br />
	This is free software, and covered under the GNU GPLv3 or later license. You are welcome to redistribute it under certain conditions.<br />
	For details read the LICENSE file included in the download package with this script."
	LICENSE_LINK = "A copy of the license may also be obtained at <a href="https://www.gnu.org/licenses/" target="_blank" rel="noopener noreferrer">https://www.gnu.org/licenses/</a>"
	FRCA_JDISCLAIMER = "Fishikawa (FRCA) is not affiliated with or endorsed by The Joomla! Project<sup>&trade;</sup>. Use of the Joomla!<sup>&reg;</sup> name, symbol, logo, and related trademarks is licensed by Open Source Matters, Inc."


	; probably not needed anymore
	FRCA_SNAP_TITLE = "Environment Snapshot"
	FRCA_INST_TITLE = "Application Instance"
	FRCA_SYS_TITLE = "System Environment"
	FRCA_PHP_TITLE = "PHP Environment"
	FRCA_PHPEXT_TITLE = "PHP Extensions"
	FRCA_PHPREQ_TITLE = "PHP Requirements"
	FRCA_APAMOD_TITLE = "Apache Modules"
	FRCA_APAREQ_TITLE = "Apache Requirements"
	FRCA_DB_TITLE = "Database Instance"
	FRCA_TABLE = "Tables"
	FRCA_DBTBL_TITLE = "Table Structure"
	FRCA_PERMCHK_TITLE = "Permissions Checks"
	FRCA_COREDIR_TITLE = "Core Folders"
	FRCA_ELEVPERM_TITLE = "Elevated Permissions"
	FRCA_EXTCOM_TITLE = "Components"
	FRCA_EXTMOD_TITLE = "Modules"
	FRCA_EXTPLG_TITLE = "Plugins"
	FRCA_TMPL_TITLE = "Templates"
	FRCA_EXTLIB_TITLE = "Libraries"

	FRCA_DNE = "Does Not Exist"

    ; added by @frostmakk 24.01.2021
	FRCA_EXT_CORE = "Core"
	FRCA_EXT_3PD = "3rd Party"
	FRCA_WIN_LOCALHOST = "Elevated permissions are expected on Windows localhost development environments."
	FRCA_FPALATEST2 = "Download the latest FPA release (zip)"
	FRCA_DASHBOARD_CONFIDENCE_NOTE = "TODO String used but not defined."
	FRCA_DELNOTE_LN2 = "TODO String used but not defined."
	FRCA_DELNOTE_LN3 = "TODO String used but not defined."
	FRCA_INS_8 = "TODO String used but not defined."



');

// add the default language code
$lang['languagecode'] = 'en';



// DEVELOPER MODE INFO
if ( defined('_FRCA_DEV') ) {
	if ( isset($lang) ) {
		@$langdevMSG .= 'got internal en-GB language array<br />';
	}
	if ( isset($_SESSION['force_en']) and $_SESSION['force_en'] == 1 ) {
		@$langdevMSG .= 'force_en to en-GB enabled<br />';
	}
} // end developer message



// TESTING
//echo '1['.$browserLanguage.']';


// if translations & candoREMOTE set, look for any matching translation file remotely
if ( defined('_FRCA_LANG_DETECT')
     and $candoREMOTE == '1'
     and isset($browserLanguage)
	 and !empty($browserLanguage)
	 and substr( $browserLanguage, 0, 2 ) != 'en'
	 and ( isset($_SESSION['force_en'])
	 and $_SESSION['force_en'] != 1 ) ) {

	/**
	 * Nominalise, if possible, any known frca unused or less common localisations
	 *   eg: if the detected langauge is "no-NO" or "nn-NO" (Norwegian locaisations)
	 *       frca redirects to the existing "nb" translation/locaistation, being the most common spoken/written version
	 *
	 * @RussW 26-january-2021
	 */
	if ( substr( $browserLanguage, 0, 2 ) == 'no' or substr( $browserLanguage, 0, 2 ) == 'nb' ) {
		// norwegian nynorsk > norwegian bokmål
		$browserLanguage = 'nb';

	} elseif ( substr( $browserLanguage, 0, 2 ) == 'se' ) {
		// northern sami > swedish
		$browserLanguage = 'sv';

	} elseif ( substr( $browserLanguage, 0, 2 ) == 'os' ) {
		// ossetic > georgia
		$browserLanguage = 'ka';

	}


	include_once ( 'frca-get-translation.php' );

	/**
	 * we have an frca translation
	 * set the new language and merge the updated (translated) laguage strings in to the existing $lang Array
	 * - overwriting any duplicate keys with the new language
	 *   - leaving any untranslatioed string as "en" so we have a complete languageset
	 *
	 */


	if ( isset($updatelang) and !empty($updatelang) ) {

		$lang = array_merge ( $lang, $updatelang );

		// update the language code
		$lang['languagecode'] = $browserLanguage;


		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$langdevMSG .= ' updated language array with '. $lang['languagecode'];
		} // end developer message


		// post a userMessage if a translation was found and they notice errors
		$userMessages[]	= '<i class="fas fa-question-circle fa-fw text-info"></i> '. $lang['FRCA_TRANSLATION_ERROR_FOUND'];

	} else {

		$translationError = '1';

		// post a userMessage if a translation wasn't found
		$userMessages[]	= '<i class="fas fa-comment-slash fa-fw text-info"></i> Translation '. $browserLanguage .' was not found, using default en-GB instead';

		// DEVELOPER MODE INFO
		if ( defined('_FRCA_DEV') ) {
			@$langdevMSG .= '<br /><strong> could not get "'. $browserLanguage .'" language</strong><br />';
		} // end devloper message

	}

} // end attempt translation

// TESTING
//echo '2['.$browserLanguage.']';




	// DEVELOPER MODE INFO
//	if ( defined('_FRCA_DEV') ) {
//		@$langdevMSG .= 'using default internal "en-GB" language';
//	} // end devloper message




include_once ( 'frca-dashboard.php' );


/**
 * indlude the PDA json database from cURL
 */
include_once ( 'frca-getpdadata.php' );


/**
 * indlude the frca getPDC() function
 */
include ( 'frca-getpdc-function.php' );


/**
 * indlude the VEL json database from cURL
 */
include_once ( 'frca-getveldata.php' );






/**
 * FIND AND ESTABLISH INSTALLED EXTENSIONS
 *
 * this function recurively looks for installed Components, Modules, Plugins and Templates
 * it only reads the .xml file to determine installation status and info, some extensions
 * do not have an associated .xml file and wont be displayed (normally core extensions)
 *
 * modified version of the function for the recirsive folder permisisons previously
 */
 include_once ( 'frca-getextensions.php' );


/**
 * indlude the frca doFRCALIVE() function
 */
 include_once ( 'frca-dofrcalive.php' );


/** SET THE JOOMLA! PARENT FLAG AND BASIC CONSTANTS ********************************************/
define( '_VALID_MOS', 1 );  // for J!1.0
define( '_JEXEC', 1 );      // for J!1.5, J!1.6 thru J!2.5, J!3.0, J!4.0

// @frostmakk 24.01.2021  Reactivated defines. Some to be renamed.
define('_RES_FPALINK2', 'https://github.com/ForumPostAssistant/FPA/zipball/en-GB/'); // where to get the latest 'Final Releases'




/*
// Define some basic assistant information
define('_LICENSE_LINK', '<a href="https://www.gnu.org/licenses/" target="_blank" rel="noopener noreferrer">https://www.gnu.org/licenses/</a>'); // link to GPL license
define('_LICENSE_FOOTER', ' The FRCA comes with ABSOLUTELY NO WARRANTY. <br> This is free software,
	and covered under the GNU GPLv3 or later license. You are welcome to redistribute it under certain conditions.
	For details read the LICENSE.txt file included in the download package with this script.
    A copy of the license may also be obtained at ');
define('_RES_FPALINK', 'https://github.com/ForumPostAssistant/FPA/tarball/en-GB/'); // where to get the latest 'Final Releases'
// @RussW updated 23-May-2020
define('_RES_FPALATEST', 'Download the latest FPA release (tar.gz)');
define('_RES_FPALINK2', 'https://github.com/ForumPostAssistant/FPA/zipball/en-GB/'); // where to get the latest 'Final Releases'
// @RussW updated 23-May-2020
define('_RES_FPALATEST2', 'Download the latest FPA release (zip)');

** DEFINE LANGUAGE STRINGS **************************************************************
define('_PHP_DISERR', 'Display PHP Errors Enabled');
define('_PHP_ERRREP', 'PHP Error Reporting Enabled');
define('_PHP_LOGERR', 'PHP Errors Being Logged To File');
// section titles & developer-mode array names
// updated @RussW 29-May-2020
-define('_FPA_SNAP_TITLE', 'Environment Snapshot');
-define('_FPA_INST_TITLE', 'Application Instance');
-define('_FPA_SYS_TITLE', 'System Environment');
-define('_FPA_PHP_TITLE', 'PHP Environment');
-define('_FPA_PHPEXT_TITLE', 'PHP Extensions');
-define('_FPA_PHPREQ_TITLE', 'PHP Requirements');
-define('_FPA_APAMOD_TITLE', 'Apache Modules');
-define('_FPA_APAREQ_TITLE', 'Apache Requirements');
-define('_FPA_DB_TITLE', 'Database Instance');
-define('_FPA_TABLE', 'Tables');
-define('_FPA_DBTBL_TITLE', 'Table Structure');
-define('_FPA_PERMCHK_TITLE', 'Permissions Checks');
-define('_FPA_COREDIR_TITLE', 'Core Folders');
-define('_FPA_ELEVPERM_TITLE', 'Elevated Permissions');
-define('_FPA_EXTCOM_TITLE', 'Components');
-define('_FPA_EXTMOD_TITLE', 'Modules');
-define('_FPA_EXTPLG_TITLE', 'Plugins');
-define('_FPA_TMPL_TITLE', 'Templates');
-define('_FPA_EXTLIB_TITLE', 'Libraries');
// snapshot definitions
define('_FPA_SUPPHP', 'PHP Supports');
define('_FPA_SUPSQL', 'Database Supports');
define('_FPA_BADPHP', 'Known Buggy PHP');
define('_FPA_BADZND', 'Known Buggy Zend');
// slow screen message
// @RussW _FPA_SLOWGENPOST to be removed 23-May-2020
define('_FPA_SLOWGENPOST', 'Generating Post Output...');
// @RussW _FPA_SLOWRUNTEST to be removed 23-May-2020
define('_FPA_SLOWRUNTEST', 'Hang on while we run some tests...');
// remove script notice content - @PhilD 17-Apr-2012
// @RussW _FPA_DELNOTE_LN1 to be removed 23-May-2020
define('_FPA_DELNOTE_LN1', '<h5 class="text-danger">** SECURITY NOTICE **</h5>');
// @RussW updated 23-May-2020
define('_FPA_DELNOTE_LN2', '<p class="small">The FPA script may contain private information that could be used to obtain information by others to compromise your website. We recommend that you remove the FPA script after you use it.</p>');
// @RussW updated 23-May-2020
define('_FPA_DELNOTE_LN3', '<p class="text-danger">After use, please delete the FPA script.</p>');
// dev/diag-mode content
///define('_FRCA_DEVMI', 'developer-mode-information');
define('_FPA_ELAPSE', 'elapse-runtime');
// @RussW removed uppercase 27-May-2020
///define('_FRCA_DEVENA', 'Developer Mode Enabled');
///define('_FRCA_DEVDSC', 'This means that a variety of additional information will be displayed on-screen to assist with troubleshooting this script.');
// @RussW typo fixed & removed uppercase 27-May-2020
define('_FPA_DIAENA', 'Diagnostic Mode Enabled');
define('_FPA_DIADSC', 'This means that all php and script errors will be displayed on-screen and logged out to a file named');
// @RussW _FPA_DIAERR to be removed 27-May-2020
define('_FPA_DIAERR', 'Last DIGNOSTIC MODE Error');
define('_FPA_SPNOTE', 'Special Note');
// user post form content
define('_FPA_INSTRUCTIONS', 'Instructions');
define('_FPA_INS_1', 'Enter your problem description <em>(optional)</em>');
define('_FPA_INS_2', 'Enter any error messages you see <em>(optional)</em>');
define('_FPA_INS_3', 'Enter any actions taken to resolve the issue <em>(optional)</em>');
define('_FPA_INS_4', 'Select detail level options of output <em>(optional)</em>');
// @RussW updated 23-May-2020
define('_FPA_INS_5', 'Click the <span class="text-success">Click Here To Generate Post</span> button to build the post content');
// @RussW updated 23-May-2020
define('_FPA_INS_6', 'Copy the contents of the <span class="text-dark">Post Content</span> box and paste it into a post following the instructions provided');
// @RussW updated 23-May-2020
define('_FPA_INS_7', '<p class="text-muted">To copy the contents of the Post Detail box:</p>
            <ol>
            <li class="pb-1">Click the <span class="badge badge-warning">Copy Post Content To Clipboard</span> button</li>
            <li class="text-muted p-1">Login to the Joomla! Forum and start a new post or reply</li>
            <li class="pb-1">Use <strong>CTRL-v</strong> to paste the copied text into your forum post/reply</li>
            <li class="pb-1"><em>Disable smilies to prevent charcters being converted by the forums software</em></li>
            </ol>
            <p class="xsmall py-1 my-1"><i class="fas fa-info-circle text-info"></i> In the event that the "Copy Post Content To Clipboard" button does not work, <strong>click inside the Post Content textarea</strong>, then <strong>press CTRL-a (or Command-a)</strong> to select all the content, then <strong>press CTRL-c (Command-c)</strong> to copy the content and use <strong>CRTL-v (Command-v)</strong> to paste the copied content in to your forum post</p>');
// @RussW added 23-May-2020
define('_FPA_INS_8', '<p class="text-center">Your site has many extensions installed, the post output exceededs the forum post limit. <strong>Please run the FPA twice</strong> and make two seperate posts/replies.</p><ol><li>First run without the plugins selected</li><li>Run again with only the plugins selected</li></ol>');
define('_FPA_POST_NOTE', 'Leave ALL fields blank/empty to simply post diagnostic information.');
define('_FPA_PROB_DSC', 'Problem Description');
define('_FPA_PROB_MSG', 'Log/Error Message');
define('_FPA_PROB_ACT', 'Actions Taken To Resolve');
define('_FPA_PROB_CRE', 'Actions To ReCreate Issue');
define('_FPA_OPT', 'Optional Settings');
define('_FPA_SHOWELV', 'Show elevated folder permissions');
define('_FPA_SHOWDBT', 'Show database table statistics');
define('_FPA_SHOWCOM', 'Show Components');
define('_FPA_SHOWMOD', 'Show Modules');
define('_FPA_SHOWLIB', 'Show Libraries');
define('_FPA_SHOWPLG', 'Show Plugins');
define('_FPA_SHOWCEX', 'Show Core Extensions');
define('_FPA_INFOPRI', 'Information Privacy');
define('_FPA_STRICT', 'Strict');
define('_FPA_PRIVNON', 'None');
define('_FPA_PRIVNONNOTE', 'No elements are masked');
define('_FPA_PRIVPAR', 'Partial');
define('_FPA_PRIVPARNOTE', 'Some elements are masked');
define('_FPA_PRIVSTR', 'Strict');
define('_FPA_PRIVSTRNOTE', 'All indentifiable elements are masked');
define('_FPA_CLICK', 'Click Here To Generate Post');
define('_FPA_OUTMEM', 'Out of Memory');
define('_FPA_OUTTIM', 'Execution Time-Outs');
define('_FPA_INCPOPS', 'Temporarily increase PHP Memory and Execution Time');
define('_FPA_POSTD', 'Your Forum Post Content');

** common screen and post output strings ************************************************
define('_FPA_APP', 'Joomla!');
define('_FPA_INSTANCE', 'Instance');
define('_FPA_PLATFORM', 'Platform');
define('_FPA_DB', 'Database');
define('_FPA_SYS', 'System');
define('_FPA_SERV', 'Server');
define('_FPA_CLNT', 'Client');
define('_FPA_HNAME', 'Hostname');
define('_FPA_DISC', 'Discovery');
define('_FPA_LEGEND', 'Legends and Settings');
define('_FPA_GOOD', 'OK/GOOD');
define('_FPA_WARNINGS', 'WARNINGS');
define('_FPA_ALERTS', 'ALERTS');
define('_FPA_SITE', 'Site');
define('_FPA_ADMIN', 'Admin');
define('_FPA_BY', 'by');
define('_FPA_OR', 'or');
define('_FPA_OF', 'of');
define('_FPA_TO', 'to');
define('_FPA_FOR', 'for');
define('_FPA_IS', 'is');
define('_FPA_AT', 'at');
define('_FPA_IN', 'in');
define('_FPA_BUT', 'but');
define('_FPA_LAST', 'Last');
define('$lang['FRCA_N']ONE', 'None');
define('_FPA_DEF', 'default');
define('$lang['FRCA_Y']', 'Yes');
define('$lang['FRCA_N']', 'No');
define('_FPA_FIRST', 'First');
define('_FPA_M', 'Maybe');
define('_FPA_MDB', 'Yes - MariaDB Used');
define('_FPA_U', 'Unknown');
define('_FPA_K', 'Known');
define('_FPA_E', 'Exists');
define('_FPA_JCORE', 'Core');
define('_FPA_3PD', '3rd Party');
define('_FPA_TESTP', 'tests performed');
define('_FPA_DNE', 'Does Not Exist');
define('_FPA_F', 'Found');
define('$lang['FRCA_N']F', 'Not Found');
define('_FPA_OPTS', 'Options');
define('_FPA_CF', 'Config');
define('_FPA_CFG', 'Configuration');
define('$lang['FRCA_Y']C', 'Configured');
define('$lang['FRCA_N']C', 'Not Configured');
-define('_FPA_ECON', 'Connection Error');
define('_FPA_CON', 'Connect');
define('$lang['FRCA_Y']CON', 'Connected');
define('_FPA_CONT', 'Connection Type');
define('$lang['FRCA_N']CON', 'Not Connected');
define('_FPA_SUP', 'support');
define('$lang['FRCA_Y']SUP', 'supported');
define('_FPA_DROOT', 'Doc Root');
define('$lang['FRCA_N']SUP', 'not supported');
define('$lang['FRCA_N']OA', 'Not Attempted');
define('$lang['FRCA_N']ER', 'No Errors Reported');
define('_FPA_ER', 'Error(s) Reported');
define('_FPA_ERR', 'error');
define('_FPA_ERRS', 'errors');
define('$lang['FRCA_Y']MATCH', 'Matches');
define('$lang['FRCA_N']MATCH', 'Mis-Match');
define('$lang['FRCA_NA']COMP', 'Appear Incomplete');
define('$lang['FRCA_Y']ACOMP', 'Appear Complete');
define('_FPA_SEC', 'Security');
define('_FPA_FEAT', 'Features');
define('_FPA_PERF', 'Performance');
define('$lang['FRCA_NA']', 'N/A');
define('_FPA_CRED', 'Credentials');
define('_FPA_CREDPRES', 'Credentials Present');
define('_FPA_HOST', 'Host');
define('_FPA_TEC', 'Technology');
define('_FPA_WSVR', 'Web Server');
define('_FPA_HIDDEN', 'protected');
define('_FPA_PASS', 'Password');
define('_FPA_USER', 'Username');
define('_FPA_USR', 'User');
// @RussW updated 23-May-2020
define('_FPA_TNAM', 'Name');
define('_FPA_TSIZ', 'Size');
define('_FPA_TENG', 'Engine');
define('_FPA_TCRE', 'Created');
define('_FPA_TUPD', 'Updated');
define('_FPA_TCKD', 'Checked');
define('_FPA_TCOL', 'Collation');
define('_FPA_CHARS', 'Character Set');
define('_FPA_TFRA', 'Fragment Size');
define('_FPA_AUTH', 'Author');
define('_FPA_ADDR', 'Address');
define('_FPA_STATUS', 'Status');
define('_FPA_TYPE', 'Type');
define('_FPA_TREC', 'Rcds');  // Number of table records
define('_FPA_TAVL', 'Avg. Length');
-define('_FPA_MODE', 'Mode');
-define('_FPA_WRITABLE', 'Writable');
-define('_FPA_RO', 'Read-Only');
-define('_FPA_FOLDER', 'Folder');
-define('_FPA_FILE', 'File');
-define('_FPA_OWNER', 'Owner');
-define('_FPA_GROUP', 'Group');
-define('_FPA_VER', 'Version');
-define('_FPA_CRE', 'Created');
-define('_FPA_LOCAL', 'Local');
-define('_FPA_REMOTE', 'Remote');
-define('_FPA_SECONDS', 'seconds');
define('_FPA_TBL', 'Table');
define('_FPA_STAT', 'Statistics');
define('_FPA_BASIC', 'Basic');
define('_FPA_DETAILED', 'Detailed');
define('_FPA_ENVIRO', 'Environment');
define('_FPA_VALID', 'Valid');
define('$lang['FRCA_N']VALID', 'Not Valid');
define('_FPA_EN', 'Enabled');
define('_FPA_DI', 'Disabled');
define('$lang['FRCA_N']O', 'No');
define('_FPA_STATS', 'statistics');
define('_FPA_POTOI', 'Potential Ownership Issues');
define('_FPA_POTME', 'Potential Missing Extensions');
define('_FPA_POTMM', 'Potential Missing Modules');
define('_FPA_DBCONNNOTE', 'may not be an error, check with host for remote access requirements.');
define('_FPA_DBCREDINC', 'Credentials incomplete or not available');
define('_FPA_MISSINGCRED', 'Missing credentials detected');
define('$lang['FRCA_N']ODISPLAY', 'Nothing to display.');
define('_FPA_EMPTY', 'could be empty');
define('_FPA_UINC', 'increased by user, was');
define('_PHP_VERLOW', 'PHP version too low');
define('_FPA_SHOW', 'Show');
define('_FPA_HIDE', 'Hide');
define('act', '');
define('_FPA_MVFW', 'More than one instance of version.php found!');
define('_FPA_MVFWF', 'Multiple found');
define('_FPA_DIR_UNREADABLE', 'A directory is <b>NOT READABLE</b> and cannot be checked!');
define('_FPA_DI_PHP_FU', 'Disabled Functions');
define('_FPA_FDSKSP', 'Free Disk Space');
define('$lang['FRCA_N']IMPLY', 'Not implemented for');
-define('_FPA_PGSQL', 'PostgreSQL');
-define('_FPA_PMISS', 'Password missing');
-define('_FPA_DEFI', 'Defines');
-define('_FPA_DEFIPA', 'Site and Admin config paths not equal');
define('_FPA_CONF_PREF_TABLE', '#of Tables with config prefix');
define('_FPA_OTHER_TABLE', '#of other Tables');
define('_FPA_MSSQL_SUPP', 'Microsoft SQL Server is not supported by the FPA');
define('_FPA_MYSQLI_CONN', 'PHP function mysqli_connect not found.');
// @RussW new May-2020
define('_FPA_DASHBOARD', 'Dashboard');
define('_FPA_DASHBOARD_CONFIDENCE_TITLE', 'Confidence');
define('_FPA_DASHBOARD_CONFIDENCE_NOTE', 'An initial <em>basic confidence audit</em> has been performed to determine if the minimum requirements and best practices have been met to ensure the successful operation of the latest version of Joomla! and it\'s standard functions.');
define('_FPA_DISCOVERY_REPORT', 'Discovery Report');
define('_FPA_PERMOWN', 'Permissions & Ownership');
define('_FPA_CNF_A', 'Joomla! should run without any problems');
define('_FPA_CNF_B', 'Joomla! should run but some features may have minor problems');
define('_FPA_CNF_C', 'Joomla! might run but some features will have problems');
define('_FPA_CNF_D', 'Joomla! might run but many features will have problems');
define('_FPA_CNF_E', 'Joomla! probably will not run or will have many problems');
define('_FPA_CNF_F', 'Joomla! probably will not run and will have many problems');
define('_FPA_UPRIV', 'User Privileges');
define('_VER_CHECK_ATOLD', 'is out of date');
define('_VER_CHECK_ATCUR', 'is up to date');
define('_VER_CHECK_ATDEV', 'is a development version');

define('_FPA_WIN_LOCALHOST', '<span class="d-inline-block text-dark py-1"><span class="badge badge-info">Note:</span> Elevated permissions are expected on Windows localhost development environments.</span>');

define('_FRCA_JDISCLAIMER', 'Fishikawa (FRCA) is not affiliated with or endorsed by The Joomla! Project<sup>&trade;</sup>. Use of the Joomla!<sup>&reg;</sup> name, symbol, logo, and related trademarks is licensed by Open Source Matters, Inc.');
*/
/** END LANGUAGE STRINGS *****************************************************************/



/**
 * FRCA (SELF-)DELETE ROUTINE
 * attempts to delete the FRCA file from site, including any tmp files and session data
 * If it fails then set message to manually delete the file is presented.
 *
 */
if ( ( isset($_POST['act']) and $_POST['act']  == 'delete' ) or ( defined('_FRCA_SELF_DESTRUCT') and defined('_FRCA_SELF_DESTRUCT_DOIT') ) ) {

	$host	= $_SERVER['HTTP_HOST'];
	$uri	= rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra	= '';  // add index (or other) page if desired


	// also delete frca and any tmp data files that may have been created to ensure new datasets are
	// downloaded from the Fishikawa project site (Github) & the VEL feed (extensions.joomla.org)
	$tmpFiles = array( 'frca-pdadata-tmp.json', 'frca-veldata-tmp.json', 'frca-latestfrca-tmp.json', 'frca-latestjoomla-tmp.xml', 'frca-translation.ini' );
	foreach( $tmpFiles as $deleteTmpFile ) {

		if ( file_exists($deleteTmpFile) ) {

			// attempt to set a mode (permissions) that should ensure frca can unlink the file
			// we dont set 0777, as many servers protect against world, execute rights and will
			// cause a server 500 fatal error or white screen
			@chmod( $deleteTmpFile, 0776 );
			@unlink( $deleteTmpFile );

		} // delete file

		// check if the file still exists, if yes, the delete failed so try and set it back to
		// a sane/default mode and post a message to the user
		if( file_exists($deleteTmpFile) ) {

			@chmod( $deleteTmpFile, 0644 );

		} // file not deleted

	} // end foreach $tmpFiles


	// try to set script to (octal) 0776 to make sure we have permission to delete
	// and then delete frca itself
	@chmod( _FRCA_SELF, 0776 );
	@unlink( _FRCA_SELF );


	// start the session so we can access any set session variables and complete some housekeeping
	@session_start();

	// unset any session data so the defaults will be set again later
	unset( $_SESSION );

	// belt n braces, destroy any initialised session
	@session_destroy();



	// prepare to post an approprite on-sceen message and link back to users home page of site.
	// if SSL return to https:// otherwise http://
	if ( @$_SERVER['HTTPS'] == 'on' ? $hostPrefix = 'https://' : $hostPrefix = 'http://' );

	$page	= $hostPrefix . $host . $uri . $extra;

	// something went wrong and the frca script and/or tmp files were not deleted
	// so it/they must be removed manually, tell the user they need to action this themselves
	if ( file_exists(_FRCA_SELF) ) {

		// check if we need to reset permissions on any tmp files
		foreach( $tmpFiles as $chmodTmpFile ) {

			if ( file_exists($chmodTmpFile) ) {

				// attempt to set a sane mode (permissions) of (octal) 0644 on any tmp files
				@chmod( $chmodTmpFile, 0644 );

			} // chmod file

		} // end chmod tmp files


		// and finally the frca itself
		@chmod( _FRCA_SELF, 0644 );


		echo '<div id="deleteMessage" style="padding:20px;border:1px solid #e99002;background-color:#fff8ee;margin:0 auto;margin-top:50px;margin-bottom:20px;max-width:70%;position:relative;z-index:9999;top:10%;font-family:sans-serif, arial;" align="center">';
		echo '<h1 style="color:#e99002;font-size:44px;">SOMETHING WENT WRONG!</h1>';

		if ( defined('_FPA_SELF_DESTRUCT_DOIT') ) {

			echo '<h2 style="color:#43ac6a;">As a security measure, FRCA attempted to self-delete itself and any tmp files due to the time it has been present on the server, but was not successful.</h2>';
			echo '<p style="color:#e99002;font-size:20px;margin:0 auto;max-width:80%;">Please remove these files manually using FTP or through your hosting File Manager, or upload a new copy of FRCA to continue using it.</p>';

		} else {

			echo '<h1 style="color:#e99002;font-size:44px;">SOMETHING WENT WRONG!</h1>';
			echo '<p style="color:#e99002;font-size:30px;">We could not delete the FRCA file(s) (' . _FRCA_SELF . ').</p>';
			echo '<p style="color:#e99002;font-size:20px;margin:0 auto;max-width:80%;">For your website security, please remove the file <em style="color:#f04124;">' . _FRCA_SELF . '</em> and any tmp files manually using FTP or through your hosting File Manager.</p>';

		}

	} else {

		echo '<div id="deleteMessage" style="padding:20px;border:1px solid #43ac6a;background-color:#effff5;margin:0 auto;margin-top:50px;margin-bottom:20px;max-width:70%;position:relative;z-index:9999;top:10%;font-family:sans-serif, arial;" align="center">';

		if ( defined('_FPA_SELF_DESTRUCT_DOIT') ) {

			echo '<h2 style="color:#43ac6a;">As a security measure, this copy of FRCA has been self-deleted due to the time it has been present on the server.</h2>';
			echo '<p style="color:#e99002;font-size:20px;margin:0 auto;max-width:80%;">You will need to upload another copy of FRCA to continue.</p>';

		} else {

			echo '<h1 style="color:#43ac6a;">Thank You For Using Fishikawa</h1>';
			echo '<p style="color:#43ac6a;">We hope it was useful to you</p>';
		}
	}

	echo '<p><a href="' . $page . '">Go To Your Home Page</a></p>';
	echo '</div>';

	exit;

} // end delete routine








// TODO: do we still need this? commented out for now
/** TIMER-POPS ***************************************************************************/
/*
// mt_get: returns the current microtime
function mt_get()
{
	global $mt_time;
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

// mt_start: starts the microtime counter
function mt_start()
{
	global $mt_time;
	$mt_time = mt_get();
}

// mt_end: calculates the elapsed time
function mt_end($len = 3)
{
	global $mt_time;
	$time_end = mt_get();
	return round($time_end - $mt_time, $len);
}
// actually start the timer-pop
mt_start();
*/

// TODO: check if these old arrays are needed anymore
include_once ( 'frca-arraylist.php' );




/**
 * is joomla configured
 *
 * confirm that if we found a joomla instance that it is actually configureded
 * a very basic and simple check for the database connection variables being configured
 *  - we intentionaly dont check for the user & password in $instance['instanceCONFIGURED']
 *    as this is purely looking for multiple points of to make an assumption that joomla
 *    has been installed or not
 *
 *    user & password are checked separately afterwards and will present it's own
 *    $instance['instanceDBCREDOK'] variable and post it's own issue status
 */
if ( $instance['instanceFOUND'] == 1 and
	 ( !empty($jConfig->host) and
	   !empty($jConfig->dbtype) and
	   !empty($jConfig->dbprefix) and
	   !empty($jConfig->db) and
	   !empty($jConfig->user) and
	   !empty($jConfig->password) ) ) {

	// if all database variables present, consider J! installed
	$instance['instanceCONFIGURED'] = $lang['FRCA_Y'];
	$instance['instanceDBCREDOK']	= $lang['FRCA_Y'];

} elseif ( $instance['instanceFOUND'] == 1 and
		   ( !empty($jConfig->host) and
			 !empty($jConfig->dbtype) and
			 !empty($jConfig->dbprefix) and
			 !empty($jConfig->db) ) ) {

	// if all database variables are present, consider joomla configured (ignoring user/password as that is checked individually)
	$instance['instanceCONFIGURED'] = $lang['FRCA_Y'];

} elseif ( $instance['instanceFOUND'] == 1 and
		   ( empty($jConfig->host) or
			 empty($jConfig->dbtype) or
			 empty($jConfig->dbprefix) or
			 empty($jConfig->db) ) ) {

	// if any individual database variable is empty, consider joomla to not be configured (ignoring user/password as that is checked individually)
	$instance['instanceCONFIGURED'] = $lang['FRCA_N'];

}


// if joomla assumed to be installed, check the database user & password credentials separately
if ( $instance['instanceFOUND'] == 1 and
	 $instance['instanceCONFIGURED'] == $lang['FRCA_Y'] and
	 ( !empty($jConfig->user) and
	   !empty($jConfig->password) ) ) {

	$instance['instanceDBCREDOK']	= $lang['FRCA_Y'];

} else {

	$instance['instanceDBCREDOK']	= $lang['FRCA_N'];

}





// build the developer-mode function to display the raw arrays
// TODO: thik about doing this for new _FRCA_DEV stuff
function showDev(&$section)
{
	if (defined('_FRCA_DEV')) {
		echo '<div class="row"><div class="col-12">';
		echo '<div class="card border border-warning mb-3 w-100">';
		echo '<div class="card-header bg-warning text-white">';
		echo '<span class="text-dark">[' . $lang['FRCA_DEVMI'] . ']</span><br />';
		echo @$section['ARRNAME'] . ' Array :';
		echo '</div>';

		echo '<div class="card-body small p-2">';
		echo '<pre class="xsmall m-0">';
		print_r($section);
		echo '<pre>';
		echo '<p class="m-0"><em>' . $lang['FRCA_ELAPSE'] . ': <strong>' . mt_end() . '</strong> ' . $lang['FRCA_SECONDS'] . '</em></p>';
		echo '</div>';
		echo '</div>';
		echo '</div></div>';
	} // end if _FRCA_DEV defined
} // end developer-mode function


// TODO: do we need these arrays
/** DETERMINE SOME SETTINGS BEFORE FPA MIGHT PLAY WITH THEM ******************************/
$phpenv['phpERRORDISPLAY']  = ini_get('display_errors');
$phpenv['phpERRORREPORT']   = ini_get('error_reporting');
$fpa['ORIGphpMEMLIMIT']     = ini_get('memory_limit');
$fpa['ORIGphpMAXEXECTIME']  = ini_get('max_execution_time');
$phpenv['phpERRLOGFILE']    = ini_get('error_log');
$system['sysSHORTOS']       = strtoupper(substr(PHP_OS, 0, 3)); // WIN, DAR, LIN, SOL
$system['sysSHORTWEB']      = strtoupper(substr($_SERVER['SERVER_SOFTWARE'], 0, 3)); // APA = Apache, MIC = MS IIS, LIT = LiteSpeed etc


// TODO: check if pops are low and just try and double them
// TODO: do these earlier as well
// if the user see's Out Of Memory or Execution Timer pops, double the current memory_limit and max_execution_time
if (@$_POST['increasePOPS'] == 1) {
	ini_set('memory_limit', (rtrim($fpa['ORIGphpMEMLIMIT'], "M") * 2) . "M");
	ini_set('max_execution_time', ($fpa['ORIGphpMAXEXECTIME'] * 2));
}



// TODO: convert this in to a new pda entry
/**
 * DETERMINE IF THERE IS A KNOWN ERROR ALREADY
 *
 * here we try and determine if there is an existing php error log file, if there is we
 * then look to see how old it is, if it's less than one day old, lets see if what the last
 * error this and try and auto-enter that as the problem description
 */
// is there an existing php error-log file?
if (file_exists($phpenv['phpERRLOGFILE'])) {
	// when was the file last modified?
	$phpenv['phpLASTERRDATE'] = @date("dS F Y H:i:s.", filemtime($phpenv['phpERRLOGFILE']));

	// determine the number of seconds for one day
	$age = 86400;
	// $age = strtotime('tomorrow') - time();
	// get the modified time in seconds
	$file_time = filemtime($phpenv['phpERRLOGFILE']);
	// get the current time in seconds
	$now_time = time();

	/**
	 * if the file was modified less than one day ago, grab the last error entry
	 * Changed this section to get rid of the "Strict Standards: Only variables should be passed by reference" error
	 * @PhilD 20-Sep-2012
	 *
	 */
	if ($now_time - $file_time < $age) {
		/**
		 * !FIXME memory allocation error on large php_error file - @RussW
		 * replaced these two lines with code below - @PhilD 23-Sep-2012
		 *  $lines = file( $phpenv['phpERRLOGFILE'] );
		 *  $phpenv['phpLASTERR'] = array_pop( $lines );
		 *
		 * Begin the fix for the memory allocation error on large php_error file
		 * Solution is to read the file line by line; not reading the whole file in memory.
		 * I just open a kind of a pointer to it, then seek it char by char.
		 * This is a more efficient way to work with large files.   - @PhilD 23-Sep-2012
		 *
		 */
		$line = '';

		$f = fopen(($phpenv['phpERRLOGFILE']), 'r');
		$cursor = -1;

		fseek($f, $cursor, SEEK_END);
		$char = fgetc($f);

		// Trim trailing newline chars of the file
		while ($char === "\n" || $char === "\r") {
			fseek($f, $cursor--, SEEK_END);
			$char = fgetc($f);
		}

		// Read until the start of file or first newline char
		while ($char !== false && $char !== "\n" && $char !== "\r") {
			// Prepend the new char
			$line = $char . $line;
			fseek($f, $cursor--, SEEK_END);
			$char = fgetc($f);
		}

		$phpenv['phpLASTERR'] = $line;
	}
} // End Fix for memory allocation error when reading php_error file



/**
 * DETERMINE INSTANCE STATUS & VERSIONING
 *
 * here we check for known files to determine if an instance even exists, then we look for
 * the version and configuration files. some differ between between versions, so we have a
 * bit of jiggling to do.
 * to try and avoid "white-screens" fpa no-longer "includes" these files, but merely tries
 * to open and read them, although this is slower, it improves the reliability of fpa.
 *
 */


/**
 * what version is the instance?
 *
 */
// >= J3.8.0
if (file_exists('libraries/src/Version.php')) {
	$instance['cmsVFILE'] = 'libraries/src/Version.php';

	// >= J3.6.3
} elseif (file_exists('libraries/cms/version/version.php') and !file_exists('libraries/platform.php')) {
	$instance['cmsVFILE'] = 'libraries/cms/version/version.php';

	// J2.5 & J3.0 libraries/joomla/platform.php files
} elseif (file_exists('libraries/cms/version/version.php') and file_exists('libraries/platform.php')) {
	$instance['cmsVFILE'] = 'libraries/cms/version/version.php';

	// J1.7 includes/version.php & libraries/joomla/platform.php files
} elseif (file_exists('includes/version.php') and file_exists('libraries/platform.php')) {
	$instance['cmsVFILE'] = 'includes/version.php';

	// J1.6 libraries/joomla/version.php & joomla.xml files
} elseif (file_exists('libraries/joomla/version.php') and file_exists('joomla.xml')) {
	$instance['cmsVFILE'] = 'libraries/joomla/version.php';

	// J1.5 & Nooku Server libraries/joomla/version.php & koowa folder
} elseif (file_exists('libraries/joomla/version.php') and file_exists('libraries/koowa/koowa.php')) {
	$instance['cmsVFILE'] = 'libraries/joomla/version.php';

	// J1.5 libraries/joomla/version.php & xmlrpc folder
} elseif (file_exists('libraries/joomla/version.php') and file_exists('xmlrpc/')) {
	$instance['cmsVFILE'] = 'libraries/joomla/version.php';

	// J1.0 includes/version.php & mambots folder
} elseif (file_exists('includes/version.php') and file_exists('mambots/')) {
	$instance['cmsVFILE'] = 'includes/version.php';

	// fpa could find the required files to determine version(s)
} else {
	$instance['cmsVFILE'] = $lang['FRCA_N'];
}

//echo $instance['cmsVFILE'];

/**
 * Detect multiple instances of version file
 *
 */
if (file_exists('libraries/src/Version.php')) {
	$vFile1 = 1;
} else {
	$vFile1 = 0;
}
if (file_exists('libraries/cms/version/version.php')) {
	$vFile2 = 1;
} else {
	$vFile2 = 0;
}
if (file_exists('includes/version.php')) {
	$vFile3 = 1;
} else {
	$vFile3 = 0;
}
if (file_exists('libraries/joomla/version.php')) {
	$vFile4 = 1;
} else {
	$vFile4 = 0;
}
$vFileSum = $vFile1 + $vFile2 + $vFile3 + $vFile4;



/**
 * what version is the framework? (J!1.7 & above)
 *
 */
// J1.7 libraries/joomla/platform.php
if (file_exists('libraries/platform.php')) {
	$instance['platformVFILE'] = 'libraries/platform.php';

	// J1.5 Nooku Server libraries/koowa/koowa.php
} elseif (file_exists('libraries/koowa/koowa.php')) {
	$instance['platformVFILE'] = 'libraries/koowa/koowa.php';

	// J3.7
} elseif (file_exists('libraries/joomla/platform.php')) {
	$instance['platformVFILE'] = 'libraries/joomla/platform.php';
} else {
	$instance['platformVFILE'] = $lang['FRCA_N'];
}



// read the cms version file into $cmsVContent (all versions)
if ($instance['cmsVFILE'] != $lang['FRCA_N']) {
	$cmsVContent = file_get_contents($instance['cmsVFILE']);
	// find the basic cms information
	preg_match('#\$PRODUCT\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsPRODUCT);
	preg_match('#\$RELEASE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELEASE);
	preg_match('#\$(?:DEV_LEVEL|MAINTENANCE)\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVLEVEL);
	preg_match('#\$(?:DEV_STATUS|STATUS)\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVSTATUS);
	preg_match('#\$(?:CODENAME|CODE_NAME)\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsCODENAME);
	preg_match('#\$(?:RELDATE|RELEASE_DATE)\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELDATE);

	// Joomla 3.5 to 3.9
	if (empty($cmsPRODUCT)) {
		preg_match('#const\s*PRODUCT\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsPRODUCT);
		preg_match('#const\s*RELEASE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELEASE);
		preg_match('#const\s*DEV_LEVEL\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVLEVEL);
		preg_match('#const\s*DEV_STATUS\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVSTATUS);
		preg_match('#const\s*CODENAME\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsCODENAME);
		preg_match('#const\s*RELDATE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELDATE);
		preg_match('#const\s*MAJOR_VERSION\s*=\s*(.*);#', $cmsVContent, $cmsMAJOR_VERSION);
	}

	// Joomla 4
	if (empty($cmsRELEASE)) {
		preg_match('#const\s*PRODUCT\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsPRODUCT);
		preg_match('#const\s*DEV_STATUS\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVSTATUS);
		preg_match('#const\s*CODENAME\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsCODENAME);
		preg_match('#const\s*RELDATE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELDATE);
		preg_match('#const\s*EXTRA_VERSION\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $EXTRA_VERSION);
		preg_match('#const\s*MAJOR_VERSION\s*=\s*(.*);#', $cmsVContent, $cmsMAJOR_VERSION);
		preg_match('#const\s*MINOR_VERSION\s*=\s*(.*);#', $cmsVContent, $cmsMINOR_VERSION);
		preg_match('#const\s*PATCH_VERSION\s*=\s*(.*);#', $cmsVContent, $cmsPATCH_VERSION);
		$cmsRELEASE[1] = $cmsMAJOR_VERSION[1] . '.' . $cmsMINOR_VERSION[1];
		if (strlen($EXTRA_VERSION[1]) > 0) {
			$cmsDEVLEVEL[1] = $cmsPATCH_VERSION[1] . '-' . $EXTRA_VERSION[1];
		} else {
			$cmsDEVLEVEL[1] = $cmsPATCH_VERSION[1];
		}
	}

// TESTING
//echo $cmsVContent;

	if (empty($cmsMAJOR_VERSION)) {
		$cmsMAJOR_VERSION[1] = '0';
	}

	$instance['cmsMAJORVERSION'] = $cmsMAJOR_VERSION[1];
	$instance['cmsPRODUCT'] = $cmsPRODUCT[1];
	$instance['cmsRELEASE'] = $cmsRELEASE[1];
	$instance['cmsDEVLEVEL'] = $cmsDEVLEVEL[1];
	$instance['cmsDEVSTATUS'] = $cmsDEVSTATUS[1];
	$instance['cmsCODENAME'] = $cmsCODENAME[1];
	$instance['cmsRELDATE'] = $cmsRELDATE[1];
}

$thisJVER	= @$instance['cmsRELEASE'] .'.' . @$instance['cmsDEVLEVEL'];

// TESTING
// var_dump($instance);

// read the platform version file into $platformVContent (J!1.7 & above only)
if ($instance['platformVFILE'] != $lang['FRCA_N']) {

	$platformVContent = file_get_contents($instance['platformVFILE']);

	// find the basic platform information
	if ($instance['platformVFILE'] == 'libraries/koowa/koowa.php') {

		// Nooku platform based
		preg_match('#VERSION.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformRELEASE);
		preg_match('#VERSION.*=\s[\'|\"].*-(.*)-.*[\'|\"];#', $platformVContent, $platformDEVSTATUS);

		$instance['platformPRODUCT'] = 'Nooku';
		$instance['platformRELEASE'] = $platformRELEASE[1];
		$instance['platformDEVSTATUS'] = $platformDEVSTATUS[1];
	} else {

		// default to the Joomla! platform, as it is most common at the momemt
		preg_match('#PRODUCT\s*=\s*[\'"](.*)[\'"]#', $platformVContent, $platformPRODUCT);
		preg_match('#RELEASE\s*=\s*[\'"](.*)[\'"]#', $platformVContent, $platformRELEASE);
		preg_match('#MAINTENANCE\s*=\s*[\'"](.*)[\'"]#', $platformVContent, $platformDEVLEVEL);
		preg_match('#STATUS\s*=\s*[\'"](.*)[\'"]#', $platformVContent, $platformDEVSTATUS);
		preg_match('#CODE_NAME\s*=\s*[\'"](.*)[\'"]#', $platformVContent, $platformCODENAME);
		preg_match('#RELEASE_DATE\s*=\s*[\'"](.*)[\'"]#', $platformVContent, $platformRELDATE);

		// Joomla 3.5 to 3.9
		if (empty($platformPRODUCT)) {
			preg_match('#const\s*PRODUCT\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsPRODUCT);
			preg_match('#const\s*RELEASE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELEASE);
			preg_match('#const\s*MAINTENANCE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVLEVEL);
			preg_match('#const\s*STATUS\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsDEVSTATUS);
			preg_match('#const\s*CODE_NAME\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsCODENAME);
			preg_match('#const\s*RELEASE_DATE\s*=\s*[\'"](.*)[\'"]#', $cmsVContent, $cmsRELDATE);
		}

		$instance['platformPRODUCT'] = $platformPRODUCT[1];
		$instance['platformRELEASE'] = $platformRELEASE[1];
		$instance['platformDEVLEVEL'] = $platformDEVLEVEL[1];
		$instance['platformDEVSTATUS'] = $platformDEVSTATUS[1];
		$instance['platformCODENAME'] = $platformCODENAME[1];
		$instance['platformRELDATE'] = $platformRELDATE[1];
	}
}


// TODO: do we need this? only support J2.5 and up now
/**
 * is Joomla! installed/configured?
 *
 * determine exactly where the REAL configuration file is, it might not be the one in the "/" folder
 *
 */
if (@$instance['cmsRELEASE'] == '1.0') {

	if (file_exists('configuration.php')) {
		$instance['configPATH'] = 'configuration.php';
	}
} elseif (@$instance['cmsRELEASE'] == '1.5') {
	$instance['configPATH'] = 'configuration.php';
} elseif (@$instance['cmsRELEASE'] >= '1.6') {

	if (file_exists('defines.php') or file_exists('administrator\defines.php')) {
		$instance['definesEXIST'] = $lang['FRCA_Y'];

		// look for a 'defines' override file in the "/" folder.
		if (file_exists('defines.php')) {

			$cmsOverride = file_get_contents('defines.php');
			preg_match('#JPATH_CONFIGURATION\s*\S*\s*[\'"](.*)[\'"]#', $cmsOverride, $cmsOVERRIDEPATH);

			if (file_exists(@$cmsOVERRIDEPATH[1] . '\configuration.php')) {
				$instance['configPATH'] = $cmsOVERRIDEPATH[1] . '\configuration.php';
				$instance['configSiteDEFINE'] = $lang['FRCA_Y'];
			} else {
				$instance['configPATH'] = 'configuration.php';
				$instance['configSiteDEFINE'] = $lang['FRCA_Y'];
			}
		} else {
			$instance['configPATH'] = 'configuration.php';
			$instance['configSiteDEFINE'] = $lang['FRCA_N'];
		}

		if (file_exists('administrator\defines.php')) {

			$cmsAdminOverride = file_get_contents('administrator\defines.php');
			preg_match('#JPATH_CONFIGURATION\s*\S*\s*[\'"](.*)[\'"]#', $cmsAdminOverride, $cmsADMINOVERRIDEPATH);

			if (file_exists(@$cmsOVERRIDEPATH[1] . '\configuration.php')) {
				$instance['configADMINPATH'] = $cmsADMINOVERRIDEPATH[1] . '\configuration.php';
				$instance['configAdminDEFINE'] = $lang['FRCA_Y'];
			} else {
				$instance['configADMINPATH'] = 'configuration.php';
				$instance['configAdminDEFINE'] = $lang['FRCA_Y'];
			}
		} else {
			$instance['configAdminDEFINE'] = $lang['FRCA_N'];
			$instance['configADMINPATH'] = 'configuration.php';
		}

		if (($instance['configPATH'] <> $instance['configADMINPATH']) or ($instance['configSiteDEFINE'] <> $instance['configAdminDEFINE'])) {
			$instance['equalPATH'] = $lang['FRCA_N'];
		} else {
			$instance['equalPATH'] = $lang['FRCA_Y'];
		}
	} else {
		$instance['configPATH'] = 'configuration.php';
		$instance['definesEXIST'] = $lang['FRCA_N'];
		$instance['equalPATH'] = $lang['FRCA_Y'];
	}
} else {
	$instance['configPATH'] = 'configuration.php';
}


// check the configuration file (all versions)
if (file_exists($instance['configPATH'])) {
// TODO: check what this effects as frca is doing the same earlier
//	$instance['instanceCONFIGURED'] = $lang['FRCA_Y'];

	// determine it's ownership and mode
	if (is_writable($instance['configPATH'])) {
		$instance['configWRITABLE']	= $lang['FRCA_Y'];
	} else {
		$instance['configWRITABLE']	= $lang['FRCA_N'];
	}

	$instance['configMODE'] = substr(sprintf('%o', fileperms($instance['configPATH'])), -3, 3);

	if (function_exists('posix_getpwuid') and $system['sysSHORTOS'] != 'WIN') { // gets the UiD and converts to 'name' on non Windows systems
		$instance['configOWNER'] = posix_getpwuid(fileowner($instance['configPATH']));
		$instance['configGROUP'] = posix_getgrgid(filegroup($instance['configPATH']));
	} else { // only get the UiD for Windows, not 'name'
		$instance['configOWNER']['name'] = fileowner($instance['configPATH']);
		$instance['configGROUP']['name'] = filegroup($instance['configPATH']);
	}



	/**
	 * if present, is the configuration file valid?
	 *
	 * added code to fix the config version mis-match on 2.5 versions of Joomla - @PhilD 8-Apr-2012
	 * reworked code block to better determine version in 1.7 to 3.0+ versions of Joomla - @PhilD 6-Aug-2012
	 *
	 */
	$cmsCContent = file_get_contents($instance['configPATH']);

	// >= 3.8.0
	if (preg_match('#(public)#', $cmsCContent) and file_exists('libraries/src/Version.php')) {
		$instance['configVALIDFOR'] = $instance['cmsRELEASE'];
		$instance['cmsVFILE'] = 'libraries/src/Version.php';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		// >= 3.6.3
	} elseif (preg_match('#(public)#', $cmsCContent) and $instance['platformVFILE'] == $lang['FRCA_N'] and file_exists('libraries/cms/version/version.php')) {
		$instance['configVALIDFOR'] = $instance['cmsRELEASE'];
		$instance['cmsVFILE'] = 'libraries/cms/version/version.php';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		//for 3.0
	} elseif (preg_match('#(public)#', $cmsCContent) and $instance['platformVFILE'] != $lang['FRCA_N']) {
		$instance['configVALIDFOR'] = $instance['cmsRELEASE'];
		$instance['cmsVFILE'] = 'libraries/cms/version/version.php';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		//for 2.5
	} elseif (preg_match('#(public)#', $cmsCContent) and substr($instance['platformRELEASE'], 0, 2) == '11') {
		$instance['configVALIDFOR'] = $instance['cmsRELEASE'];
		$instance['cmsVFILE'] = 'libraries/cms/version/version.php';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		//for 1.7
	} elseif (preg_match('#(public)#', $cmsCContent) and $instance['platformVFILE'] != $lang['FRCA_N']  and $instance['cmsVFILE'] != 'libraries/cms/version/version.php') {
		$instance['cmsVFILE'] = 'includes/version.php';
		$instance['configVALIDFOR'] = $instance['cmsRELEASE'];
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		//for 1.6
	} elseif (preg_match('#(public)#', $cmsCContent) and $instance['platformVFILE'] == $lang['FRCA_N']) {
		$instance['configVALIDFOR'] = '1.6';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		// for 1.5
	} elseif (preg_match('#(var)#', $cmsCContent)) {
		$instance['configVALIDFOR'] = '1.5';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];

		// for 1.0
	} elseif (preg_match('#(\$mosConfig_)#', $cmsCContent)) {
		$instance['configVALIDFOR'] = '1.0';
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_Y'];
	} else {
		$instance['configVALIDFOR'] = $lang['FRCA_U'];
	}


	// fpa found a configuration.php but couldn't determine the version, is it valid?
	if ($instance['configVALIDFOR'] == $lang['FRCA_U']) {

		if (filesize($instance['configPATH']) < 512) {
			$instance['configSIZEVALID'] = $lang['FRCA_N'];
		}
	}


	// check if the configuration.php version matches the discovered version
	if ($instance['configVALIDFOR'] != $lang['FRCA_U'] and $instance['cmsVFILE'] != $lang['FRCA_N']) {

		// set defaults for the configuration's validity and a sanity score of zero
		$instance['configSANE'] = $lang['FRCA_N'];
		$instance['configSANITYSCORE'] = 0;


		// !TODO add white-space etc checks
		// do some configuration.php sanity/validity checks
		if (filesize($instance['configPATH']) > 512) {
			$instance['cfgSANITY']['configSIZEVALID'] = $lang['FRCA_Y'];
		}

		// !TODO FINISH  white-space etc checks
		$instance['cfgSANITY']['configNOTDIST']  = $lang['FRCA_Y'];   // is not the distribution example
		$instance['cfgSANITY']['configNOWSPACE'] = $lang['FRCA_Y'];  // no white-space
		$instance['cfgSANITY']['configOPTAG']    = $lang['FRCA_Y'];     // has php open tag
		$instance['cfgSANITY']['configCLTAG']    = $lang['FRCA_Y'];     // has php close tag
		$instance['cfgSANITY']['configJCONFIG']  = $lang['FRCA_Y'];   // has php close tag

		// run through the sanity checks, if sane ( =Yes ) increment the score by 1 (should total 6)
		foreach ($instance['cfgSANITY'] as $i => $sanityCHECK) {

			if ($instance['cfgSANITY'][$i] == $lang['FRCA_Y']) {
				$instance['configSANITYSCORE'] = $instance['configSANITYSCORE'] + 1;
			}
		}

		// if the configuration file is sane, set it as valid
		if ($instance['configSANITYSCORE'] == '6') {
			$instance['configSANE'] = $lang['FRCA_Y'];   // configuration appears valid?
		}
	} else {
		$instance['instanceCFGVERMATCH'] = $lang['FRCA_U'];
	}



	/**
	 * include configuration.php
	 *
	 */

	if ($instance['configVALIDFOR'] != $lang['FRCA_U']) {
      //??why		ini_set('display_errors', 1);
		//$includeconfig = require_once($instance['configPATH']);
		//$config = new JConfig();
		// now uses $jConfig at line: 719


		$instance['configERRORREP'] = $jConfig->error_reporting;
		$instance['configDBTYPE'] = $jConfig->dbtype;
		$instance['configDBHOST'] = $jConfig->host;
		$instance['configDBNAME'] = $jConfig->db;
		$instance['configDBPREF'] = $jConfig->dbprefix;
		$instance['configDBUSER'] = $jConfig->user;
		$instance['configDBPASS'] = $jConfig->password;

		switch ($jConfig->offline) {
			case true:
				$instance['configOFFLINE'] = 'true';
				break;
			case false:
				$instance['configOFFLINE'] = 'false';
				break;
			default:
				$instance['configOFFLINE'] = $jConfig->offline;
		}

		switch ($jConfig->sef) {
			case true:
				$instance['configSEF'] = 'true';
				break;
			case false:
				$instance['configSEF'] = 'false';
				break;
			default:
				$instance['configSEF'] = $jConfig->sef;
		}

		switch ($jConfig->gzip) {
			case true:
				$instance['configGZIP'] = 'true';
				break;
			case false:
				$instance['configGZIP'] = 'false';
				break;
			default:
				$instance['configGZIP'] = $jConfig->gzip;
		}

		switch ($jConfig->caching) {
			case true:
				$instance['configCACHING'] = 'true';
				break;
			case false:
				$instance['configCACHING'] = 'false';
				break;
			default:
				$instance['configCACHING'] = $jConfig->caching;
		}

		switch ($jConfig->debug) {
			case true:
				$instance['configSITEDEBUG'] = 'true';
				break;
			case false:
				$instance['configSITEDEBUG'] = 'false';
				break;
			default:
				$instance['configSITEDEBUG'] = $jConfig->debug;
		}

		if (isset($jConfig->shared_session)) {
			switch ($jConfig->shared_session) {
				case true:
					$instance['configSHASESS'] = 'true';
					break;
				case false:
					$instance['configSHASESS'] = 'false';
					break;
				default:
					$instance['configSHASESS'] = $jConfig->shared_session;
			}
		} else {
			$instance['configSHASESS'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->cache_platformprefix)) {
			switch ($jConfig->cache_platformprefix) {
				case true:
					$instance['configCACHEPLFPFX'] = 'true';
					break;
				case false:
					$instance['configCACHEPLFPFX'] = 'false';
					break;
				default:
					$instance['configCACHEPLFPFX'] = $jConfig->cache_platformprefix;
			}
		} else {
			$instance['configCACHEPLFPFX'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->ftp_enable)) {
			switch ($jConfig->ftp_enable) {
				case true:
					$instance['configFTP'] = 'true';
					break;
				case false:
					$instance['configFTP'] = 'false';
					break;
				default:
					$instance['configFTP'] = $jConfig->ftp_enable;
			}
		} else {
			$instance['configFTP'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->debug_lang)) {
			switch ($jConfig->debug_lang) {
				case true:
					$instance['configLANGDEBUG'] = 'true';
					break;
				case false:
					$instance['configLANGDEBUG'] = 'false';
					break;
				default:
					$instance['configLANGDEBUG'] = $jConfig->debug_lang;
			}
		} else {
			$instance['configLANGDEBUG'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->sef_suffix)) {
			switch ($jConfig->sef_suffix) {
				case true:
					$instance['configSEFSUFFIX'] = 'true';
					break;
				case false:
					$instance['configSEFSUFFIX'] = 'false';
					break;
				default:
					$instance['configSEFSUFFIX'] = $jConfig->sef_suffix;
			}
		} else {
			$instance['configSEFSUFFIX'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->sef_rewrite)) {
			switch ($jConfig->sef_rewrite) {
				case true:
					$instance['configSEFRWRITE'] = 'true';
					break;
				case false:
					$instance['configSEFRWRITE'] = 'false';
					break;
				default:
					$instance['configSEFRWRITE'] = $jConfig->sef_rewrite;
			}
		} else {
			$instance['configSEFRWRITE'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->proxy_enable)) {
			switch ($jConfig->proxy_enable) {
				case true:
					$instance['configPROXY'] = 'true';
					break;
				case false:
					$instance['configPROXY'] = 'false';
					break;
				default:
					$instance['configPROXY'] = $jConfig->proxy_enable;
			}
		} else {
			$instance['configPROXY'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->unicodeslugs)) {
			switch ($jConfig->unicodeslugs) {
				case true:
					$instance['configUNICODE'] = 'true';
					break;
				case false:
					$instance['configUNICODE'] = 'false';
					break;
				default:
					$instance['configUNICODE'] = $jConfig->unicodeslugs;
			}
		} else {
			$instance['configUNICODE'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->force_ssl)) {
			$instance['configSSL'] = $jConfig->force_ssl;
		} else {
			$instance['configSSL'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->session_handler)) {
			$instance['configSESSHAND'] = $jConfig->session_handler;
		} else {
			$instance['configSESSHAND'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->lifetime)) {
			$instance['configLIFETIME'] = $jConfig->lifetime;
		} else {
			$instance['configLIFETIME'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->cachetime)) {
			$instance['configCACHETIME'] = $jConfig->cachetime;
		} else {
			$instance['configCACHETIME'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->live_site)) {
			$instance['configLIVESITE'] = $jConfig->live_site;
		} else {
			$instance['configLIVESITE'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->cache_handler)) {
			$instance['configCACHEHANDLER'] = $jConfig->cache_handler;
		} else {
			$instance['configCACHEHANDLER'] = $lang['FRCA_NA'];
		}

		if (isset($jConfig->access)) {
			$instance['configACCESS'] = $jConfig->access;
		} else {
			$instance['configACCESS'] = $lang['FRCA_NA'];
		}
	}

	if ($instance['configDBTYPE'] == 'mysql' and $instance['cmsMAJORVERSION'] == '4') {
		$instance['configDBTYPE'] = 'pdomysql';
	}

	// J!1.0 assumed 'mysql' with no variable, so we'll just add it
	if ($instance['configDBTYPE'] == $lang['FRCA_N'] and $instance['configVALIDFOR'] == '1.0') {
		$instance['configDBTYPE'] = 'mysql';
	}

	// look to see if we are using a remote or local MySQL server
	if (strpos($instance['configDBHOST'], 'localhost') === 0  or strpos($instance['configDBHOST'], '127.0.0.1') === 0) {
		$database['dbLOCAL'] = $lang['FRCA_Y'];
	} else {
		$database['dbLOCAL'] = $lang['FRCA_N'];
	}

	// check if all the DB credentials are complete
	if (@$instance['configDBTYPE'] and $instance['configDBHOST'] and $instance['configDBNAME'] and $instance['configDBPREF'] and $instance['configDBUSER'] and $instance['configDBPASS']) {
		$instance['configDBCREDOK'] = $lang['FRCA_Y'];
	} else if (@$instance['configDBTYPE'] and $instance['configDBHOST'] and $instance['configDBNAME'] and $instance['configDBPREF'] and $instance['configDBUSER'] and $database['dbLOCAL'] = $lang['FRCA_Y']) {
		$instance['configDBCREDOK'] = $lang['FRCA_PMISS'];
	} else {
		$instance['configDBCREDOK'] = $lang['FRCA_N'];
	}

	// looking for htaccess (Apache and some others) or web.config (IIS)
	if ($system['sysSHORTWEB'] != 'MIC') {

		// htaccess files
		if (file_exists('.htaccess')) {
			$instance['configSITEHTWC'] = $lang['FRCA_Y'];
		} else {
			$instance['configSITEHTWC'] = $lang['FRCA_N'];
		}

		if (file_exists('administrator/.htaccess')) {
			$instance['configADMINHTWC'] = $lang['FRCA_Y'];
		} else {
			$instance['configADMINHTWC'] = $lang['FRCA_N'];
		}
	} else {

		// web.config file
		if (file_exists('web.config')) {
			$instance['configSITEHTWC'] = $lang['FRCA_Y'];
			$instance['configADMINHTWC'] = $lang['FRCA_NA'];
		} else {
			$instance['configSITEHTWC'] = $lang['FRCA_N'];
			$instance['configADMINHTWC'] = $lang['FRCA_NA'];
		}
	}
} else { // no configuration.php found

//	$instance['instanceCONFIGURED'] = $lang['FRCA_N'];
	$instance['configVALIDFOR'] = $lang['FRCA_U'];
}



/**
 * DETERMINE SYSTEM ENVIRONMENT & SETTINGS
 *
 * here we try to determine the hosting enviroment and configuration
 * to try and avoid "white-screens" fpa tries to check for function availability before
 * using any function, but this does mean it has grown in size quite a bit and unfortunately
 * gets a little messy in places.
 *
 */

// what server and os is the host?
$phpenv['phpVERSION'] = phpversion();
$system['sysPLATFUL'] = php_uname('a');
$system['sysPLATOS'] = php_uname('s');
$system['sysPLATREL'] = php_uname('r');
$system['sysPLATFORM'] = php_uname('v');
$system['sysPLATNAME'] = php_uname('n');
$system['sysPLATTECH'] = php_uname('m');
$system['sysSERVNAME'] = $_SERVER['SERVER_NAME'];
$system['sysSERVIP'] = gethostbyname($_SERVER['SERVER_NAME']);
$system['sysSERVSIG'] = $_SERVER['SERVER_SOFTWARE'];
$system['sysENCODING'] = $_SERVER["HTTP_ACCEPT_ENCODING"];
$system['sysCURRUSER'] = get_current_user(); // current process user
$system['sysSERVIP'] = gethostbyname($_SERVER['SERVER_NAME']);

// !TESTME for WIN IIS7?
// $system['sysSERVIP'] =  $_SERVER['LOCAL_ADDR'];

if ($system['sysSHORTOS'] != 'WIN') {

	$system['sysEXECUSER'] = @$_ENV['USER']; // user that executed this script

	if (!@$_ENV['USER']) {
		$system['sysEXECUSER'] = $system['sysCURRUSER'];
	}

	$system['sysDOCROOT'] = $_SERVER['DOCUMENT_ROOT'];
} else {
	$localpath = getenv('SCRIPT_NAME');
	$absolutepath = str_replace('\\', '/', realpath(basename(getenv('SCRIPT_NAME'))));
	$system['sysDOCROOT'] = substr($absolutepath, 0, strpos($absolutepath, $localpath));
	$system['sysEXECUSER'] = $system['sysCURRUSER']; // Windows work-around for not using EXEC User (this limits the cpability of discovering SU Environments though)
}

// looking for the Apache "suExec" Utility
if (function_exists('exec') and $system['sysSHORTOS'] != 'WIN') { // find the owner of the current process running this script
	$system['sysWEBOWNER'] = exec("whoami");
} elseif (function_exists('passthru') and $system['sysSHORTOS'] != 'WIN') {
	$system['sysWEBOWNER'] = passthru("whoami");
} else {
	$system['sysWEBOWNER'] = $lang['FRCA_NA'];  // we'll have to give up if we can't 'exec' or 'passthru' something, this occurs with Windows and some more secure environments
}

// find the system temp directory
if (version_compare(PHP_VERSION, '5.2.1', '>=')) {
	$system['sysSYSTMPDIR'] = sys_get_temp_dir();

	// is the system /tmp writable to this user?
	if (is_writable(sys_get_temp_dir())) {
		$system['sysTMPDIRWRITABLE'] = $lang['FRCA_Y'];
	} else {
		$system['sysTMPDIRWRITABLE'] = $lang['FRCA_N'];
	}
} else {
	$system['sysSYSTMPDIR'] = $lang['FRCA_U'];
	$system['sysTMPDIRWRITABLE'] = $lang['FRCA_U'];
}



/**
 * DETERMINE PHP ENVIRONMENT & SETTINGS
 *
 * here we try to determine the php enviroment and configuration
 * to try and avoid "white-screens" fpa tries to check for function availability before
 * using any function, but this does mean it has grown in size quite a bit and unfortunately
 * gets a little messy in places.
 *
 */

// general php related settings?
if (version_compare(PHP_VERSION, '5.0', '>=')) {
	$phpenv['phpSUPPORTSMYSQLI'] = $lang['FRCA_Y'];
} elseif (version_compare(PHP_VERSION, '4.4.9', '<=')) {
	$phpenv['phpSUPPORTSMYSQLI'] = $lang['FRCA_N'];
} else {
	$phpenv['phpSUPPORTSMYSQLI'] = $lang['FRCA_U'];
}

if (version_compare(PHP_VERSION, '7.0', '>=')) {
	$phpenv['phpSUPPORTSMYSQL'] = $lang['FRCA_N'];
} elseif (version_compare(PHP_VERSION, '5.9.9', '<=')) {
	$phpenv['phpSUPPORTSMYSQL'] = $lang['FRCA_Y'];
} else {
	$phpenv['phpSUPPORTSMYSQL'] = $lang['FRCA_U'];
}

// find the current php.ini file
if (version_compare(PHP_VERSION, '5.2.4', '>=')) {
	$phpenv['phpINIFILE']       = php_ini_loaded_file();
} else {
	$phpenv['phpINIFILE']       = $lang['FRCA_U'];
}

// find the other loaded php.ini file(s)
if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
	$phpenv['phpINIOTHER']      = php_ini_scanned_files();
} else {
	$phpenv['phpINIOTHER'] = $lang['FRCA_U'];
}

// determine the rest of the normal PHP settings
$phpenv['phpREGGLOBAL']         = ini_get('register_globals');
$phpenv['phpMAGICQUOTES']       = ini_get('magic_quotes_gpc');
$phpenv['phpSAFEMODE']          = ini_get('safe_mode');
$phpenv['phpMAGICQUOTES']       = ini_get('magic_quotes_gpc');
$phpenv['phpSESSIONPATH']       = session_save_path();
$phpenv['phpOPENBASE']          = ini_get('open_basedir');

// is the session_save_path writable?
if (is_writable(session_save_path())) {
	$phpenv['phpSESSIONPATHWRITABLE'] = $lang['FRCA_Y'];
} else {
	$phpenv['phpSESSIONPATHWRITABLE'] = $lang['FRCA_N'];
}


// input and upload related settings
$phpenv['phpUPLOADS']           = ini_get('file_uploads');
$phpenv['phpMAXUPSIZE']         = ini_get('upload_max_filesize');
$phpenv['phpMAXPOSTSIZE']       = ini_get('post_max_size');
$phpenv['phpMAXINPUTTIME']      = ini_get('max_input_time');
$phpenv['phpMAXEXECTIME']       = ini_get('max_execution_time');
$phpenv['phpMEMLIMIT']          = ini_get('memory_limit');
$phpenv['phpDISABLED']          = ini_get('disable_functions');
$phpenv['phpURLFOPEN']          = ini_get('allow_url_fopen');

// API and ownership related settings
$phpenv['phpAPI']               = php_sapi_name();

// looking for php to be installed as a CGI or CGI/Fast
if (substr($phpenv['phpAPI'], 0, 3) == 'cgi') {
	$phpenv['phpCGI'] = $lang['FRCA_Y'];

	// looking for the Apache "suExec" utility
	if (($system['sysCURRUSER'] === $system['sysWEBOWNER']) and (substr($phpenv['phpAPI'], 0, 3) == 'cgi')) {
		$phpenv['phpAPACHESUEXEC'] = $lang['FRCA_Y'];
	} else {
		$phpenv['phpAPACHESUEXEC'] = $lang['FRCA_N'];
	}

	// looking for the "phpsuExec" utility
	if (($system['sysCURRUSER'] === $system['sysEXECUSER']) and (substr($phpenv['phpAPI'], 0, 3) == 'cgi')) {
		$phpenv['phpPHPSUEXEC'] = $lang['FRCA_Y'];
	} else {
		$phpenv['phpPHPSUEXEC'] = $lang['FRCA_N'];
	}
} else {
	$phpenv['phpCGI'] = $lang['FRCA_N'];
	$phpenv['phpAPACHESUEXEC'] = $lang['FRCA_N'];
	$phpenv['phpPHPSUEXEC'] = $lang['FRCA_N'];
}




/**
 * Simplified "effective" rights testing
 * this routine is designed to try and determine if the user is, or will have, problems
 * installing extensions or uploading files due to ownershop/permission configuration
 * - only runs if an instance is found
 *
 * test a directory and the fpa script itself for "writable" status
 * - if BOTH the test items are user writable then ownership obviously isn't a problem - display NO
 * - if ONLY ONE test item is writable then we have non-standard permissions : display MAYBE
 * - else if above criteria is not met (ie: both items are NOT writable) : display YES
 *
 * then check both items for elevated permissions, which may indicate a need raise modeset to achieve access
 * assumed modeset defaults - Directory: 755, File: 644
 * - raise a warning message elevated permisions are found
 *
 * NOTE: this test routine is now independant of the suExec (Switch User) status
 * - this means the suExec (& user) status is purely informational now
 * - this caters for litespeed using setUID and custom/Cloud solutions
 * - this is a more robust method than using the presence of suExec, using the users own
 *   "effective" rights to test for ownership or permission issues
 *
 * added @RussW 04-May-2020
 *
 */


if ($instance['instanceFOUND'] == 1) {

	$dirTOTest   = 'components';
	$dirEPCheck  = substr(sprintf('%o', fileperms($dirTOTest)), -3, 3);
	$fileEPCheck = substr(sprintf('%o', fileperms(basename($_SERVER['PHP_SELF']))), -3, 3);

	if (is_writeable(basename($_SERVER['PHP_SELF'])) and is_writeable('components')) {
		$suColor     = 'success';
		$suStatus    = $lang['FRCA_N'];
		$suMSG       = 'Extension & template installations, file & image uploads should not have any problems.';
		$elevatedMSG = '';
	} elseif (!is_writeable(basename($_SERVER['PHP_SELF'])) xor !is_writeable('components')) {
		$suColor     = 'info';
		$suStatus    = $lang['FRCA_M'];
		$suMSG       = 'Extension & template installations, file & image uploads might have some problems.';
		$elevatedMSG = 'Permissions are non-standard and may cause issues.';
	} else {
		$suColor     = 'warning';
		$suStatus    = $lang['FRCA_Y'];
		$suMSG       = 'Extension & template installations, file & image uploads are likely to have problems.';
		$elevatedMSG = '';
	}

	// display a warnng message if any "actual" permissions are elevated,
	// this may indicate a need to raise modeset to make user writable
	if ((substr($dirEPCheck, 1, 1) > '5' or substr($dirEPCheck, 2, 1) > '5') or (substr($fileEPCheck, 0, 1) > '6' or substr($fileEPCheck, 1, 1) > '4' or substr($fileEPCheck, 2, 1) > '4')) {
		$elevatedMSG = 'Permissions may have been elevated to overcome access problems.';
		if ($isWINLOCAL == '1') {
			$elevatedMSG = @$elevatedMSG . ' ' . $lang['FRCA_WIN_LOCALHOST'];
		}
	}
} else {
	$suColor     = 'info';
	$suStatus    = $lang['FRCA_U'];
	$suMSG       = 'No Joomla! instance found to test';
	$elevatedMSG = '';
} // instanceFOUND, effective rights test




// get all the PHP loaded extensions and versions
foreach (get_loaded_extensions() as $i => $ext) {
	$phpextensions[$ext]    = phpversion($ext);
}

$phpextensions['Zend Engine'] = zend_version();



/**
 * DETERMINE APACHE ENVIRONMENT & SETTINGS ***********************************************
 * here we try to determine the php enviroment and configuration
 * to try and avoid "white-screens" fpa tries to check for function availability before
 * using any function, but this does mean it has grown in size quite a bit and unfortunately
 * gets a little messy in places.
 */

// general apache loaded modules?
if (function_exists('apache_get_version')) {  // for Apache module interface

	foreach (apache_get_modules() as $i => $modules) {

		$apachemodules[$i] = ($modules);  // show the version of loaded extensions

	}

	// include the Apache version
	$apachemodules[] = apache_get_version();
} else {  // for Apache cgi interface

	// !TESTME Does this work in cgi or cgi-fcgi
	/**
	 * BERNARD: commented out
	 * @todo: find out if this is even used on the webpage
	 */
	#print_r( get_extension_funcs( "cgi-fcgi" ) );
}
// !TODO see if there are IIS specific functions/modules



/**
 * COMPLETE MODE (PERMISSIONS) CHECKS ON KNOWN FOLDERS
 *
 * test the mode and writability of known folders from the $folders array
 * to try and avoid "white-screens" fpa tries to check for function availability before
 * using any function, but this does mean it has grown in size quite a bit and unfortunately
 * gets a little messy in places.
 *
 */

// build the mode-set details for each folder
if ($instance['instanceFOUND'] == 1) {

	foreach ($folders as $i => $show) {

		if ($show != $folders['ARRNAME']) { // ignore the ARRNAME

			if (file_exists($show)) {
				$modecheck[$show]['mode'] = substr(sprintf('%o', fileperms($show)), -3, 3);

				if (is_writable($show)) {
					$modecheck[$show]['writable'] = $lang['FRCA_Y'];
				} else {
					$modecheck[$show]['writable'] = $lang['FRCA_N'];
				}


				if (function_exists('posix_getpwuid') and $system['sysSHORTOS'] != 'WIN') {
					$modecheck[$show]['owner'] = posix_getpwuid(fileowner($show));
					$modecheck[$show]['group'] = posix_getgrgid(filegroup($show));
				} else { // non-posix compatible hosts
					$modecheck[$show]['owner']['name'] = fileowner($show);
					$modecheck[$show]['group']['name'] = filegroup($show);
				}
			} else {
				$modecheck[$show]['mode'] = '---';
				$modecheck[$show]['writable'] = '-';
				$modecheck[$show]['owner']['name'] = '-';
				$modecheck[$show]['group']['name'] = $lang['FRCA_DNE'];
			}
		}
	}



	// !CLEANME this needs to be done a little smarter
	// here we take the folders array and unset folders that aren't relevant to a specific release
	function filter_folders($folders, $instance)
	{
		global $folders;

		if ($instance['cmsRELEASE'] != '1.0') {           // ignore the folders for J!1.0
			unset($folders[4]);
		} elseif ($instance['cmsRELEASE'] == '1.0') {     // ignore folders for J1.5 and above
			unset($folders[3]);
			unset($folders[8]);
			unset($folders[9]);
			unset($folders[12]);
		}

		if ($instance['platformPRODUCT'] != 'Nooku') {    // ignore the Nooku sites folder if not Nooku
			unset($folders[14]);
		}
	}

	// !FIXME need to fix warning in array_filter ( '@' work-around )
	// new filtered list of folders to check permissions on, based on the installed release
	@array_filter($folders, filter_folders($folders, $instance));
}
unset($key, $show);



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



/**
 * get database info
 */
include_once ( 'frca-databasetests.php' );

/**
 * LiveCheck - FRCA
 * comment out _LIVE_CHECK_FRCA in settings to disable
 *
 * checks this FRCA version against the latest release on Github using cURL
 * - don't run if cURL disabled or not available
 *
 */
//include_once ( 'frca-dofrcalive.php' );




/**
 * LiveCheck - Joomla!
 * comment out _LIVE_CHECK_JOOMLA in settings to disable
 *
 * checks found instance version against the latest release on update.joomla.org using cURL
 * - don't run if cURL disabled or not available
 * - don't run if simpleXML or XML not available
 * - don't run if Joomla! instance not found
 * added - @RussW 28-May-2020
 *
 */
include_once ( 'frca-getjoomlalatest.php' );


function recursive_array_search($needle, $haystack)
{
	foreach ($haystack as $key => $value) {
		$current_key = $key;

		if ($needle === $value or (is_array($value) && recursive_array_search($needle, $value) !== false)) {
			return $current_key;
		}
	}
	return false;
}

?>

<?php
// TESTING
//include_once ('frca-showarrays.php');
?>

<!doctype html>
<html lang="<?php echo substr( $browserLanguage, 0, 2 ); ?>" dir="ltr" vocab="http://schema.org/">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow, noodp, nocache, noarchive" />

		<title><?php echo _RES_FRCA . ' : v' . _RES_FRCA_VERSION . ' - ' . $lang['languagecode']; ?></title>

		<?php
			if ( file_exists('templates/cassiopeia/favicon.ico') ) {
				$faviconPath	= './templates/cassiopeia/';

			} elseif ( file_exists('templates/protostar/favicon.ico') ) {
				$faviconPath	= './templates/protostar/';

			} else {
				$faviconPath	= './';
			}
		?>
		<link rel="shortcut icon" href="<?php echo $faviconPath; ?>favicon.ico" />

		<!--load the pace progress bar-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.2.3/pace.min.js" integrity="sha512-TJX3dl0dqF2pUpKKtV60kECO4y8blw4ZPIZNEkfUnFepRKfx4nfiI37IqFa1TEsRQJkPGTIK4BBJ2k/lmsK7HA==" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.2.3/themes/orange/pace-theme-flash.css" integrity="sha512-hFV95cEdxP68EQbfWPdmlaXOF2E8DYKWhcEfWqYTsFhgTZwjX/ms0LNJraxYQtAvsNfehlNLDpFpzmc5130AAA==" crossorigin="anonymous" />

		<?php
		/**
		 * use DNS prefetch to try and improve initial remote resource download TTFB time
		 * - not added the default fontawesome or various cdn's so as not to adversly effect the background connection
		 *   to the more essential data resources
		 * - not required if we are retrieving data from the user session
		 *
		 * NOTE: @RussW : testing has shown that preconnect or actual resource prefectch are way too resource hungry for
		 * the majority of the frca remote resources and just absolutely hammers the TTFB & rendering of the total page
		 *
		 * @RussW : 19-january-2021
		 *
		 */
		?>
		<?php if ( !isset($_SESSION) ) { ?>

			<!-- TODO UPDATE TO PRODUCTION URLs -->
			<link rel="dns-prefetch" href="//hotmangoteam.github.io">
			<link rel="dns-prefetch" href="//api.github.io">
			<link rel="dns-prefetch" href="//update.joomla.org">
			<link rel="dns-prefetch" href="//extensions.joomla.org">

		<?php } // end prefetch ?>

		<!--bootswatch yeti (light) & cyborg (dark) theme - bootstrap core css-->
		<?php if ( @$_POST['darkmode'] == 1 or $_SESSION['darkmode'] == 1 ) { ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.5.3/cyborg/bootstrap.min.css" integrity="sha512-QzwqVdCfEIUhmovYlJ/ET11Uh4MLuvEpwVpTVTRhChwzgfkrQH9BMcDvgwFpi5fMUGVLJAPsEXJVHuODuhmctg==" crossorigin="anonymous" />

		<?php } else { ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.5.3/yeti/bootstrap.min.css" integrity="sha512-DGK2dpsxlITD6EjNkkkWtLmdmar3HiWBRBCp5RSLvzqmqyrDVVAkCXop4I/KwtxVzqLGbeylyG8otmBax8J5UA==" crossorigin="anonymous" />

		<?php } // darkmode ?>

		<!-- fontawesome icon css -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ=" crossorigin="anonymous" />

		<!-- custom BS4 styling @RussW 23-May-2020 -->
		<style>
			html {
				position: relative;
				min-height: 100%;
			}

			body {
				font-size: 0.98rem;
				color: #666 !important;
				line-height: 1.3 !important;
				margin-top: 85px;
				scroll-behavior: smooth;
			}
			.navbar-brand h1 { font-size: 2.34375rem; font-weight: 500 !important; line-height: 1 !important; }

			.card-body-text, .problem-symptoms, .problem-effects { font-size: 0.90rem; line-height:1.2; }
			.user-messages-container::-webkit-scrollbar,
			.card-body-text::-webkit-scrollbar,
			.problem-symptoms::-webkit-scrollbar,
			.problem-effects::-webkit-scrollbar,
			.problem-action-alert::-webkit-scrollbar { width: 0.35em; }
			.user-messages-container::-webkit-scrollbar-track,
			.card-body-text::-webkit-scrollbar-track,
			.problem-symptoms::-webkit-scrollbar-track,
			.problem-effects::-webkit-scrollbar-track,
			.problem-action-alert::-webkit-scrollbar-track {  background: #f2f2f2; }
			.user-messages-container::-webkit-scrollbar-thumb,
			.card-body-text::-webkit-scrollbar-thumb,
			.problem-symptoms::-webkit-scrollbar-thumb,
			.problem-effects::-webkit-scrollbar-thumb,
			.problem-action-alert::-webkit-scrollbar-thumb { background-color: #e0e0e0; }
			.user-messages-container,
			.card-body-text,
			.problem-symptoms,
			.problem-effects,
			.problem-action-alert { scrollbar-color: auto; }

			.small {
				letter-spacing: -0.25px;
				line-height: 1.1;
				font-size: 0.85rem !important;
			}

			.xsmall {
				letter-spacing: -0.25px;
				line-height: 1.1;
				font-size: 0.7rem !important;
			}

			small {
				letter-spacing: -0.25px;
				line-height: 1.2 !important;
			}

			.protected {
				background: #f0f0f5;
				color: #b80000;
				text-transform: uppercase;
				padding: 0 5px;
				border-left: 1px solid #b80000;
				border-right: 1px solid #b80000;
				font-size: 10px;
				line-height: 1.1;
				display: inline-block;
			}

			p,
			.btn {
				font-weight: 400 !important;
			}

			.badge:not(.badge-pill),
			.btn,
			.card,
			.nav-pills .nav-link,
			.alert {
				border-radius: 0px !important;
			}
			.nav-item .badge-pill { font-size: 0.85em; font-weight: bold; min-width: 40px; top: 10px; right: 10px; border-radius: 10rem !important; }

			/* add dark purple to match other FRCA pages */
			.btn-frca,
			.alert-frca,
			.badge-frca { background-color: #593196 !important; color: #fff !important; }
			.alert-frca { border-color: #9871d3; }
			.bg-frca-dark {
				background-color: #593196 !important;
			}

			.border-frca,
			.border-frca-dark {
				border-color: #593196 !important;
			}

			.text-frca,
			.text-frca-dark {
				color: #593196;
			}

			.text-fpa-dark {
				color: #224872;
			}

			.badge-vel { background: #9e005d !important; color: #fff !important; }
			.bg-vel { background-color: #9e005d !important; }
			.text-vel { color: #9e005d !important; }

			.pdf-break-before {
				page-break-before: always;
			}

			.pdf-break-after {
				page-break-after: always;
			}

			.pace .pace-progress { height: 4px !important; background: #fbb03b !important; }
			.pace .pace-progress-inner { box-shadow: 0 0 10px #fbb03b, 0 0 5px #fbb03b; }
			.pace .pace-activity {  border-top-color: #fbb03b; border-left-color: #fbb03b; }

			<?php if (@$darkmode != 1) { ?>

			/* override default BS Yeti theme to match other FPA pages */
				h1 { color: #000; font-weight: 400; }
				h1.h2, h2, h3, h4, h5, h6 { color: #343a40; font-weight: 400; }
				.bg-info, .badge-info, .btn-info { background-color: #17a2b8 !important; }
				.border-info, .btn-info { border-color: #17a2b8 !important; }
				.btn-info:hover { color: #fff !important;background-color: #138496 !important;border-color: #117a8b !important; }
				.btn-outline-info {color: #17a2b8 !important;border-color: #17a2b8 !important; }
				.btn-outline-info:hover { color: #fff !important;background-color: #17a2b8 !important;border-color: #17a2b8 !important; }
				.text-info { color: #17a2b8 !important; }
				.alert-info {color: #0c5460 !important;background-color: #d1ecf1 !important;border-color: #bee5eb !important; }
				.bg-light { background-color: rgba(0,0,0,0.03) !important; }

				#rca-severity-tabs li a.nav-link:hover { -webkit-box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; }
				#rca-severity-tabs li a.nav-link:not(.active) { background: #fff; }
				#rca-severity-tabs li a.nav-link:not(.active) p { color: #777; }
				#rca-severity-tabs li a.nav-link.active { -webkit-box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; }
				#rca-severity-tabs li a.nav-link.active p { color:#fff !important; }

			<?php } else { // is darkmode ?>
			body,
			#confidenceHelp td,
			.form-control {
				color: #b0b0b0 !important;
			}

			h1,
			.h1 {
				font-size: 2.1875rem;
				font-weight: 400;
			}

			h2,
			.h2 {
				font-size: 1.75rem;
				font-weight: 400;
			}

			h1.h2,
			h2,
			h3,
			h4,
			h5,
			h6 {
				font-weight: 400;
			}

			.border {
				border-color: #97999c !important;
			}

			.text-muted {
				color: #888 !important;
			}

			.btn {
				padding: 0.375rem 0.5rem;
			}

			.btn,
			.btn-sm,
			.btn-lg,
			.card,
			.badge {
				border-radius: 0px !important;
			}

			.form-control,
			.form-control-sm {
				border-radius: 0px;
			}

			#confidenceHelp td {
				font-size: 0.85rem;
			}

			.bg-white {
				background-color: #424242 !important;
			}

			<?php } // end style mode
			?>

			/* increase default container widths to better suit dashboard type page */
			@media (min-width: 576px) { .container { max-width: 566px; } }
			@media (min-width: 768px) { .container { max-width: 758px; } }
			@media (min-width: 992px) { .container { max-width: 960px; } }
			@media (min-width: 1200px) { .container { max-width: 1160px; } }
			@media (min-width: 1440px) { .container { max-width: 1280px; } }

			@media print {
				.pdf-break-before {
					page-break-before: auto;
				}

				.pdf-break-after {
					page-break-after: auto;
				}

				.print-break-before {
					page-break-before: always;
				}

				.print-break-after {
					page-break-after: always;
				}

				.card-header,
				table th {
					color: #000 !important;
				}

				footer {
					border-top: 1px solid #000;
				}
			}
		</style>

		<meta name="theme-color" content="#593196">

	</head>
	<body data-spy="scroll" data-target=".navbar" data-offset="85" id="frca-body">


			<?php
			/**
			 * FRCA now does some of the heavy-lifting and remote service work in the page body to ensure that some of
			 * the page is displayed whilst loading and the pace progress bar better reflects that something is happening,
			 * not just a white page whilst downloading remote resources and doing some of the tests
			 *
			 * @RussW 17-january-2021
			 *
			 * FRCA & JOOMLA LIVE CHECKS
			 * do frca and joomla live version checks
			 *
			 */
			?>
		<?php
			if ( defined('_LIVE_CHECK_FRCA') and ( isset($candoFRCACHECK) and $candoFRCACHECK = 1 ) ) {

				///include_once ( 'frca-dofrcalive.php' );
				doFRCALIVE();

				/**
				 * check the FRCA version status
				 * - display a download link if out-of-date or a message if a development version
				 *
				 */
				if ( isset($frcaversionARRAY) and !empty($frcaversionARRAY) ) {

					$frcaversionState	= 0;  // assume out-of-date (0 = out-of-date, 1 = Development, 2 = current)
					$thisFRCAVER		= _RES_FRCA_VERSION;
					// TESTING:
					//$thisFRCAVER		= '1.6.0';

					// get the latest FRCA release version
					if ( substr($frcaversionARRAY['tag_name'], 0, 1) == 'v' ) {

						$latestFRCAVER	= ltrim( $frcaversionARRAY['tag_name'], 'v' );  // trim the "v" (version) from the latest release tag

					} else {

						$latestFRCAVER	= $frcaversionARRAY['tag_name'];

					}

					// compare the latest version with this version
					if ( version_compare( $thisFRCAVER, $latestFRCAVER ) < 0 ) {

						$frcaversionState			= 0;  // is out-of-date
						$frcaversionCheckStatus		= 'white';
						$frcaversionCheckIcon		= 'cloud-download-alt';
						$frcaversionCheckMessage	= $lang['VER_CHECK_DWNLD'] .' (v'. $latestFRCAVER .')';
						$frcaversionCheckDownload	= $frcaversionARRAY['html_url'];

						// post a $userMessage and encourage to update with new improvements info added at the end
						$thisMessage				= '<i class="fas fa-'. $frcaversionCheckIcon .' text-warning fa-fw"></i> '. _RES_FRCA .' '. $lang['VER_CHECK_ATOLD'];

						// raise a ProblemCode
						getPDC( '1', '0005' );

						// TESTING
						//var_dump($problemList);

					} elseif (version_compare( $thisFRCAVER, $latestFRCAVER ) > 0 ) {

						$frcaversionState			= 1;  // is a development release
						$frcaversionCheckStatus		= 'white';
						$frcaversionCheckIcon		= 'exclamation-circle';
						$frcaversionCheckMessage	= 'v'. $thisFRCAVER .' '. $lang['VER_CHECK_ATDEV'];
						$frcaversionCheckDownload	= '';

						// post a $userMessage and encourage to update with new improvements info added at the end
						$thisMessage			    = '<i class="fas fa-'. $frcaversionCheckIcon .' text-info fa-fw"></i> '. _RES_FRCA .' '. $lang['VER_CHECK_ATDEV'];

					} else {

						// is up-to-date, only posts a $userMessage (not on navbar)
						$frcaversionState			= 3;  // is current (up-to-date) nothing displayed to user
						$frcaversionCheckStatus		= 'success';
						$frcaversionCheckIcon		= 'check-circle';
						$frcaversionCheckMessage	= $thisFRCAVER .' '. $lang['VER_CHECK_ATCUR'];
						$frcaversionCheckDownload	= '';

						// post a $userMessage
						$userMessages[]				= '<i class="fas fa-'. $frcaversionCheckIcon .' text-success fa-fw"></i> '. _RES_FRCA .' '. $lang['VER_CHECK_ATCUR'];

					}

					// if out-of-date or a development version, encourage the user to update to a
					// released version with the additional latest improvements information
					if ( $frcaversionState < 3 ) {

						// add the new release information, if available, explode the "body" element by the markdown list character (* )
						if ( isset($frcaversionARRAY['body']) and !empty($frcaversionARRAY['body']) ) {
							$thisPieces				= explode( '* ', $frcaversionARRAY['body'] );
						}

						$thisMessage 			   .= '<br /><strong class="ml-1 xsmall">v'. $latestFRCAVER .' '. $lang['VER_CHECK_RELEASE'] .' '. $lang['VER_CHECK_IMPROVEMENTS'] .'</strong>';
						$thisMessage 			   .= '<ul class="xsmall">';
						foreach ( $thisPieces as $thisLine ) {
							if ( !empty($thisLine) ) {
								// trim the exploded result of whitespace, /r & /n
								$thisMessage			   .= '<li class="pb-1" style="line-height:1.1;">'. trim( $thisLine ) .'</li>';
							}
						}
						$thisMessage			   .= '</ul>';

						// convert the temp message structure to the final $userMessage output
						$userMessages[]				= $thisMessage;

						// tidy up after ourselves
						unset ( $thisPieces );
						unset ( $thisMessage );

					} // userMessage encouragement

				}  // end $frcaversionARRAY

			} // end candoFRCACHECK


			// get the latest Joomla release information
			if ( defined( '_LIVE_CHECK_JOOMLA' ) and $candoJOOMLACHECK == 1 and $instance['instanceFOUND'] == 1 ) {

				doJOOMLALIVE( $thisJVER );

				// TESTING
				//echo '-[thisJVER: '. $thisJVER .']- -[latestJVER: '. $latestJVER .' ]-';

			}
		?>


		<header>

			<nav class="navbar navbar-expand-sm navbar-dark bg-frca-dark py-1 fixed-top shadow d-print-none" data-html2canvas-ignore="true">
				<div class="container-fluid">

					<a class="navbar-brand mr-0 mr-md-2 text-white py-2" href="<?php echo _FRCA_SELF; ?>" aria-label="<?php echo _RES_FRCA; ?>">

						<!-- get from project git-pages branch-->
						<?php
							$isFLogo = ('https://hotmangoteam.github.io/Fishikawatest/resources/frca-logo.svg');
							$header_response = get_headers( $isFLogo, 1 );
							if ( strpos( $header_response[0], '404' ) === false ) {
						?>
							<img class="d-none d-md-inline-block mt-n2 mr-3 img-fluid" src="<?php echo $isFLogo; ?>" width="75" height="auto" style="float:left;" />
						<?php } ?>

						<!--<img class="d-none d-md-inline-block mt-n2 mr-3 img-fluid" src="https://hotmangoteam.github.io/Fishikawatest/resources/frca-logo.svg" width="75" height="auto" style="float:left;" />-->


						<h1 class="d-inline-block d-sm-none text-white" aria-hidden="true"><?php echo _RES_FRCA_SHORT; ?></h1>
						<h1 class="d-none d-sm-block mb-0 text-white">
							<?php echo _RES_FRCA; ?>
						</h1>
						<span class="d-none d-sm-inline-block text-white small">
							<?php echo _RES_FRCA_GRAB; ?>
						</span>
						<span class="d-block d-sm-inline-block ml-1 small"><?php echo 'v' . _RES_FRCA_VERSION . ' (' . _RES_FRCA_CODENAME; ?>)</span>
					</a>
					<!--/.navbar-brand-->

					<div class="ml-md-auto justify-content-end">
						<ul class="navbar-nav flex-row justify-content-end ml-md-auto">

							<?php
							/**
							 * userMessages
							 * to avoid cluttering up the "notices" area below the header, less important and informational messages
							 * can be displayed here using the $userMessages array
							 *
							 * added 16-january-2021 @RussW
							 */
							?>
							<?php
								// TESTING
								//unset ( $userMessages );

								if ( isset($userMessages) and count($userMessages) > 0 ) {

									$userMessageButton = 'primary';

								} else {

									$userMessageButton = 'light';

								}
							?>
							<li class="nav-item dropdown py-2 mr-3 d-none d-sm-inline-block">

								<button class="btn btn-<?php echo $userMessageButton; ?> position-relative p-1 1dropdown-toggle" type="button" id="dropdownUserMessages" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="FRCA User Messages" style="width:32px;height:32px;border-radius:50%!important;">
									<?php if ( isset($userMessages) and count($userMessages) > 0 ) { ?>
										<span class="badge badge-pill badge-warning xsmall text-center p-1 position-absolute" style="top:-6px;left:-10px;min-width:20px;max-width:20px;height:20px;"><?php echo count($userMessages); ?></span>
									<?php } ?>
									<i class="fas fa-envelope lead"></i>
								</button>

								<div class="dropdown-menu dropdown-menu-right pt-0 pb-0 shadow-lg" aria-labelledby="dropdownUserMessages" style="width:350px;">
									<span class="p-2 d-block 1dropdown-item-text 1list-group-item- bg-primary 1border 1border-white text-white"><i class="fas fa-envelope fa-fw"></i> Fishikawa Messages</span>
									<div class="user-messages-container" style="overflow-y:auto;max-height:55vh;">
										<?php if ( isset($userMessages) and count($userMessages) > 0 ) { ?>

											<ul class="list-group list-group-flush">
												<?php foreach ( $userMessages as $message ) { ?>

													<li class="list-group-item list-group-item-action"><?php echo $message; ?></li>

												<?php } // foreach $userMessages ?>
											</ul>

										<?php } else { ?>

											<div class="p-3 text-muted text-center small">No Messages</div>

										<?php } // end $userMessages ?>
									</div><!--scroll-box-->
								</div><!--dropdown-menu-->
							</li><!--end $userMessage dropdown-->



							<?php if ( isset( $browserLanguage ) and substr( $browserLanguage, 0, 2 ) != 'en' ) { ?>

								<li class="nav-item py-2 d-none d-md-inline-block" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="Reset Localisation" data-content="Force FRCA to use English (en-GB) not the detected browser language. (Reset back to defaults to clear this option.)">
									<form class="m-0 ml-auto p-0" method="post" name="navFORCEENForm" id="navFORCEENForm">
										<input type="hidden" name="force_en" value="1" />
										<button class="btn btn-outline-warning mr-3" type="submit" aria-label="Force the FRCA localisation to be English" style="height:32px;">
											<i class="fas fa-language fa-lg 1lead"></i>
										</button>
									</form>
								</li><!--/force_en language-->

								<?php //$browserLanguage = 'en-GB'; ?>

							<?php } // force_en ?>



							<?php
							/**
							 * problem layoutview mode
							 *
							 * compact  : 1x issue per grid column + carousel and multiple issue info items
							 * expanded : 1x issue per grid row + 1x card per issue info block
							 * use PHP_SESSION to maintain user choice
							 * added @RussW 16 Dec 2020
							 */
							?>
							<?php
								if ($layoutview == 'e') {

									$layoutCOMPACTACTIVE   = '';
									$layoutCOMPACTCHECKED  = '';
									$layoutCOMPACTBTN      = 'outline-secondary';
									$layoutEXPANDEDACTIVE  = 'active';
									$layoutEXPANDEDCHECKED = 'checked';
									$layoutEXPANDEDBTN     = 'info';

								} else {

									$layoutCOMPACTACTIVE   = 'active';
									$layoutCOMPACTCHECKED  = 'checked';
									$layoutCOMPACTBTN      = 'info';
									$layoutEXPANDEDACTIVE  = '';
									$layoutEXPANDEDCHECKED = '';
									$layoutEXPANDEDBTN     = 'outline-secondary';

								}
							?>
							<li class="nav-item py-2 d-none d-lg-inline-block" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="View Layout" data-content="Switch the problem discovery layout between compact and expanded views.">
								<form class="m-0 ml-auto p-0" method="post" name="layoutviewForm" id="layoutviewForm">

									<div class="btn-group btn-group-toggle mr-1" data-toggle="buttons">

										<label class="btn btn-<?php echo $layoutCOMPACTBTN; ?> <?php echo $layoutCOMPACTACTIVE; ?>">
											<input type="radio" name="layoutview" value="c" onclick="document.getElementById('layoutviewForm').submit()" id="layoutviewCOMPACT" <?php echo $layoutCOMPACTCHECKED; ?>> <i class="fas fa-th-large lead"></i>
										</label>

										<label class="btn btn-<?php echo $layoutEXPANDEDBTN; ?> <?php echo $layoutEXPANDEDACTIVE; ?>">
											<input type="radio" name="layoutview" value="e" onclick="document.getElementById('layoutviewForm').submit()" id="layoutviewEXPANDED" <?php echo $layoutEXPANDEDCHECKED; ?>> <i class="fas fa-th-list lead"></i>
										</label>

									</div>
								</form>
							</li><!--/layoutview-->


							<!-- reset and clear session data (forces fresh download of vel-data and pda-data)-->
							<li class="nav-item py-2 d-none d-md-inline-block" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="Reset FRCA To Defaults" data-content="Reset views to defaults and download fresh pda, vel & language data (accesskey = [control] alt + r)">
								<form class="m-0 ml-auto p-0" method="post" name="navRESETForm" id="navRESETForm">
									<input type="hidden" name="doRESET" value="1" />
									<button class="btn btn-outline-light mr-1" type="submit" accesskey="r" aria-label="Reset all session data back to default and download fresh pda & vel data">
										<i class="fas fa-eraser lead"></i>
									</button>
								</form>
							</li>


							<!--print to PDF-->
							<li class="nav-item py-2 d-none d-md-inline-block" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="Print FPA Report to PDF" data-content="Print to PDF the current FPA snapshot and discovery report.">
								<form class="m-0 ml-auto p-0" method="post" name="navPDFForm" id="navPDFForm">
									<input type="hidden" name="doPDF" value="1" />
									<button class="btn btn-outline-light mr-1" type="submit" accesskey="p" aria-label="Produce a PDF document of the FPA Report">
										<i class="fas fa-file-pdf lead"></i>
									</button>
								</form>
							</li>


							<!--download latest FRCA
							<?php
								//if ( isset($latestFRCAVER) and !empty($latestFRCAVER) ) {
									// TODO: change icon colour if out of date
								//}
							?>
							<li class="nav-item py-2 d-none d-md-inline-block" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="Download FPA" data-content="<?php echo $lang['FRCA_FPALATEST2']; ?>">
								<a class="btn btn-info 1mr-1" href="<?php echo _RES_FPALINK2; ?>" rel="noreferrer noopener" target="_blank" role="button" aria-label="<?php echo $lang['FRCA_FPALATEST2']; ?>">
									<i class="fas fa-cloud-download-alt lead"></i>
								</a>
							</li>
							-->


							<!-- Guided FRCA Tour
							<li class="nav-item py-2 d-none d-md-inline-block" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="FPA Guided Tour" data-content="View the FPA guided tour and learn how to reead and use the FPA">
								<a class="btn btn-primary" href="#" role="button" aria-label="View the FPA guided tour">
									<i class="fas fa-shoe-prints lead"></i>
								</a>
							</li>
							-->


							<!--darkmode-->
							<?php if ( @$darkmode == '0' ) { ?>

								<li class="nav-item py-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="Switch To Night Mode" data-content="View FPA in Night Mode (accesskey = [control] alt + n)">
									<form class="m-0 ml-auto p-0" method="post" name="navDARKForm" id="navDARKForm">
										<input type="hidden" name="darkmode" value="1" />
										<button class="btn btn-dark" type="submit" accesskey="n" aria-label="Night Mode">
											<i class="fas fa-moon lead"></i>
										</button>
									</form>
								</li>

							<?php } else { ?>

								<li class="nav-item py-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="Switch To Light Mode" data-content="View FPA in Light Mode (accesskey = [control] alt + l)">
									<form class="m-0 ml-auto p-0" method="post" name="navDARKForm" id="navDARKForm">
										<input type="hidden" name="darkmode" value="0" />
										<button class="btn btn-dark" type="submit" accesskey="l" aria-label="Light Mode">
											<i class="fas fa-sun lead"></i>
										</button>
									</form>
								</li>

							<?php } // darkmode ?>


							<!--got to docs-->
							<li class="nav-item py-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="FPA Documentation" data-content="Visit the FPA documentation site on Github">
								<a class="1nav-link btn btn-info" href="https://forumpostassistant.github.io/docs/" rel="noreferrer noopener" target="_blank" role="button" aria-label="Visit the FPA documentation site on Github">
									<i class="fas fa-book-reader lead"></i>
								</a>
							</li>


							<!--SPARE
							<li class="nav-item py-2 border-right d-none d-md-inline-block mr-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-content="Print the current FPA audit report">
								<a class="btn btn-info lead mr-2" href="#" role="button" aria-label="Print the current FPA audit report">
									<i class="fas fa-print lead"></i>
								</a>
							</li>
							-->

							<!--delete FRCA-->
							<li class="nav-item py-2" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-fallbackPlacement="flip" data-title="FRCA Security Notice" data-content="Delete the FRCA script now (accesskey = [control] alt + d)">
								<form class="m-0 ml-auto p-0" method="post" name="navDELForm" id="navDELForm">
									<input type="hidden" name="act" value="delete" />
									<button class="btn btn-danger ml-1" type="submit" accesskey="d" aria-label="Delete FRCA now">
										<i class="fas fa-trash-alt lead"></i>
									</button>
								</form>
							</li>

						</ul><!--/.navbar-nav-->


						<!--FRCA version status-->
						<?php if ( isset($frcaversionARRAY) and isset($frcaversionState) ) { ?>

							<?php if ( $frcaversionState == 0 ) { // out-of-date ?>

								<a href="<?php echo $frcaversionCheckDownload; ?>" class="text-white float-right d-none d-sm-block"><i class="fas fa-<?php echo $frcaversionCheckIcon; ?>"></i> <?php echo $frcaversionCheckMessage; ?></a>

							<?php } elseif ( $frcaversionState == 1 ) { // development version ?>

								<span class="text-light float-right d-none d-sm-block"><i class="fas fa-<?php echo $frcaversionCheckIcon; ?>"></i> <?php echo $frcaversionCheckMessage; ?></span>

							<?php } ?>

						<?php } ?>

					</div>

				</div>
			</nav>


			<?php
			/**
			 * print only header
			 * display some heading info when printed only
			 *
			 */
			?>
			<div class="container d-none d-print-block border-bottom border-dark" id="printHeader">
				<div>
					<h2 class="h1 font-weight-lighter mb-1">
						<span class="xsmall text-right float-right">
							<?php echo date('jS F Y'); ?><br />
							<?php echo date('g:i a'); ?><br />
							<?php echo date('e'); ?>
						</span>

						<?php
							if (!empty($config->sitename)) {
								echo $config->sitename;
							}
						?>
					</h2>
					<h3 class="h4">
						<?php echo $_SERVER['HTTP_HOST']; ?>
					</h3>
				</div>
			</div>

		</header>



		<?php
		/**
		 * FRCA NOTICES & MESSAGES
		 * display any development, debug or important messages
		 * if there is anything important to show based on settings, options or testing, this is the place
		 *  - otherwise, less important or informational messges, use $userMessages array
		 *
		 * @RussW - 11-January-2021
		 */
		?>

	<!-- MESSAGES TO SELF -->
	<!--TESTING: variable, variables
	<div class="alert alert-danger text-center 1px-1 1py-2 m-0 d-print-none">

		TODO: CONVERT cURL GETS TO USE VARIABLE;VARIABLES ( getREMOTEJSON('pdadata|veldata|translation|frcaversion'); )<br />
		TODO: CONVERT simpleXML GETS TO USE VARIABLE;VARIABLES ( getREMOTEXML('jversion'); )<br />
	-->
		<?php
		function testdoREMOTEJSON($type) {
			//$varName = $css."_".$chosen_menu;
			//$$varName = "value";

			// send the data "type" to the function depending on the data we're retrieving
			//$type = 'pda';
			//$type = 'vel';
			//$type = 'translation';
			//$type = 'frcaversion';
			//$type = 'jversion';  will be in a simpleXML function not cURL

			// we make use of "variable, variables" to cater for different json data being retrieved and output to frca
			$testARRAY = $type.'testARRAY';
			$$testARRAY[] = 'test one';
			$$testARRAY[] = 'test two';

			// NOTE: this returns a variable/array name reflecting the "type" paramater
			// EG: $$testARRAY will equal;
			// $pdatestARRY | $veltestARRAY | $trnslationtestARRAY | $frcatestARRAY
			// allowing the same function to be used for all json data types but returning a
			// unique array name depending on the data retrieved

			// test both native & variable/variable array
			//var_dump($jversiontestARRAY);
			///var_dump($$testARRAY);

		} // end function
		testdoREMOTEJSON('jversion');
		?>

	</div>
	<!--TESTING

	<div class="alert alert-danger text-center 1px-1 1py-2 m-0 d-print-none">
		REMINDER: USE $candoXYX for variable names when setting action criteria<br />
		REMINDER: USE getREMOTEXYZ for function names when getting remote data<br />
		REMINDER: USE doXYXCHECKS for function names when doing somehting on page
	</div>

 /MESSAGES TO SELF -->


		<?php if ( $candoREMOTE != '1' ) { ?>

			<div class="alert alert-danger text-center px-1 py-2 m-0 small d-print-none">
				<i class="fas fa-minus-circle fa-fw"></i> <?php echo $lang['FRCA_DOREMOTE_ERROR']; ?>
			</div>

		<?php } // end no cURL message ?>


		<?php if ( !isset($hasSSL) ) { ?>

			<div class="alert alert-info text-center p-1 m-0 small d-print-none">
				<i class="fas fa-unlock-alt fa-fw"></i> <?php echo $lang['FRCA_SSLDSC']; ?>
			</div>

		<?php } // end noSSL message ?>


		<?php if ( isset($hasPROXY) ) { ?>

			<div class="alert alert-info text-center p-1 m-0 small d-print-none">
				<i class="fas fa-server fa-fw"></i> <?php echo $lang['FRCA_PRXDSC']; ?>
			</div>

		<?php } // end hasProxy message?>


		<?php if ( $translationError == '1' ) { ?>

			<div class="alert alert-info text-center px-1 p-1 m-0 small d-print-none">
				<i class="fas fa-comment-slash fa-fw"></i> <?php echo $lang['FRCA_TRANSLATE_ERROR']; ?>
			</div>

		<?php } // end no translation message ?>


		<?php if ( defined('_FRCA_DEV') or defined('_FRCA_DBG') ) { ?>

			<?php defined('_FRCA_DEV') and defined('_FRCA_DBG') ? $colSpan = '6' : $colSpan = '12'; ?>

			<div class="container-fluid py-3">
				<div class="row no-gutters d-print-none" id="frca-notices" data-html2canvas-ignore="true">

					<?php if ( defined('_FRCA_DBG' )) { ?>

						<div class="col-sm-<?php echo $colSpan; ?> d-flex align-items-stretch  mb-2">

							<div class="alert alert-primary flex-fill d-print-none">
								<h6 class="text-white m-0 p-0 text-capitalize"><?php echo $lang['FRCA_DBGENA']; ?></h6>
								<span><?php echo $lang['FRCA_DBGDSC']; ?></span>
							</div>

						</div>

					<?php } // end debug-mode display ?>


					<?php if ( defined('_FRCA_DEV' )) { ?>

						<div class="col-sm-<?php echo $colSpan; ?> d-flex align-items-stretch mb-2">

							<div class="alert alert-frca flex-fill d-print-none">

								<div class="d-inline-block float-right">
									<button class="btn btn-ouline-link btn-sm" type="button" data-toggle="collapse" data-target="#showfrcaDEV" aria-expanded="false" aria-controls="showfrcaDEV">
										<i class="fab fa-dev fa-2x text-white"></i>
									</button>
								</div>
								<h6 class="text-white m-0 p-0 text-capitalize"><?php echo $lang['FRCA_DEVENA']; ?></h6>
								<span><?php echo $lang['FRCA_DEVDSC']; ?></span>

							</div>

						</div>

					<?php } // end developer-mode display ?>


					<!--showDEV content collapse-->
					<div class="collapse w-100" id="showfrcaDEV">

						<div class="row">
							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">frca Configuration</h6>

									Debug Mode: <span class="float-right"><?php echo defined('_FRCA_DBG') ? '<span class="text-success">'. $lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span><br />

									Self Destruct: <span class="float-right"><?php echo defined('_FRCA_SELF_DESTRUCT') ? '<span class="text-success">'. $lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span><br />
									<?php if ( defined('_FRCA_SELF_DESTRUCT') and ( isset($candoSELFDESTRUCT) and $candoSELFDESTRUCT == 0 ) ) { ?>
										<span class="float-right xsmall text-warning">Conditionally Disabled</span><br />
									<?php } ?>
									<?php if ( defined('_FRCA_SELF_DESTRUCT') and ( isset($candoSELFDESTRUCT) and $candoSELFDESTRUCT == 1 ) ) { ?>
										<span class="float-right xsmall">Age: <?php echo _FRCA_SELF_DESTRUCT_AGE; ?> days</span><br />
									<?php } ?>

									SSL Redirect: <span class="float-right"><?php echo defined('_FRCA_SSL_REDIRECT') ? '<span class="text-success">'. $lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span><br />
									<?php if ( defined('_FRCA_SSL_REDIRECT') and ( isset($candoSSLREDIRECT) and $candoSSLREDIRECT == 0 ) ) { ?>
										<span class="float-right xsmall text-warning">Conditionally Disabled</span><br />
									<?php } ?>

									Language Detect: <span class="float-right"><?php echo defined('_FRCA_LANG_DETECT') ? '<span class="text-success">'. $lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span><br />
									<?php if ( defined('_FRCA_LANG_DETECT') and ( isset($candoGETLANG) and $candoGETLANG == 0 ) ) { ?>
										<span class="float-right xsmall text-warning">Conditionally Disabled</span><br />
									<?php } ?>
									<?php if ( defined('_FRCA_LANG_DETECT') and ( isset($candoGETLANG) and $candoGETLANG == 1 ) ) { ?>
										<span class="float-right xsmall">Detected: <?php echo $browserLanguage; ?></span><br />
										<?php if ( isset($_SESSION['force_en']) and $_SESSION['force_en'] == 1 ) { ?>
											<span class="float-right xsmall">Forced: <?php echo $lang['languagecode']; ?></span><br />
										<?php } else { ?>
											<span class="float-right xsmall">Using: <?php echo $lang['languagecode']; ?></span><br />
										<?php } ?>
									<?php } ?>

									Include VEL: <span class="float-right"><?php echo defined('_FRCA_USE_VEL') ? '<span class="text-success">'. $lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span><br />
									<?php if ( defined('_FRCA_USE_VEL') and ( isset($candoGETVEL) and $candoGETVEL == 0 ) ) { ?>
										<span class="float-right xsmall text-warning">Conditionally Disabled</span><br />
									<?php } ?>

									FRCA Live Check: <span class="float-right"><?php echo defined('_LIVE_CHECK_FRCA') ? '<span class="text-success">'. $lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span><br />
									<?php if ( defined('_LIVE_CHECK_FRCA') and ( isset($candoFRCACHECK) and $candoFRCACHECK == 0 ) ) { ?>
										<span class="float-right xsmall text-warning">Conditionally Disabled</span><br />
									<?php } ?>

									Joomla! Live Check: <span class="float-right"><?php echo defined('_LIVE_CHECK_JOOMLA') ? '<span class="text-success">'.$lang['FRCA_ENA'] .'</span>' : $lang['FRCA_DIS']; ?></span>
									<?php if ( defined('_LIVE_CHECK_JOOMLA') and ( isset($candoJOOMLACHECK) and $candoJOOMLACHECK == 0 ) ) { ?>
										<span class="float-right xsmall text-warning">Conditionally Disabled</span><br />
									<?php } ?>
								</div>

							</div>
							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">session Data</h6>
									<?php
										if ( isset($_SESSION) ) {

											foreach ( $_SESSION as $sessionkey => $sessionvalue ) {
												if ( $sessionkey == 'veldata' or $sessionkey == 'pdadata' ) {

													echo $sessionkey .'<span class="float-right">['. count($sessionvalue) .' records]</span><br />';

												} elseif ( $sessionkey == 'translation' ) {

													echo 'translation<span class="float-right">['. strlen( $_SESSION['translation'] ) .' bytes]</span><br />';

												} elseif ( $sessionkey == 'latestfrca' ) {

													echo 'latestfrca<span class="float-right">['. $_SESSION['latestfrca']['tag_name'] .']</span><br />';

												} else {

													echo $sessionkey .'<span class="float-right">['. $sessionvalue .']</span><br />';

												} // foreach $_SESSION

											} // if $_SESSION

										} else {

											echo 'No Session data available';

										} // isset($_SESSION)
									?>
								</div>

							</div>
							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">pdadata Retrieval</h6>
									<?php
										echo @$pdadevMSG;
									?>
								</div>

							</div>
							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">veldata Retrieval</h6>
									<?php
										echo @$veldevMSG;
									?>
								</div>

							</div>
							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">translation Retrieval</h6>
									<?php
										echo @$langdevMSG;
									?>
								</div>

							</div>


							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">
								<?php
									if ( isset($updatelang) ) {
										$untranslatedStrings	= array_diff( $lang, $updatelang );
										$untranslatedCount		= count( $untranslatedStrings );
									} else {
										$untranslatedCount		= '0';
									}
								?>

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">untranslated Strings <span class="badge badge-pill badge-primary"><?php echo $untranslatedCount; ?></span></h6>

									<?php if ( $untranslatedCount > '0' ) { ?>
										<div class="mt-1" style="overflow:auto;max-height:200px;">
											<ul class="list-group xsmall">
												<?php
													foreach ( $untranslatedStrings as $stringkey => $stringvalue ) {
														echo '<li class="list-group-item px-1 py-2"><strong>'. $stringkey .' =</strong> '. $stringvalue .'</li>';
													}
												?>
											</ul>
										</div>
									<?php
										} else {
											echo 'None';
										}
									?>

								</div>

							</div>



							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">latestfrca Retrieval</h6>
									<?php
										echo @$frcadevMSG;
									?>
								</div>

							</div>
							<div class="col-sm-6 col-lg-3 d-flex align-items-stretch mb-2">

								<div class="alert bg-white text-dark border border-frca shadow flex-fill">
									<h6 class="text-frca m-0 p-0">latestjoomla Retrieval</h6>
									<?php
										echo @$joomladevMSG;
									?>
								</div>

							</div>
						</div><!--/.row-->

					</div><!-- /.collapse #showfrcaDEV-->

				</div><!--/.row #frca-notices-->
			</div><!--/.container-->

		<?php } // end DEV & DBG notices ?>



	<?php
	/**
	 * SEEING A WHITE SCREEN WHILST RUNNING FPA? OR SOMEONE HELPING YOU SENT YOU HERE?
	 * uncomment _FRCA_DEV or _FRCA_DBG in Default Settings to enable and re-run FPA
	 *
	 * display_errors, enables php errors to be displayed on the screen
	 * error_reporting, sets the level of errors to report, "-1" is all errors
	 * log_errors, enables errors to be logged to a file, fpa_error.log in the "/" folder
	 *
	 * moved inside body to avoid page layout errors - @RussW 27-May-2020
	 *
	 */
	//if ( defined('_FRCA_DEV') or defined('_FRCA_DBG') or @$_SERVER['HTTPS'] != 'on' ) {
	?>
	<!--
		<div class="alert alert-warning 1text-white text-center p-0 m-0 d-print-none" id="frca-notices" data-html2canvas-ignore="true">

			<?php
				// display developer-mode notice(d)
				if ( defined('_FRCA_DEV' )) {

					echo '<h4 class="text-white m-1 p-0 text-capitalize">'. $lang['FRCA_DEVENA'] .'</h4>';

				} // end developer-mode display


				// display debug-mode notice
				if ( defined('_FRCA_DBG') ) {

					echo '<h4 class="text-white m-1 p-0 text-capitalize">' . $lang['FRCA_DBGENA'] . '</h4>';

				} // end debug-mode display


				// display SSL and/or Proxy Notice
				if (@$_SERVER['HTTPS'] != 'on') {

					echo '<p class="pt-1 mb-1 w-75 mx-auto"><i class="fas fa-unlock-alt fa-fw"></i> SSL may not be available for this site, it is recommended that SSL is used on all sites where possible.</p>';

				} // end SSL & Proxy display
			?>

		</div>
		-->
		<!--/.alert DEV/DBG/SSL & PROXY-->

	<?php
	//} else { // end developer- or diag -mode display
	//	ini_set('display_errors', 0); // default-display
	//}
	?>

<!--TESTING

<?php
$url = 'https://www.hotmango.me';
?>
<pre>
<?php
print_r(get_headers($url), 1);
?>
</pre>
<hr />
<pre>
<?php
print_r(apache_response_headers());
//print_r(get_headers($url, 1));
?>
</pre>
<hr />
<pre>
<?php
print_r(headers_list());
//print_r(apache_response_headers());
//print_r(get_headers($url, 1));
?>
</pre>
<hr />
<pre>
<?php
///print_r (dns_get_record('www.hotmango.me', DNS_ALL, $authns, $addtl));
?>
</pre>
<hr />
<pre>
<?php
//print_r(get_resources());
print_r(get_resources('stream'));
///print_r (checkdnsrr(idn_to_ascii('www.hotmango.me'), 'TXT'));
?>
</pre>
-->

		<main class="main">


		<!-- TESTING frca.json -->
<?php // include_once ('frca-getpdadata.php'); ?>








		<?php
/*
			if ( isset($_SESSION['pdaData']) ) {
				$pdaITEMARRAY   = $_SESSION['pdaData'];

				echo '<div class="bg-info text-center text-white">DEV MESSAGE : USING SESSION PDA DATA</div>';

			} elseif ( file_exists('frca-pdadata-tmp.json') ) {
				// get test data
				$get_pdadata	= file_get_contents('frca-pdadata-tmp.json');
				$pdajsonARRAY	= json_decode($get_pdadata, true);
				$pdaITEMARRAY   = $pdajsonARRAY['dataset']['pdc'];


				echo '<div class="bg-info text-center text-white">DEV MESSAGE : USING LOCAL PDA DATA</div>';

			} else {

			$randCacheBuster = mt_rand();  // break GitHub CDN/Browser Caching with a randon number added to the request
			$pdacURL     = 'https://hotmangoteam.github.io/Fishikawatest/pdadata/frca-pdadata.json?'.$randCacheBuster;  // FRCA PDA json feed URL
			$ch          = curl_init( $pdacURL );  // init cURL
			$pdacURLOPT  = array ( CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
								CURLOPT_TIMEOUT => 5,
								CURLOPT_CONNECTTIMEOUT => 5,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_HTTPHEADER => array('Content-type: application/json'),
								);
			curl_setopt_array( $ch, $pdacURLOPT );

			$pdacURLJSON  = curl_exec($ch); // get json result string

            if ($pdacURLJSON === false) {
				echo 'PDACONNECT ERROR';
				// TODO: disable if in error
				echo '<div class="bg-info text-center text-white">DEV MESSAGE : COULD GET LIVE & NO LOCAL PDA DATA</div>';

			} else {

				if (!$_SESSION) {
					session_start();
				}
				$_SESSION['pdaData'] = json_decode($pdacURLJSON, true);  // decode json in to an array

				if ( !isset($_SESSION['pdaData']) ) {

					$fp = fopen('frca-pdadata-tmp.json', 'w');
					fwrite($fp, $pdacURLJSON);
					fclose($fp);
					// TODO: drop an error if can't write

					$pdajsonARRAY	= json_decode($pdacURLJSON, true);  // decode json in to an array

					echo '<div class="bg-info text-center text-white">DEV MESSAGE : GOT LIVE PDA TEST DATA & WROTE TMP FILE</div>';
				}

				$pdaITEMARRAY   = $pdajsonARRAY['dataset']['pdc'];

			}

		}

		//var_dump($pdajsonARRAY);
//////		$pdaITEMARRAY   = $pdajsonARRAY['dataset']['pdc'];
//		$pdaITEMARRAY   = $pdajsonARRAY['dataset'];

		echo '<pre>';
		var_dump($pdaITEMARRAY);
		echo '</pre>';


// TESTING storing pda data in the session
//if (!$_SESSION) {
//	session_start();
//}

//$_SESSION['pdaData'] = $pdaITEMARRAY;




echo '<hr><pre><h2>PDA-DATA</h2>';
///foreach ($_SESSION as $sesskey => $sessvalue) {
///	echo $sesskey .' | '. $sessvalue .'<br>';
///}
var_dump($_SESSION['pdaData']);
echo '</pre><hr>';

// TESTING storing pda data in the session

/// https://hotmangoteam.github.io/Fishikawatest/pdadata/frca-pdadata.json
////			$frcaJSON		= file_get_contents( 'frca.json' );
////			$frcajsonARRAY	= json_decode($frcaJSON, true);  // decode json in to an array

///			echo '<pre class="small">';
///			var_dump($frcajsonARRAY['dataset']);
///			echo '</pre>';

///			foreach ($frcajsonARRAY->data->records as $probKey => $probData) {
//				echo $probKey .' : '. $probData.'<br />';
///				echo $probKey .'<br />';
///			}

*/
		?>
		<!-- TESTING -->







			<?php
			/**
			 * confidence ratings based on basic environment tests
			 *
			 * a quick and dirty visual feedback method to display multiple score/rating for the following areas;
			 * - hosting server & database
			 * - php environment
			 * - joomla settings (set to "NA" if instance not found, and ognore so not to skew final total)
			 * -- total aggregate rating of above
			 *
			 * confidenceScore methodology
			 * scale :
			 * 0  = fatal/danger/no/unknown/unsupported/bad
			 * 1  = notice/warning/ok/old(minor)/maybe
			 * 2  = success/yes/latest/good
			 *
			 */
			?>
			<?php
				$thisJVER 													= @$instance['cmsRELEASE'] . '.' . @$instance['cmsDEVLEVEL'];
//				$confidence 												= array();
//				$confidence['SERVER']										= array();
				// check php handler & phpsuexec
				if (substr($phpenv['phpAPI'], 0, 3) == 'cgi' OR $phpenv['phpAPI'] != 'apache2handler') {
					$confidence['SERVER']['WebServer PHP Handler']					= 2;

				} elseif ($phpenv['phpAPI'] == 'apache2handler') {
					$confidence['SERVER']['WebServer PHP Handler']				= 1;

				} else {
					$confidence['SERVER']['WebServer PHP Handler']				= 0;
				}

				// check if system tmp is writable
				if ($system['sysTMPDIRWRITABLE'] == $lang['FRCA_Y']) {
					$confidence['SERVER'][$lang['FRCA_SERV'] . ' ~/tmp ' . $lang['FRCA_WRITABLE']]	= 2;

				} else {
					$confidence['SERVER'][$lang['FRCA_SERV'] . ' ~/tmp ' . $lang['FRCA_WRITABLE']]	= 0;
				}

				if ($phpenv['phpAPACHESUEXEC'] == $lang['FRCA_Y'] or substr($phpenv['phpAPI'], 0, 4) == 'lite') {
					$confidence['SERVER']['Server suExec Available']			= 2;

				} else {
					$confidence['SERVER']['Server suExec Available']			= 1;
				}


					// test more accurately for writable status
					// TODO: Check that DOCUMENT_ROOt works correctly
					//if ( is_writeable(basename($_SERVER['PHP_SELF'])) AND is_writeable('components') ) {
					if ( is_writeable(basename($_SERVER['PHP_SELF'])) AND is_writeable($_SERVER['DOCUMENT_ROOT']) ) {
						$confidence['SERVER']['File System Writable']			= 2;

					} elseif ( !is_writeable(basename($_SERVER['PHP_SELF'])) XOR !is_writeable($_SERVER['DOCUMENT_ROOT']) ) {
						$confidence['SERVER']['File System Writable']			= 1;

					} else {
						$confidence['SERVER']['File System Writable']			= 0;
					}

				// only run these if instance is found (installed or not)
				if ($instance['instanceFOUND'] == 1) {

					// SQL Supports J!
					$input_line = @$database['dbHOSTSERV'];
					preg_match("/\b(\w*mariadb\w*)\b/i", $input_line, $output_array);

					if (@$instance['cmsRELEASE'] >= '4.0') {
						$fpa['supportENV']['minPHP']        = '7.2.5';
						$fpa['supportENV']['minSQL']        = '5.6.0';
						$fpa['supportENV']['maxPHP']        = '8.0.0';
						$fpa['supportENV']['maxSQL']        = '9.0.0';
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '3.10') {
						$fpa['supportENV']['minPHP']        = '5.3.10';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '8.0.0';
						$fpa['supportENV']['maxSQL']        = '9.0.0';
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '3.9') {
						$fpa['supportENV']['minPHP']        = '5.3.10';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '7.5.0';
						$fpa['supportENV']['maxSQL']        = '8.5.0';
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] > '3.7' and @$instance['cmsDEVLEVEL'] > '2') {
						$fpa['supportENV']['minPHP']        = '5.3.10';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '7.5.0';
						$fpa['supportENV']['maxSQL']        = '5.8.0';
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] >= '3.5') {
						$fpa['supportENV']['minPHP']        = '5.3.10';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '7.1.99';
						$fpa['supportENV']['maxSQL']        = '5.8.0';
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE']  == '3.3' or @$instance['cmsRELEASE']  == '3.4') {
						$fpa['supportENV']['minPHP']        = '5.3.10';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '6.0.0';
						$fpa['supportENV']['maxSQL']        = '5.8.0';
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '3.2' and @$instance['cmsDEVLEVEL'] >= 1) {
						$fpa['supportENV']['minPHP']        = '5.3.1';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '3.2' and @$instance['cmsDEVLEVEL'] == 0) {
						$fpa['supportENV']['minPHP']        = '5.3.7';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '3.1') {
						$fpa['supportENV']['minPHP']        = '5.3.1';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '3.0') {
						$fpa['supportENV']['minPHP']        = '5.3.1';
						$fpa['supportENV']['minSQL']        = '5.1.0';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = '5.3.0';
						$fpa['supportENV']['badPHP'][1]     = '5.3.1';
						$fpa['supportENV']['badPHP'][2]     = '5.3.2';
						$fpa['supportENV']['badPHP'][3]     = '5.3.3';
						$fpa['supportENV']['badPHP'][4]     = '5.3.4';
						$fpa['supportENV']['badPHP'][5]     = '5.3.5';
						$fpa['supportENV']['badPHP'][6]     = '5.3.6';
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '2.5') {
						$fpa['supportENV']['minPHP']        = '5.2.4';
						$fpa['supportENV']['minSQL']        = '5.0.4';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = $lang['FRCA_NA'];
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '1.7') {
						$fpa['supportENV']['minPHP']        = '5.2.4';
						$fpa['supportENV']['minSQL']        = '5.0.4';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = $lang['FRCA_NA'];
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '1.6') {
						$fpa['supportENV']['minPHP']        = '5.2.4';
						$fpa['supportENV']['minSQL']        = '5.0.4';
						$fpa['supportENV']['maxPHP']        = '6.0.0';  // latest release?
						$fpa['supportENV']['maxSQL']        = '5.8.0';  // latest release?
						$fpa['supportENV']['badPHP'][0]     = $lang['FRCA_NA'];
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} elseif (@$instance['cmsRELEASE'] == '1.5') {

						if (@$instance['cmsDEVLEVEL'] <= '14') {
							$fpa['supportENV']['minPHP']        = '4.3.10';
							$fpa['supportENV']['minSQL']        = '3.23.0';
							$fpa['supportENV']['maxPHP']        = '5.2.17';
							$fpa['supportENV']['maxSQL']        = '5.5.0';  // limited by ENGINE TYPE changes in 5.5 and install sql syntax

						} else {
							$fpa['supportENV']['minPHP']        = '4.3.10';
							$fpa['supportENV']['minSQL']        = '3.23.0';
							$fpa['supportENV']['maxPHP']        = '5.3.6';
							$fpa['supportENV']['maxSQL']        = '5.5.0';  // limited by ENGINE TYPE changes in 5.5 and install sql syntax

						}

						$fpa['supportENV']['badPHP'][0]     = '4.3.9';
						$fpa['supportENV']['badPHP'][1]     = '4.4.2';
						$fpa['supportENV']['badPHP'][2]     = '5.0.4';
						$fpa['supportENV']['badZND'][0]     = '2.5.10';
					} elseif (@$instance['cmsRELEASE'] == '1.0') {
						$fpa['supportENV']['minPHP']        = '3.0.1';
						$fpa['supportENV']['minSQL']        = '3.0.0';
						$fpa['supportENV']['maxPHP']        = '5.2.17';  // changed max supported php from 4.4.9 to 5.2.17 - 03/12/17 - PD
						$fpa['supportENV']['maxSQL']        = '5.0.91';  // limited by ENGINE TYPE changes in 5.0 and install sql syntax
						$fpa['supportENV']['badPHP'][0]     = $lang['FRCA_NA'];
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					} else {
						$fpa['supportENV']['minPHP']        = $lang['FRCA_NA'];
						$fpa['supportENV']['minSQL']        = $lang['FRCA_NA'];
						$fpa['supportENV']['maxPHP']        = $lang['FRCA_NA'];
						$fpa['supportENV']['maxSQL']        = $lang['FRCA_NA'];
						$fpa['supportENV']['badPHP'][0]     = $lang['FRCA_NA'];
						$fpa['supportENV']['badZND'][0]     = $lang['FRCA_NA'];
					}
					// minimum and maximum MySQL support requirements met?
					if ($fpa['supportENV']['minSQL'] == $lang['FRCA_NA'] or @$database['dbERROR'] != $lang['FRCA_N']) {
						$snapshot['sqlSUP4J'] = $lang['FRCA_U'];
					} elseif ((version_compare(@$database['dbHOSTSERV'], $fpa['supportENV']['minSQL'], '>=')) and (version_compare(@$database['dbHOSTSERV'], $fpa['supportENV']['maxSQL'], '<='))) {

						// WARNING, will run, but ONLY after modifying install SQL to remove ENGINE TYPE statements (removed in MySQL 5.5)
						if (($instance['cmsRELEASE'] == '1.5') and (@$database['dbHOSTSERV'] > '5.1.43')) {
							$snapshot['sqlSUP4J'] = $lang['FRCA_M'];
						} else {
							$snapshot['sqlSUP4J'] = $lang['FRCA_Y'];
						}
					} elseif ((version_compare(@$database['dbHOSTSERV'], $fpa['supportENV']['minSQL'], '<')) or (version_compare(@$database['dbHOSTSERV'], $fpa['supportENV']['maxSQL'], '>'))) {

						// WARNING, will run, but ONLY after modifying install SQL to remove ENGINE TYPE statements (removed in MySQL 5.5)
						if (($instance['cmsRELEASE'] == '1.5') and (@$database['dbHOSTSERV'] > '5.1.43')) {
							$snapshot['sqlSUP4J'] = $lang['FRCA_M'];
						}
						//Added this elseif to give the ok for postgreSQL
						elseif ($instance['configDBTYPE'] == 'postgresql' and $database['dbHOSTSERV'] >= 8.3) {
							$snapshot['sqlSUP4J'] = $lang['FRCA_Y'];
						}
						//Added this elseif to give the ok for PDO postgreSQL
						elseif ($instance['configDBTYPE'] == 'pgsql' and $database['dbHOSTSERV'] >= 8.3) {
							$snapshot['sqlSUP4J'] = $lang['FRCA_Y'];
						}
						//Added this elseif to give the ok for MariaDB - @PhilD 17-Mar-2017
						elseif (strtoupper(@$output_array[0]) == "MARIADB") {
							$snapshot['sqlSUP4J'] = $lang['FRCA_Y'];
						} else {
							$snapshot['sqlSUP4J'] = $lang['FRCA_N'];
						}
					} else {
						$snapshot['sqlSUP4J'] = $lang['FRCA_U'];
					}

					if ($snapshot['sqlSUP4J'] == $lang['FRCA_Y']) {
						$confidence['SERVER'][$lang['FRCA_SUPSQL'] .' J!'. @$thisJVER]			= 2;

					} elseif ($snapshot['sqlSUP4J'] == $lang['FRCA_U']) {
						$confidence['SERVER'][$lang['FRCA_SUPSQL'] .' J!'. @$thisJVER]			= 1;

					} else {
						$confidence['SERVER'][$lang['FRCA_SUPSQL'] .' J!'. @$thisJVER]			= 0;
					}


//					var_dump($database);
//					echo $snapshot['sqlSUP4SQL'];
//					echo $snapshot['sqlSUP4SQL-i'];


				} // if instanceFound


				if (isset($system['sysENCODING']) OR !empty($system['sysENCODING'])) {
					$confidence['SERVER']['Server Compression Available']	= 2;

				} else {
					$confidence['SERVER']['Server Compression Available']	= 1;
				}

				if (stristr($_SERVER['SERVER_SIGNATURE'], 'openssl') === FALSE OR stristr($_SERVER['SERVER_SIGNATURE'], 'python') === FALSE OR stristr($_SERVER['SERVER_SIGNATURE'], 'perl') === FALSE) {
					$confidence['SERVER']['Restricted Server Signature']	= 2;
				} else {
					$confidence['SERVER']['Restricted Server Signature']	= 1;
				}


				// TODO: test these headers online see if you get more from headrs and .htaccess directives
				if ($http_response_header) {

					// set the headers to 1 first and update if found in request headers
					$confidence['SERVER']['Server Offered HTTP/2']				= 1;
					$confidence['SERVER']['Origin Access Control Directive']	= 1;
					$confidence['SERVER']['HSTS Directive Found']				= 1;

					foreach ($http_response_header AS $header => $value) {

						if (stristr($value, 'upgrade: h') == TRUE) {
							$confidence['SERVER']['Server Offered HTTP/2']	= 2;
						}

						if (stristr($value, 'access-control-allow-origin:') == TRUE) {
							$confidence['SERVER']['Origin Access Control Directive']	= 2;
						}

						if (stristr($value, 'strict-transport-security:') == TRUE) {
							$confidence['SERVER']['HSTS Directive Found']	= 2;
						}

					} // endforeach

				} // end http_response_headers

				// TODO: test these headers online to see if yu get more than above
				/*
				if (function_exists('apache_request_headers')) {
					$headers = apache_request_headers();
				}
				echo '<pre>';
				var_dump($headers);
				echo '</pre>';

				if (function_exists('apache_response_headers')) {
					$headers_list = apache_response_headers();
				}
				echo '<pre>';
				var_dump($headers_list);
				echo '</pre>';
				*/

				// nothing to change here if adding new confidence results (auto counts & calculates)
				$countConfidenceSERVER = count($confidence['SERVER']) * 2;
				$confidenceScoreSERVER = (array_sum($confidence['SERVER']) / $countConfidenceSERVER) * 100;
//				echo '-[SERVER['.$confidenceScoreSERVER.']]-<br />';



//				$confidence['PHP']											= array();
				// only test these items if instance is found (installed or not)
				if ($instance['instanceFOUND'] == 1) {

					// does php support J! version
					// TODO : fix index error on phpSUP4J
					// suppressed with @ at the moment
					if (@$snapshot['phpSUP4J'] == $lang['FRCA_Y']) {
						$confidence['PHP'][$lang['FRCA_SUPPHP'] .' J!'. @$thisJVER]	= 2;

					} elseif (@$snapshot['phpSUP4J'] == $lang['FRCA_M']) {
						$confidence['PHP'][$lang['FRCA_SUPPHP'] .' J!'. @$thisJVER] 	= 1;

					} else {
						$confidence['PHP'][$lang['FRCA_SUPPHP'] .' J!'. @$thisJVER] 	= 0;
					}

				} // if instance found

				// phpsuExec
				if ($phpenv['phpPHPSUEXEC'] == $lang['FRCA_Y'] OR ($phpenv['phpPHPSUEXEC'] == $lang['FRCA_N'] AND substr($phpenv['phpAPI'], 0, 4) == 'lite')) {
					$confidence['PHP']['PHP suExec Available']				= 2;

				} elseif ($phpenv['phpPHPSUEXEC'] == $lang['FRCA_N'] AND $phpenv['phpAPI'] == 'apache2handler') {
					$confidence['PHP']['PHP suExec available']				= 1;

				} else {
					$confidence['PHP']['PHP suExec available']				= 0;
				}

				// check for UTF8 support
				if (array_key_exists('mbstring', $phpextensions)) {
					$confidence['PHP']['PHP mbstring available']			= 2;

				} else {
					$confidence['PHP']['PHP mbstring available']			= 1;
				}

				// check for compression extensions
				if (array_key_exists('zip', $phpextensions) or array_key_exists('zlib', $phpextensions) or array_key_exists('bz2', $phpextensions)) {
					$confidence['PHP']['PHP zip, zlib or bz2 available']	= 2;

				} else {
					$confidence['PHP']['PHP zip, zlib or bz2 available']	= 1;
				}

				// check for encryption extensions
				if (array_key_exists('mcrypt', $phpextensions) or array_key_exists('sodium', $phpextensions)) {
					$confidence['PHP']['PHP mcrypt or sodium available']	= 2;

				} else {
					$confidence['PHP']['PHP mcrypt or sodium available']	= 1;
				}

				// check for cURL extensions
				if (array_key_exists('curl', $phpextensions)) {
					$confidence['PHP']['PHP cURL available']				= 2;

				} else {
					$confidence['PHP']['PHP cURL available']				= 1;
				}

				// check for openSSL extension
				if (array_key_exists('openssl', $phpextensions)) {
					$confidence['PHP']['PHP openSSL available']				= 2;

				} else {
					$confidence['PHP']['PHP openSSL available']				= 1;
				}

				// check for XML extensions
				if (array_key_exists('xml', $phpextensions) OR array_key_exists('libxml', $phpextensions)) {
					$confidence['PHP']['PHP xml or libxml available']		= 2;

				} else {
					$confidence['PHP']['PHP xml or libxml available']		= 1;
				}

				// check for json extension
				if (array_key_exists('json', $phpextensions)) {
					$confidence['PHP']['PHP json available']				= 2;

				} else {
					$confidence['PHP']['PHP json available']				= 1;
				}

				// check for iconv extension
				if (array_key_exists('iconv', $phpextensions)) {
					$confidence['PHP']['PHP iconv available']				= 2;

				} else {
					$confidence['PHP']['PHP iconv available']				= 1;
				}

				// check for allow_url_fopen
				if ($phpenv['phpURLFOPEN'] == '1') {
					$confidence['PHP']['PHP allow_url_fopen available']		= 2;

				} else {
					$confidence['PHP']['PHP allow_url_fopen available']		= 0;
				}

				// check for display_errors
				if (strtolower($phpenv['phpERRORDISPLAY']) == 'off' OR $phpenv['phpERRORDISPLAY'] == '0') {
					$confidence['PHP']['PHP Error Display Disabled']		= 2;

				} else {
					$confidence['PHP']['PHP Error Display Disabled']		= 1;

					$problemList['MODERATE'][]			= array(
						'HEADING'		=> 'PHP display errors is enabled.',
						'DESCRIPTION'	=> 'This means that if PHP produces any errors, warning or notices they will be shown on your website.',
						'CATEGORY' 		=> 'PHP',
						'SEVERITY'		=> '3',
						'SYMPTOMS'		=> 'visible php messages',
						'CAUSES'		=> array(
							'0'	=> 'cause1',
							'1'	=> 'cause2'
						),
						'EFFECTS'		=> 'user interface,seo,security',
						'ACTIONS'		=> array(
							'0'	=> 'test1',
							'1'	=> 'test2'
						)
					);

				}

				// check for error_reporting
				if (strtolower($phpenv['phpERRORREPORT']) == 'off' OR $phpenv['phpERRORREPORT'] == '0') {
					$confidence['PHP']['PHP Error Reporting']		= 2;

				} else {
					$confidence['PHP']['PHP Error Reporting']		= 1;
				}

				// check for expose_php (shows in headers)
				if (ini_get('expose_php') != '1' OR strtolower(ini_get('expose_php')) == 'off') {
					$confidence['PHP']['Expose PHP Disabled']		= 2;

				} else {
					$confidence['PHP']['Expose PHP Disabled']		= 1;
				}

				// check for database support
				// TODO : fix index error on phpSUPMYSQL
				// suppressed with @ at the moment
				if (@$snapshot['phpSUP4MYSQL'] == $lang['FRCA_Y'] or @$snapshot['phpSUP4MYSQL-i'] == $lang['FRCA_Y']) {
					$confidence['PHP'][$lang['FRCA_SUPPHP'] . ' MySQL or MySQLi']	= 2;

				} else {
					$confidence['PHP'][$lang['FRCA_SUPPHP'] . ' MySQL or MySQLi']	= 0;
				}

				// check for bad php versions
				// known buggy php releases (mainly for installation on 1.5)
				foreach ($fpa['supportENV']['badPHP'] as $badKey => $badValue) {
					if (version_compare(PHP_VERSION, $badValue, '==')) {
						$badANS = $lang['FRCA_Y'];
						continue;
					}
				}

				if (@$badANS == $lang['FRCA_Y']) {
					$snapshot['buggyPHP'] = $lang['FRCA_N'];
				} else {
					$badANS = $lang['FRCA_N'];
					$snapshot['buggyPHP'] = $lang['FRCA_N'];
				}

				if ($snapshot['buggyPHP'] == $lang['FRCA_N']) {
					$confidence['PHP'][$lang['FRCA_BADPHP']]							= 2;

				} else {
					$confidence['PHP'][$lang['FRCA_BADPHP']]							= 0;
				}

				// check bad zend versions
				// known buggy zend releases (mainly for installation on 1.5)
				$badValue   = ''; // reset variables to fix zend check bug
				$badANS     = '';
				foreach ($fpa['supportENV']['badZND'] as $badKey => $badValue) {

					if (version_compare($phpextensions['Zend Engine'], $badValue, '==')) {
						$badANS = $lang['FRCA_Y'];
						continue;
					}
				}

				if (@$badANS == $lang['FRCA_Y']) {
					$snapshot['buggyZEND'] = $lang['FRCA_Y'];
				} else {
					$badANS = $lang['FRCA_N'];
					$snapshot['buggyZEND'] = $lang['FRCA_N'];
				}

				if ($snapshot['buggyZEND'] == $lang['FRCA_N']) {
					$confidence['PHP'][$lang['FRCA_BADZND']]							= 2;

				} else {
					$confidence['PHP'][$lang['FRCA_BADZND']]							= 0;
				}

				// check if php sessionpath writable
				if ($phpenv['phpSESSIONPATHWRITABLE'] == $lang['FRCA_Y']) {
					$confidence['PHP']['PHP Session Path ' . $lang['FRCA_WRITABLE']]	= 2;

				} else {
					$confidence['PHP']['PHP Session Path ' . $lang['FRCA_WRITABLE']]	= 0;
				}

				// nothing to change here if adding new confidence results (auto counts & calculates)
				$countConfidencePHP = count($confidence['PHP']) * 2;
				$confidenceScorePHP = (array_sum($confidence['PHP']) / $countConfidencePHP) * 100;
//				echo '-[PHP['.$confidenceScorePHP.']]-<br />';


// testing
//$instance['instanceFOUND'] = 0;

//				$confidence['JOOMLA']										= array();

///var_dump($jConfig);

				if ($instance['instanceFOUND'] == 1) {

					if ( $instance['instanceCONFIGURED'] == $lang['FRCA_Y'] ) {
						$confidence['JOOMLA']['Instance (Correctly) Configured']				= 2;

					} else {
						$confidence['JOOMLA']['Instance (Correctly) Configured']				= 0;

						// TODO: convert to pda entry
						$problemList['CRITICAL'][]			= array(
							'heading'		=> 'Joomla! instance found but not configured correctly',
							'description'	=> 'A Joomla! instance was found but appears to be missing the database connection credentials. Have you run the intall process? Or did you just copy the configuration.php-dist file to configuration.php?',
							'category' 		=> 'JOOMLA',
							'severity'		=> '1',
							'symptoms'		=> array(
								'0'	=> 'white',
								'1'	=> 'on screen "error" message',
								'2'	=> '',
								'3'	=> ''
							),
							'causes'		=> array(
								'0'	=> 'cause1',
								'1'	=> 'cause2'
							),
							'effects'		=> array(
								'0'	=> 'installation',
								'1'	=> 'operation',
								'2'	=> '',
								'3'	=> ''
							),
							'actions'		=> array(
								'0'	=> 'action1',
								'1'	=> 'action2',
								'2'	=> '',
								'3'	=> ''
							),
							'problemcode'	=> 'not assigned'
						);

					}

					if ( $instance['instanceCONFIGURED'] == $lang['FRCA_Y'] and $instance['instanceDBCREDOK'] == $lang['FRCA_N'] ) {

						// only show if joomla is configured but user & passwordword are missing
						// $instance['instanceCONFIGURED']
						$confidence['JOOMLA']['Database Credentials Valid']			= 0;

						// raise a ProblemCode
						getPDC( '4', '0053' );

					}



					if ( isset($jConfig->force_ssl) and $jConfig->force_ssl > '0' ) {

						$confidence['JOOMLA']['Force SSL Enabled']			= 2;

					} else {

						$confidence['JOOMLA']['Force SSL Enabled']			= 1;

					}


					if ( isset($jConfig->gzip) and $jConfig->gzip == '1' ) {

						$confidence['JOOMLA']['QZip Enabled']			= 2;

					} else {

						$confidence['JOOMLA']['GZip Enabled']			= 1;

					}


					if ( isset($jConfig->error_reporting) and $jConfig->error_reporting == 'none' ) {

						$confidence['JOOMLA']['Error Reporting Disabled']			= 2;

					} else {

						$confidence['JOOMLA']['Error Reporting Disabled']			= 1;
					}


					if ( isset($jConfig->ftp_enable) and $jConfig->ftp_enable == '0' ) {

						$confidence['JOOMLA']['FTP Disabled']			= 2;

					} else {

						$confidence['JOOMLA']['FTP Disabled']			= 1;

					}


					if ( isset($jConfig->sef) and $jConfig->sef == '1' ) {

						$confidence['JOOMLA']['SEF Enabled']			= 2;

					} else {

						$confidence['JOOMLA']['SEF Enabled']			= 1;

					}


					if ( isset($jConfig->sef_rewrite) and $jConfig->sef_rewrite == '1' ) {

						$confidence['JOOMLA']['SEF Rewrite Enabled']	= 2;

					} else {

						$confidence['JOOMLA']['SEF Rewrite Enabled']	= 1;
					}


					if ( file_exists('.htaccess') or file_exists('web.config') ) {

						$confidence['JOOMLA']['Found .htaccess/web.config']	= 2;

					} else {

						$confidence['JOOMLA']['Found .htaccess/web.config']	= 1;

					}

					if ( $confidence['JOOMLA']['SEF Rewrite Enabled'] == 2 and $confidence['JOOMLA']['Found .htaccess/web.config'] == 1 ) {

						$confidence['JOOMLA']['SEF Rewrite Configured Correctly']	= 0;

						$problemList['MINOR']['0066']			= array(
							'heading'		=> 'SEF URLs are misconfigured.',
							'description'	=> 'You have Joomla! SEF URLs enabled but a .htaccess/web.config file could not be found.',
							'category' 		=> 'JOOMLA',
							'severity'		=> '2',
							'symtoms'		=> 'SEF URLs not working, Error 500 when clicking menu items/links',
							'causes'		=> array(
								'0'	=> 'cause1',
								'1'	=> 'cause2'
							),
							'effects'		=> 'user interface,navigation',
							'actions'		=> array(
								'0'	=> 'test1',
								'1'	=> 'test2'
							)
						);

					} else {

						$confidence['JOOMLA']['SEF Rewrite Configured Correctly']	= 2;

					}

					if ( isset($jConfig->tmp_path) and file_exists($jConfig->tmp_path) ) {

						$confidence['JOOMLA']['Tmp Path Valid']	= 2;

					} else {

						$confidence['JOOMLA']['Tmp Path Valid']	= 0;

					}


					if ( isset($jConfig->log_path) and file_exists($jConfig->log_path) ) {

						$confidence['JOOMLA']['Log Path Valid']	= 2;

					} else {

						$confidence['JOOMLA']['Log Path Valid']	= 1;

					}


					if ( isset($jConfig->massmailoff) and $jConfig->massmailoff == '1' ) {

						$confidence['JOOMLA']['Mass Mail Disabled']	= 2;

					} else {

						$confidence['JOOMLA']['Mass Mail Disabled']	= 1;

					}

					// destroy the $config array so as not to cause issues later
					// TODO: do we unset $jConfig here? or will it be needed later
					// unset ($jConfig);

// TESTING
//$thisJVER = '4.0';


					if ( version_compare( $thisJVER, $latestJVER ) < 0 ) {

						// out-of-date
						$confidence['JOOMLA']['Joomla! '. $lang['VER_CHECK_ATOLD'] .' (v'. $thisJVER .')'] = 1;

					} elseif ( version_compare( $thisJVER, $latestJVER ) > 0 ) {

						// development version
						$confidence['JOOMLA']['Joomla! '. $lang['VER_CHECK_ATDEV'] .' (v'. $thisJVER .')'] = 1;

					} else {

						// up-to-date
						$confidence['JOOMLA']['Joomla! '. $lang['VER_CHECK_ATCUR'] .' (v'. $thisJVER .')'] = 2;

					}


					// nothing to change here if adding new confidence results (auto counts & calculates)
					$countConfidenceJOOMLA = count($confidence['JOOMLA']) * 2;
					$confidenceScoreJOOMLA = (array_sum($confidence['JOOMLA']) / $countConfidenceJOOMLA) * 100;
//				echo '-[JOOMLA['.$confidenceScoreJOOMLA.']]-<br />';

				} else {

					$confidence['JOOMLA']['Instance Found']					= $lang['FRCA_N'];
					$confidenceScoreJOOMLA = $lang['FRCA_NA'];

				}

			?>



			<section class="bg-light py-5" id="frca-confidence-dashboard">
				<div class="container">

					<h1 class="font-weight-light pb-2 border-bottom">
						<i class="fas fa-layer-group fa-sm text-muted"></i> Confidence Status
					</h1>

					<?php
					/**
					 * showstoppers & belt 'n' braces
					 *
					 * if guaranteed to break Joomla!, definately unsupported, any of the following criteria
					 * are met or if score result is not numeric or a positive number between 0-100, force
					 * $confidenceScore to 0(zero)/F
					 *
					 */
					?>
					<?php

						// unsupported minimum PHP version
						if (version_compare(PHP_VERSION, '5', '<')) {
							$confidenceScorePHP = 0;
							$confidence['JOOMLA']['PHP - Out Of Support']			= 2;
						} elseif (version_compare(PHP_VERSION, '7.1', '<')) {
							$confidenceScorePHP = 75;
							$confidence['JOOMLA']['PHP - Old Version']			= 2;

						}

						// mysql or php does not support installed Joomla! version
						// TODO : fix index error on phpSUP4J
						// suppressed with @ at the moment
						if ($instance['instanceFOUND'] == 1 AND (@$snapshot['phpSUP4J'] == $lang['FRCA_N'] OR $snapshot['sqlSUP4J'] == $lang['FRCA_N'])) {
							if ($confidenceScore > 100 OR $confidenceScore < 0) {
								$confidenceRating	= '<i class="fas fa-question-circle text-muted"></i>';
								$confidenceColor	= 'muted';
							}
						}

						// bad score result
//						if (!is_numeric($confidenceScore) OR $confidenceScore < 0) {
//							$confidenceScore = $lang['FRCA_NA'];
//						}

					?>
						<?php
							/**
							 * generate confidence rating
							 * based on $confidenceScore
							 * A to F & messaging
							 *
							 */
							function getRating($confidenceScore) {
								if ($confidenceScore > 100 OR $confidenceScore < 0 OR !is_numeric($confidenceScore)) {
									$confidenceRating	= '<i class="fas fa-question-circle text-muted"></i>';
									$confidenceColor	= 'muted';

								} elseif ($confidenceScore >= 0 and $confidenceScore <= 25) {
									$confidenceRating	= 'F';
									$confidenceColor	= 'danger';

								} elseif ($confidenceScore <= 40) {
									$confidenceRating	= 'E';
									$confidenceColor	= 'warning';

								} elseif ($confidenceScore <= 60) {
									$confidenceRating	= 'D';
									$confidenceColor	= 'warning';

								} elseif ($confidenceScore <= 75) {
									$confidenceRating	= 'C';
									$confidenceColor	= 'info';

								} elseif ($confidenceScore <= 90) {
									$confidenceRating	= 'B';
									$confidenceColor	= 'primary';

								} elseif ($confidenceScore < 100) {
									$confidenceRating	= 'A';
									$confidenceColor	= 'success';

								} elseif ($confidenceScore == 100) {
									$confidenceRating	= 'A+';
									$confidenceColor	= 'success';

								} else { // catch-all
									$confidenceRating	= '<i class="fas fa-question-circle text-muted"></i>';
									$confidenceColor	= 'muted';

								}

								$confidenceResult = array('rating' => $confidenceRating, 'color' => $confidenceColor);
								return $confidenceResult;

							} //getRating
						?>


					<?php
					/**
					 * set the $confidenceHelp icon colour
					 *
					 * we limit the colours use for the help icon and don't use the $confidenceScore colour scheme
					 * so that it is more generic and will change the icon only within larger score variations.
					 *
					 * the colour changes are purely a visual method of informing the user that some audit tests
					 * do not equal 2 (top score) and may need review (via the help icon button/panel)
					 *
					 * 0 to 25.000 (F rated) = danger
					 * 25.001 to 99.999 (E, D, C, B & A rated) = warning
					 * 100 (A+ rated) = info (default)
					 *
					 */
					?>
					<div class="row mt-4 confidence-grid">
						<div class="col-xs-12 col-sm-6 col-xl-3 1d-flex align-items-stretch mb-3">

							<?php $confidenceRating = getRating($confidenceScoreSERVER); ?>

							<div class="d-flex flex-row flex-fill align-items-stretch border bg-white shadow-sm border bg-white shadow-sm confidence-server" style="position:relative;min-height:130px;">
								<div class="flex-fill p-3">
									<h2 class="lead text-body"><?php echo $lang['FRCA_CONFDASH_SERVER_HEADING']; ?></h2>
									<p class="small m-0">
									<?php echo $lang['FRCA_CONFDASH_SERVER_TEXT']; ?>
									</p>
								</div>
								<div class="p-1 d-flex flex-column align-items-center justify-content-center border-left" style="min-width:85px!important;">
									<h3 class="h1 m-0 text-<?php echo $confidenceRating['color']; ?>"><?php echo $confidenceRating['rating']; ?></h3>
									<span class="badge badge-pill badge-light"><?php echo round($confidenceScoreSERVER, 1); ?>%</span>
								</div>
							</div><!--/.confidence-server-->

							<span class="bg-light" data-toggle="popover" data-trigger="hover" data-placement="top" data-fallbackplacement="flip" data-title="Confidence Audit Help" data-content="Click the icon to review the basic audit tests and results that determine this rating" style="position:absolute;bottom:-9px;right:3px;z-index:1;border-radius:50%;padding:4px 2px 2px 3px;">
								<i class="fas fa-info-circle fa-lg text-<?php echo $confidenceRating['color']; ?>" data-toggle="collapse" data-target="#collapseExampleSERVER" aria-expanded="false" aria-controls="collapseExampleSERVER"></i>
							</span>

							<div class="w-100 text-right confidence-tests" style="position:relative;">
								<div class="collapse text-left shadow" id="collapseExampleSERVER" style="position:absolute;top:15px;left:0;right:0;z-index:4;">

									<div class="card">
										<ul class="list-group list-group-flush">
											<li class="list-group-item"><h4 class="h5 m-0 p-0 text-center">Server Audit Results</h4></li>

											<?php
												// sort the array alphabetically by key if score is 100% else sort by ascending value
												if ($confidenceScoreSERVER == 100) {
													ksort($confidence['SERVER']);
												} else {
													ksort($confidence['SERVER']);
													asort($confidence['SERVER']);
												}
											?>
											<?php foreach($confidence['SERVER'] AS $key => $value) { ?>
												<?php
													if ($value == 2) {
														$helpIcon  = 'check-circle';
														$helpColor = 'success';

													} elseif ($value == 1) {
														$helpIcon  = 'info-circle';
														$helpColor = 'info';

													} elseif ($value == 0) {
														$helpIcon  = 'times-circle';
														$helpColor = 'danger';

													} else {
														$helpIcon  = 'question-circle';
														$helpColor = 'light';
													}
												?>
												<li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center <?php if ($value < 2) { echo 'text-'.$helpColor; } ?>"><?php echo $key; ?> <i class="fas fa-<?php echo $helpIcon; ?> fa-fw text-<?php echo $helpColor; ?>"></i></li>
											<?php } //foreach ?>

										</ul>
									</div><!--/.card-->

								</div><!--/.collapse-->
							</div><!--/.confidence-tests-->

						</div>
						<div class="col-xs-12 col-sm-6 col-xl-3 1d-flex align-items-stretch mb-3">

							<?php $confidenceRating = getRating($confidenceScorePHP); ?>

							<div class="d-flex flex-row flex-fill align-items-stretch border bg-white shadow-sm border bg-white shadow-sm confidence-php" style="position:relative;min-height:130px;">
								<div class="flex-fill p-3">
									<h2 class="lead text-body"><?php echo $lang['FRCA_CONFDASH_PHP_HEADING']; ?></h2>
									<p class="small m-0">
									<?php echo $lang['FRCA_CONFDASH_PHP_TEXT']; ?>
									</p>
								</div>
								<div class="p-1 d-flex flex-column align-items-center justify-content-center border-left" style="min-width:85px!important;">
									<h3 class="h1 m-0 text-<?php echo $confidenceRating['color']; ?>"><?php echo $confidenceRating['rating']; ?></h3>
									<span class="badge badge-pill badge-light"><?php echo round($confidenceScorePHP, 1); ?>%</span>
								</div>
							</div><!--/.confidence-php-->

							<span class="bg-light" data-toggle="popover" data-trigger="hover" data-placement="top" data-fallbackplacement="flip" data-title="Confidence Audit Help" data-content="Click the icon to review the basic audit tests and results that determine this rating" style="position:absolute;bottom:-9px;right:3px;z-index:1;border-radius:50%;padding:4px 2px 2px 3px;">
								<i class="fas fa-info-circle fa-lg text-<?php echo $confidenceRating['color']; ?>" data-toggle="collapse" data-target="#collapseExamplePHP" aria-expanded="false" aria-controls="collapseExamplePHP"></i>
							</span>

							<div class="w-100 text-right confidence-tests" style="position:relative;">
								<div class="collapse text-left shadow" id="collapseExamplePHP" style="position:absolute;top:15px;left:0;right:0;z-index:3;">

									<div class="card">
										<ul class="list-group list-group-flush">
											<li class="list-group-item"><h4 class="h5 m-0 p-0 text-center">PHP Audit Results</h4></li>

											<?php
												// sort the array alphabetically by key if score is 100% else sort by ascending value
												if ($confidenceScorePHP == 100) {
													ksort($confidence['PHP']);
												} else {
													ksort($confidence['PHP']);
													asort($confidence['PHP']);
												}
											?>
											<?php foreach($confidence['PHP'] AS $key => $value) { ?>
												<?php
													if ($value == 2) {
														$helpIcon  = 'check-circle';
														$helpColor = 'success';

													} elseif ($value == 1) {
														$helpIcon  = 'info-circle';
														$helpColor = 'info';

													} elseif ($value == 0) {
														$helpIcon  = 'times-circle';
														$helpColor = 'danger';

													} else {
														$helpIcon  = 'question-circle';
														$helpColor = 'light';
													}
												?>
												<li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center <?php if ($value < 2) { echo 'text-'.$helpColor; } ?>"><?php echo $key; ?> <i class="fas fa-<?php echo $helpIcon; ?> fa-fw text-<?php echo $helpColor; ?>"></i></li>
											<?php } //foreach ?>

										</ul>
									</div><!--/.card-->

								</div><!--/.collapse-->
							</div><!--/.confidence-tests-->

						</div>
						<div class="col-xs-12 col-sm-6 col-xl-3 1d-flex align-items-stretch mb-3">

							<?php $confidenceRating = getRating($confidenceScoreJOOMLA); ?>

							<div class="d-flex flex-row flex-fill align-items-stretch border bg-white shadow-sm border bg-white shadow-sm confidence-joomla" style="position:relative;min-height:130px;">
								<div class="flex-fill p-3">
									<h2 class="lead text-body"><?php echo $lang['FRCA_CONFDASH_JOOMLA_HEADING']; ?></h2>
									<p class="small m-0">
										<?php if ( $instance['instanceFOUND'] == 1 ) { ?>
											<?php echo $lang['FRCA_CONFDASH_JOOMLA_TEXT']; ?>
										<?php } else { ?>
											Joomla! not found, rating disabled.
										<?php } ?>
									</p>
								</div>
								<div class="p-1 d-flex flex-column align-items-center justify-content-center border-left" style="min-width:85px!important;">
									<h3 class="h1 m-0 text-<?php echo $confidenceRating['color']; ?>"><?php echo $confidenceRating['rating']; ?></h3>
									<span class="badge badge-pill badge-light"><?php echo round($confidenceScoreJOOMLA, 1); ?>%</span>
								</div>
							</div><!--/.confidence-joomla-->

							<span class="bg-light" data-toggle="popover" data-trigger="hover" data-placement="top" data-fallbackplacement="flip" data-title="Confidence Audit Help" data-content="Click the icon to review the basic audit tests and results that determine this rating" style="position:absolute;bottom:-9px;right:3px;z-index:1;border-radius:50%;padding:4px 2px 2px 3px;">
								<i class="fas fa-info-circle fa-lg text-<?php echo $confidenceRating['color']; ?>" data-toggle="collapse" data-target="#collapseExampleJOOMLA" aria-expanded="false" aria-controls="collapseExampleJOOMLA"></i>
							</span>

							<div class="w-100 text-right confidence-tests" style="position:relative;">
								<div class="collapse text-left shadow" id="collapseExampleJOOMLA" style="position:absolute;top:15px;left:0;right:0;z-index:2;">

									<div class="card">
										<ul class="list-group list-group-flush">
											<li class="list-group-item"><h4 class="h5 m-0 p-0 text-center">Joomla Audit Results</h4></li>

											<?php
												// sort the array alphabetically by key if score is 100% else sort by ascending value
												if ($confidenceScoreJOOMLA == 100) {
													ksort($confidence['JOOMLA']);
												} else {
													ksort($confidence['JOOMLA']);
													asort($confidence['JOOMLA']);
												}
											?>
											<?php foreach($confidence['JOOMLA'] AS $key => $value) { ?>
												<?php
													if ($value == 2) {
														$helpIcon	= 'check-circle';
														$helpColor	= 'success';

													} elseif ($value == 1) {
														$helpIcon	= 'info-circle';
														$helpColor	= 'info';

													} elseif ($value == 0) {
														$helpIcon	= 'times-circle';
														$helpColor	= 'danger';

													} else {
														$helpIcon	= 'question-circle';
														$helpColor	= 'light';
													}
												?>
												<li class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center <?php if ($value < 2) { echo 'text-'.$helpColor; } ?>"><?php echo $key; ?> <i class="fas fa-<?php echo $helpIcon; ?> fa-fw text-<?php echo $helpColor; ?>"></i></li>
											<?php } //foreach ?>

										</ul>
									</div><!--/.card-->

								</div><!--/.collapse-->
							</div><!--/.confidence-tests-->

						</div>
						<div class="col-xs-12 col-sm-6 col-xl-3 1d-flex align-items-stretch mb-3">

							<?php
								if ($instance['instanceFOUND'] == 1) {
									$confidenceScoreAGGREGATE = ($confidenceScoreSERVER + $confidenceScorePHP + $confidenceScoreJOOMLA) / 3;

								} else {
									$confidenceScoreAGGREGATE = ($confidenceScoreSERVER + $confidenceScorePHP) / 2;
								}

								// testing
								//$confidenceScoreAGGREGATE	= 19;

								$confidenceRating = getRating($confidenceScoreAGGREGATE);

							?>
							<div class="d-flex flex-row flex-fill align-items-stretch border bg-white shadow-sm confidence-aggregate" style="position:relative;min-height:130px;">
								<div class="flex-fill p-3">
									<h2 class="lead text-body" style="font-weight:500;"><?php echo $lang['FRCA_CONFDASH_AGGREGATE_HEADING']; ?></h2>
									<p class="small m-0">
									<?php echo $lang['FRCA_CONFDASH_AGGREGATE_TEXT']; ?>
									</p>
								</div>
								<div class="p-1 d-flex flex-column align-items-center justify-content-center border-left bg-<?php echo $confidenceRating['color']; ?>" style="min-width:85px!important;">
									<h3 class="h1 m-0 text-white" style="font-weight:500;"><?php echo $confidenceRating['rating']; ?></h3>
									<span class="badge badge-pill badge-light bg-white"><?php echo round($confidenceScoreAGGREGATE, 1); ?>%</span>
								</div>
							</div><!--/.confidence-aggregate-->

						</div>
					</div><!--/.row confidence grid-->

					<?php if ( defined('_FRCA_DEV' )) { ?>
						<button class="btn btn-frca btn-sm mb-3" type="button" data-toggle="collapse" data-target="#showconfidenceDEV" aria-expanded="false" aria-controls="showconfidenceDEV">
							<i class="fab fa-dev fa-fw fa-lg"></i> <?php echo $lang['FRCA_DEVMI']; ?>
						</button>
						<div class="row collapse" id="showconfidenceDEV">
							<div class="col-xs-12 col-sm-6 col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Server Confidence</h6>
										Items:<span class="float-right">[<?php echo count($confidence['SERVER']); ?>]</span><br />
										Weighted Value:<span class="float-right">[<?php echo $countConfidenceSERVER; ?>]</span><br />
										Actual Score:<span class="float-right">[<?php echo number_format( array_sum($confidence['SERVER']) / $countConfidenceSERVER, 5 ); ?>]</span><br />
										<hr />
										<?php
											echo '<pre class="xsmall" style="height:280px;">';
											var_dump( $confidence['SERVER']);
											echo '</pre>';
										?>
									</div>

							</div>
							<div class="col-xs-12 col-sm-6 col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">PHP Confidence</h6>
										Items:<span class="float-right">[<?php echo count($confidence['PHP']); ?>]</span><br />
										Weighted Value:<span class="float-right">[<?php echo $countConfidencePHP; ?>]</span><br />
										Actual Score:<span class="float-right">[<?php echo number_format( array_sum($confidence['PHP']) / $countConfidencePHP, 5 ); ?>]</span><br />
										<hr />
										<?php
											echo '<pre class="xsmall" style="height:280px;">';
											var_dump( $confidence['PHP']);
											echo '</pre>';
										?>
									</div>

							</div>
							<div class="col-xs-12 col-sm-6 col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Joomla Confidence</h6>
										<?php if ( $instance['instanceFOUND'] == 1 ) { ?>
											Items:<span class="float-right">[<?php echo count($confidence['JOOMLA']); ?>]</span><br />
											Weighted Value:<span class="float-right">[<?php echo $countConfidenceJOOMLA; ?>]</span><br />
											Actual Score:<span class="float-right">[<?php echo number_format( array_sum($confidence['JOOMLA']) / $countConfidenceJOOMLA, 5 ); ?>]</span><br />
											<hr />
											<?php
												echo '<pre class="xsmall" style="height:280px;">';
												var_dump( $confidence['JOOMLA']);
												echo '</pre>';
											?>
										<?php } else { ?>
											Instance Not Found!
										<?php } ?>
									</div>

							</div>
							<div class="col-xs-12 col-sm-6 col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Aggregate Confidence</h6>
										Combined Score:<span class="float-right">[<?php echo number_format( $confidenceScoreAGGREGATE, 5 ); ?>]</span><br />
										Rounded Score:<span class="float-right">[<?php echo round( $confidenceScoreAGGREGATE, 1 ); ?>]</span><br />
									</div>

							</div>
						</div>
					<?php } //developr-mode ?>

				</div><!--/.container-->
			</section>





<?php
// TESTING getPDC  FAKE DEMO ERRORS
getPDC( '1', 'A000' );  // produces a 0000 PDC as it doesn't exist
getPDC( '1', '0052' );
getPDC( '1', '0053' );
// INCLUDE FAKE VEL RECORD
$problemList['CRITICAL']['V692'] = array(
	'heading'		=> 'DemoVEL - J2Store (XSS Cross Site Scripting)',
	'description'	=> htmlspecialchars_decode('Type: package<br />J2Store,3.9.x,XSS (Cross Site Scripting)
	Update to 3.3.11  https://www.j2store.org/blog/general/j2store-3-3-11-released-with-improvements-and-a-security-fix.html'),
	'category' 		=> 'vel',
	'severity'		=> '2',
	'symptoms'		=> array(
		'0'	=> '1.2.1',
		'1'	=> '3.3.09',
		'2'	=> '-',
		'3'	=> ''
	),
	'actions'		=> array(
		'0'	=> 'Unknown',
		'1'	=> '- / -',
		'2'	=> 'https://j2store.org',
		'3'	=> ''
	),
	'velstatus'		=> 'Live',
	'velaccuracy'	=> '90',
	'problemcode'	=> 'V692'
);





// need to get exension list before velCOMPARe
// TODO: combine all the extensions, we only need one list and will save merging it later
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


// orig location
//include_once( 'frca-velcompare.php' );

//include_once ('frca-getveldata.php');
include_once ( 'frca-velcompare.php' );

?>

			<section class="pt-2" id="frca-problem-discovery">
				<div class="container pt-4 pb-3">

					<?php
					/**
					 * initial problem diagnosis grouped by Impact & Severity
					 *
					 * ITIL PROBLEM TYPE category (tabs)
					 * where the "problem type" category describes the effect/result of any discovered issue
					 * on successfully installing, running or maintaining of a Joomla! instance
					 *
					 *  |------ ITIL PROBLEM CATEGORY ------|---- Non-ITIL ----|
					 *  |  Critical  |  Moderate  |  Minor  |   Best Practice  |
					 *  |  (danger)  |  (warning) |  (info) |      (frca)      |
					 *  |-----------------------------------|------------------|
					 *
					 *
					 * ITIL SEVERITY level (tab content-pane sub-headings)
					 * where the "severity" describes the level of impact to, or impairment of, a Joomla! instance
					 *
					 *  |----------- ITIL Severity ---------|
					 *  |      1     |      2     |    3    |
					 *  |    High    |   Medium   |   Low   |
					 *  |  (danger)  |  (warning) | (info)  |
					 *  |-----------------------------------|
					 *
					 */
					?>
					<?php
						$issuecountCRITICAL		= count($problemList['CRITICAL']);
						$issuecountMODERATE		= count($problemList['MODERATE']);
						$issuecountMINOR		= count($problemList['MINOR']);
						$issuecountBESTPRACTICE	= count($problemList['BESTPRACTICE']);

						if ( $issuecountCRITICAL > 0 ) {
							$critTabActive			= 'active';
							$critAriaSelected		= 'true';
							$critTabContentActive	= 'active show';

						} elseif ( $issuecountMODERATE > 0 ) {
							$warnTabActive			= 'active';
							$warnAriaSelected		= 'true';
							$warnTabContentActive	= 'active show';

						} elseif ( $issuecountMINOR > 0 ) {
							$infoTabActive			= 'active';
							$infoAriaSelected		= 'true';
							$infoTabContentActive	= 'active show';

						} elseif ( $issuecountBESTPRACTICE > 0 ) {
							$bestTabActive			= 'active';
							$bestAriaSelected		= 'true';
							$bestTabContentActive	= 'active show';

						} else {
							$critTabActive			= '';
							$critAriaSelected		= 'false';
							$critTabContentActive	= '';
							$warnTabActive			= '';
							$warnAriaSelected		= 'false';
							$warnTabContentActive	= '';
							$infoTabActive			= '';
							$infoAriaSelected		= 'false';
							$infoTabContentActive	= '';
							$bestTabActive			= '';
							$bestAriaSelected		= 'false';
							$bestTabContentActive	= '';
						}

/*
						if ( count($problem['CRITICAL']['ISSUES']) > 0 ) {
							$critTabDisabled	= '';
							$critBadgeDisplay	= 'd-inline';
							$critBadgeColor		= 'danger';
							$critAriaDisabled	= 'false';
							$critMessage		= 'Showstopper issues that will cause fatal errors and installation or upgrade failures.';

						} else {
							$critTabDisabled	= 'disabled';
							$critBadgeDisplay	= 'd-none';
							$critBadgeColor		= 'light';
							$critAriaDisabled	= 'true';
							$critMessage		= 'Good News! No critical issues were detected.';
						}

						if ( count($problem['MODERATE']['ISSUES']) > 0 ) {
							$warnTabDisabled	= '';
							$warnBadgeDisplay	= 'd-inline';
							$warnBadgeColor		= 'warning';
							$warnAriaDisabled	= 'false';
							$warnMessage		= 'Potential issues that may cause difficulties with installations, updates or specific features and functions.';

						} else {
							$warnTabDisabled	= 'disabled';
							$warnBadgeDisplay	= 'd-none';
							$warnBadgeColor		= 'light';
							$warnAriaDisabled	= 'true';
							$warnMessage		= 'Good News! No moderate issues were detected.';
						}

						if ( count($problem['MINOR']['ISSUES']) > 0 ) {
							$infoTabDisabled	= '';
							$infoBadgeDisplay	= 'd-inline';
							$infoBadgeColor		= 'info';
							$infoAriaDisabled	= 'false';
							$infoMessage		= 'Minor issues that may reduce or limit feature functionality or administration and maintenace tasks.';

						} else {
							$infoTabDisabled	= 'disabled';
							$infoBadgeDisplay	= 'd-none';
							$infoBadgeColor		= 'light';
							$infoAriaDisabled	= 'true';
							$infoMessage		= 'Good News! No minor issues were detected.';
						}

						if ( count($problem['BESTPRACTICE']['ISSUES']) > 0 ) {
							$bestTabDisabled	= '';
							$bestBadgeDisplay	= 'd-inline';
							$bestBadgeColor		= 'frca';
							$bestAriaDisabled	= 'false';
							$bestMessage		= 'Recommendations that could potentially enhance performance, security, productivity or rankings.';

						} else {
							$bestTabDisabled	= 'disabled';
							$bestBadgeDisplay	= 'd-none';
							$bestBadgeColor		= 'light';
							$bestAriaDisabled	= 'true';
							$bestMessage		= 'Good News! All common best practices are in place.';
						}
*/

						function getDiscoveryTab($issueCount) {

							global $lang;

							if ( $issueCount > 0 ) {
								$discoveryTabResult = array(
									'tabDisabled'		=> '',
									'tabBadgeDisplay'	=> 'd-inline',
									//'tabBadgeDisabled'	=> '',
									'tabAriaDisabled'	=> 'false'
									//'tabMessage'		=> 'Recommendations that could potentially enhance performance, security, productivity or rankings.'
								);

							} else {
								$discoveryTabResult = array(
									'tabDisabled'		=> 'disabled',
									'tabBadgeDisplay'	=> 'd-none',
									//'tabBadgeDisabled'	=> 'badge-light',
									'tabAriaDisabled'	=> 'true',
									'tabMessage'		=> $lang['FRCA_NOPROBS_DESC']
								);

							}
							return $discoveryTabResult;

						}


						/*
						$reportType		 		= _FRCA_REPTYPE_DEFAULT;   // default, pre-requisite, pre-install, problem-install, post-install - selected below
						$cmsFound				= '0';  // found basic files
						$cmsConfigured			= '0';  // found configuration.php
						$cmsInstalled			= '0';  // has DB credentials
						$cmsDBAccess			= '0';  // can connect to DB

						if ( $cmsFound == '1' AND $cmsConfigured == '1' AND $cmsInstalled == '1' AND $cmsDBAccess == '1' ) {
							$reportType = _FRCA_REPTYPE_POSTINS;

						} elseif ( $cmsFound == '1' AND $cmsConfigured == '1' AND $cmsDBAccess == '0' ) {
							$reportType = _FRCA_REPTYPE_PROBINS;

						} elseif ( $cmsFound == '1' AND $cmsConfigured == '0' ) {
							$reportType = _FRCA_REPTYPE_PREINS;

						} elseif ( $cmsFound == '0' ) {
							$reportType = _FRCA_REPTYPE_PREREQ;
						}
						*/
					?>

					<h1 class="font-weight-light pb-2 border-bottom">
						<i class="fas fa-window-restore fa-sm text-muted"></i> Problem Discovery
					</h1>
					<!--
					<p class="text-dark"><?php echo $lang['FRCA_DASHBOARD_CONFIDENCE_NOTE']; ?></p>
					-->



					<ul class="nav nav-pills 1flex-column 1flex-md-row mx-n3" id="rca-severity-tabs" role="tablist">

						<?php $discoveryTab = getDiscoveryTab($issuecountCRITICAL); ?>
						<li class="nav-item 1px-0 1px-md-3 col-xs-12 col-sm-6 col-xl-3 mb-3 d-flex align-items-stretch" role="presentation">
							<a class="flex-fill nav-link border position-relative shadow-sm <?php echo $discoveryTab['tabDisabled']; ?> <?php echo $critTabActive; ?>" data-toggle="tab" href="#critical-items" id="critical-items-tab" role="tab" aria-controls="critical-items" aria-selected="<?php echo $critAriaSelected; ?>" aria-disabled="<?php echo $discoveryTab['tabAriaDisabled']; ?>">
								<span class="badge badge-pill badge-danger <?php //echo $discoveryTab['tabBadgeDisabled']; ?> <?php echo $discoveryTab['tabBadgeDisplay']; ?> position-absolute"><?php echo $issuecountCRITICAL; ?></span>
								<span class="sr-only">Critical Items</span>
								<span class="lead">
									<i class="fas fa-minus-circle fa-2x d-block my-2"></i> <?php echo $lang['FRCA_CRITICAL']; ?>
								</span>
								<p class="small">
									<?php if ($issuecountCRITICAL > 0) { ?>
										<?php echo $lang['FRCA_CRITICAL_DESC']; ?>
									<?php } else { ?>
										<?php echo $discoveryTab['tabMessage']; ?>
									<?php } ?>

								</p>
							</a>
						</li>

						<?php $discoveryTab = getDiscoveryTab($issuecountMODERATE); ?>
						<li class="nav-item 1px-0 1px-md-3 col-xs-12 col-sm-6 col-xl-3 mb-3 d-flex align-items-stretch" role="presentation">
							<a class="flex-fill nav-link border position-relative shadow-sm <?php echo $discoveryTab['tabDisabled']; ?> <?php echo $warnTabActive; ?>" data-toggle="tab" href="#moderate-items" id="moderate-items-tab" role="tab" aria-controls="moderate-items" aria-selected="<?php echo $warnAriaSelected; ?>" aria-disabled="<?php echo $discoveryTab['tabAriaDisabled']; ?>">
								<span class="badge badge-pill badge-warning <?php //echo $discoveryTab['tabBadgeDisabled']; ?> <?php echo $discoveryTab['tabBadgeDisplay']; ?> position-absolute"><?php echo $issuecountMODERATE; ?></span>
								<span class="sr-only">Moderate Items</span>
								<span class="lead">
									<i class="fas fa-exclamation-circle fa-2x d-block my-2"></i> <?php echo $lang['FRCA_MODERATE']; ?>
								</span>
								<p class="small">
									<?php if ($issuecountMODERATE > 0) { ?>
										<?php echo $lang['FRCA_MODERATE_DESC']; ?>
									<?php } else { ?>
										<?php echo $discoveryTab['tabMessage']; ?>
									<?php } ?>
								</p>
							</a>
						</li>

						<?php $discoveryTab = getDiscoveryTab($issuecountMINOR); ?>
						<li class="nav-item 1px-0 1px-md-3 col-xs-12 col-sm-6 col-xl-3  mb-3 d-flex align-items-stretch" role="presentation">
							<a class="flex-fill nav-link border position-relative shadow-sm <?php echo $discoveryTab['tabDisabled']; ?> <?php echo $infoTabActive; ?>" data-toggle="tab" href="#minor-items" id="minor-items-tab" role="tab" aria-controls="minor-items" aria-selected="<?php echo $infoAriaSelected; ?>" aria-disabled="<?php echo $discoveryTab['tabAriaDisabled']; ?>">
								<span class="badge badge-pill badge-info <?php //echo $discoveryTab['tabBadgeDisabled']; ?> <?php echo $discoveryTab['tabBadgeDisplay']; ?> position-absolute"><?php echo $issuecountMINOR; ?></span>
								<span class="sr-only">Minor Items</span>
								<span class="lead">
									<i class="fas fa-info-circle fa-2x d-block my-2"></i> <?php echo $lang['FRCA_MINOR']; ?>
								</span>
								<p class="small">
									<?php if ($issuecountMINOR > 0) { ?>
										<?php echo $lang['FRCA_MINOR_DESC']; ?>
									<?php } else { ?>
										<?php echo $discoveryTab['tabMessage']; ?>
									<?php } ?>
								</p>
							</a>
						</li>

						<?php $discoveryTab = getDiscoveryTab($issuecountBESTPRACTICE); ?>
						<li class="nav-item 1px-0 1px-md-3 col-xs-12 col-sm-6 col-xl-3 mb-3 d-flex align-items-stretch" role="presentation">
							<a class="flex-fill nav-link border position-relative shadow-sm <?php echo $discoveryTab['tabDisabled']; ?> <?php echo $bestTabActive; ?>" data-toggle="tab" href="#bestpractice-items" id="bestpractice-items-tab" role="tab" aria-controls="bestpractice-items" aria-selected="<?php echo $bestAriaSelected; ?>" aria-disabled="<?php echo $discoveryTab['tabAriaDisabled']; ?>">
								<span class="badge badge-pill badge-frca <?php //echo $discoveryTab['tabBadgeDisabled']; ?> <?php echo $discoveryTab['tabBadgeDisplay']; ?> position-absolute"><?php echo $issuecountBESTPRACTICE; ?></span>
								<span class="sr-only">Best Practice Recommendations</span>
								<span class="lead">
									<i class="fas fa-plus-circle fa-2x d-block my-2"></i> <?php echo $lang['FRCA_BESTPRACTICE']; ?>
								</span>
								<p class="small">
									<?php if ($issuecountBESTPRACTICE > 0) { ?>
										<?php echo $lang['FRCA_BESTPRACTICE_DESC']; ?>
									<?php } else { ?>
										<?php echo $discoveryTab['tabMessage']; ?>
									<?php } ?>
								</p>
							</a>
						</li>

					</ul><!--/problemCategory nav-tabs-->



					<?php if ( defined('_FRCA_DEV' )) { ?>
						<button class="btn btn-frca btn-sm mb-3" type="button" data-toggle="collapse" data-target="#showimpactDEV" aria-expanded="false" aria-controls="showimpactDEV">
							<i class="fab fa-dev fa-fw fa-lg"></i> <?php echo $lang['FRCA_DEVMI']; ?>
						</button>
						<div class="row collapse" id="showimpactDEV">
							<div class="col-xs-12 col-sm-6 1col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Critical Items</h6>
										<?php
											echo '<pre class="xsmall" style="height:280px;">';
											var_dump( $problemList['CRITICAL']);
											echo '</pre>';
										?>
									</div>

							</div>
							<div class="col-xs-6 col-sm-6 1col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Moderate Items</h6>
										<?php
											echo '<pre class="xsmall" style="height:280px;">';
											var_dump( $problemList['MODERATE']);
											echo '</pre>';
										?>
									</div>

							</div>
							<div class="col-xs-12 col-sm-6 1col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Minor Items</h6>
										<?php
											echo '<pre class="xsmall" style="height:280px;">';
											var_dump( $problemList['MINOR']);
											echo '</pre>';
										?>
									</div>

							</div>
							<div class="col-xs-12 col-sm-6 1col-xl-3">

									<div class="alert bg-white text-dark border border-frca shadow w-100">
										<h6 class="text-frca m-0 p-0">Best Practice Items</h6>
										<?php
											echo '<pre class="xsmall" style="height:280px;">';
											var_dump( $problemList['BESTPRACTICE']);
											echo '</pre>';
										?>
									</div>

							</div>
						</div>
					<?php } //developr-mode ?>




					<div class="tab-content mb-4 mt-5" id="rca-severity-detail">

						<div class="tab-pane fade <?php echo $critTabContentActive; ?>" id="critical-items" role="tabpanel" aria-labelledby="critical-items-tab">

							<h3 class="font-weight-light border-bottom">Critical Issues</h3>
							<p>CRITICAL Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>

							<!-- TESTING DNS -->
							<!--<pre>-->
							<?php
							//$result = dns_get_record('aqualis.com.au', DNS_ANY, $authns, $addtl);
							//var_dump($result);
							//var_dump($authns);
							//var_dump($addtl);
							//$stuff = checkdnsrr(idn_to_ascii('hotmango.me'), 'ANY');
							//var_dump($stuff);
							?>
							<!--</pre>-->

							<?php





///echo '<pre>';
///var_dump($problemList);
///echo '</pre>';
					?>
							<!-- TESTING DNS -->

							<?php
							/**
							 * problem layoutview mode
							 *
							 * (c) compact  : 1x problem per grid column + carousel panels for additional info
							 * (e) expanded : 1x problem per grid row + 3x panels for additional info
							 *
							 * uses PHP_SESSION['layoutview'] to maintain user choice
							 * added @RussW 16 Dec 2020
							 */
							?>
							<?php if ($layoutview == 'e') { // (e) expanded layoutview ?>

								<div class="row expanded-problem-item">
									<div class="col-12 mb-3">

										<div class="card shadow-sm problem-card">
											<div class="card-header bg-primary text-white" style="min-height:85px;">

												MySQLi is not supported by your version of PHP.

											</div><!--card-header-->
											<div class="card-body p-0">

												<div class="d-flex flex-row justify-content-end bg-light px-3 py-2 mb-3 border-bottom problem-status-bar">
													<div class="text-left">
														<span class="xsmall">CATEGORY:</span> <span class="badge badge-pill badge-primary font-weight-bold">PHP</span>
													</div>
													<div class="text-right">
														<span class="xsmall">SEVERITY:</span> <span class="badge badge-pill badge-danger font-weight-bold">HIGH</span> <span class="badge badge-pill badge-secondary font-weight-normal" data-toggle="tooltip" data-placement="top" title="Suggested Problem Resolution Priority">P1</span>
													</div>
												</div><!--/.row problem-status-bar-->

												<div class="row px-3 problem-detail-panels">
													<div class="col-md-6 col-xl-4 mb-3 problem-detail-desc" style="border:1px solid blue;">Problem Description</div>
													<div class="col-md-6 col-xl-4 mb-3 problem-detail-info" style="border:1px solid blue;">Likely To Effect</div>
													<div class="col-md-6 col-xl-4 mb-3 problem-detail-actions" style="border:1px solid blue;">Recommended Actions</div>
												</div><!--/.row .problem-detail-panels-->

											</div><!--/.card-body-->
											<div class="card-footer problem-detail-symptoms">

												Possible Symptoms:

											</div>
										</div><!--/.card .problem-card-->

									</div><!--/.col-*-->
								</div><!--/.row .expanded-problem-item-->

							<?php } else { // (c) comapct layoutview (default) ?>

								<div class="row compact-problem-item">



<?php
//echo '<pre>';
//var_dump($problemList['CRITICAL']);
//echo '</pre>';

?>



									<?php
									// TODO: make this into a function for all issue categories
										/**
										 * uasort the problem/issue array by the problem code first
										 * - primarily to sort VEL entries by newest first using their VEL "id"
										 *   (the larger the VEL id, the newer the entry)
										 * - then by usort by severity, 1, 2, 3, 4 (& UC)
										 *
										 * added 20-dec-2020 @RussW
										 */
										uasort( $problemList['CRITICAL'], function( $a, $b ) {

											// sort by problemcode number
											if ( $a['problemcode'] === $b['problemcode'] ) {
												return 0;
											}
											return ( $a['problemcode'] < $b['problemcode'] ) ? 1 : -1;

										} );

										usort( $problemList['CRITICAL'], function( $a, $b ) {

											// sort by severity
											if ( $a['severity'] === $b['severity'] ) {
												return 0;
											}
											return ( $a['severity'] < $b['severity'] ) ? -1 : 1;

										} );

									?>



									<?php foreach ($problemList['CRITICAL'] as $problemkey => $item) { ?>
										<?php
											$headBgColor	= 'primary';
											$catColor		= 'primary';

											if ( stristr($item['category'], 'server') !== FALSE OR stristr($item['category'], 'host') !== FALSE OR stristr($item['category'], 'resources') !== FALSE) {
												$headingIcon = 'fas fa-server';

											} elseif ( stristr($item['category'], 'database') !== FALSE OR stristr($item['category'], 'sql') !== FALSE ) {
												$headingIcon = 'fas fa-database';

											} elseif ( stristr($item['category'], 'php') !== FALSE ) {
												$headingIcon = 'fab fa-php';

											} elseif ( stristr($item['category'], 'joomla') !== FALSE ) {
												$headingIcon = 'fab fa-joomla';

											} elseif ( stristr($item['category'], 'template') !== FALSE ) {
												$headingIcon = 'fas fa-desktop';

											} elseif ( stristr($item['category'], 'extension') !== FALSE OR stristr($item['category'], 'component') !== FALSE OR stristr($item['category'], 'module') !== FALSE Or stristr($item['category'], 'plugin') !== FALSE) {
												$headingIcon = 'fas fa-cubes';

											} elseif ( stristr($item['category'], 'disk') !== FALSE OR stristr($item['category'], 'hdd') !== FALSE) {
												$headingIcon = 'fas fa-hdd';

											} elseif ( stristr($item['category'], 'network') !== FALSE ) {
												$headingIcon = 'fas fa-network-wired';

											} elseif ( stristr($item['category'], 'security') !== FALSE ) {
												$headingIcon = 'fas fa-user-secret';
												$catColor	= 'danger';

											} elseif ( stristr($item['category'], 'performance') !== FALSE ) {
												$headingIcon = 'fas fa-tachometer-alt';

											} elseif ( stristr($item['category'], 'seo') !== FALSE ) {
												$headingIcon = 'fab fa-searchengin';

											} elseif ( stristr($item['category'], 'vulnerability') !== FALSE OR stristr($item['category'], 'vel') !== FALSE ) {
												$headingIcon 	= 'fas fa-radiation-alt';
												$headBgColor	= 'vel';
												$catColor		= 'vel';

											} elseif ( stristr($item['category'], 'fishikawa') !== FALSE ) {
												$headingIcon 	= 'fas fa-fish';
												//$headBgColor	= 'light text-warning border-bottom-0';
												$headBgColor	= 'frca-dark';
												$catColor		= 'frca-dark';

											} else {
												$headingIcon = 'fas fa-chalkboard';
											}

											if ($item['severity'] == '1') {
												$severityBadgeColor = 'danger';

											} elseif ($item['severity'] == '2') {
												$severityBadgeColor = 'warning';

											} elseif ($item['severity'] == '3') {
												$severityBadgeColor = 'info';

											} elseif ($item['severity'] == '4') {
												$severityBadgeColor = 'frca';

											} else {  // other/non-rated
												$severityBadgeColor = 'primary';
											}
										?>

										<div class="col-md-6 col-lg-4 mb-5 d-flex">

											<div class="card shadow-sm flex-fill 1w-100 align-items-stretch problem-card">
												<div class="card-header d-flex align-items-center bg-<?php echo $headBgColor; ?> text-white p-2" style="min-height:66px;">
													<div class="1h-100 float-left d-flex align-items-center">
														<i class="<?php echo $headingIcon; ?> fa-fw fa-2x mr-1"></i>
													</div>
												<?php
												//echo $item['HEADING'];
												//echo substr($item['HEADING'], 0, strrpos(substr( $item['HEADING'], 0, 35), ' '));
												if (strlen($item['heading']) < 80) {
													echo $item['heading'];
												} else {
													echo substr($item['heading'], 0, 82) .'...';
												}

												?>

												</div><!--card-header-->
												<div class="card-body p-0">

													<div class="row no-gutters bg-light px-3 py-2 problem-status-bar">
														<div class="col-6 text-left">
															<span class="small">Category:</span> <span class="badge badge-pill badge-light bg-white text-<?php echo $catColor; ?> xsmall text-uppercase" style="font-weight:500;"><?php echo $item['category']; ?></span>
														</div>
														<div class="col-6 text-right">
															<span class="small">
																<?php if ( strtolower($item['category']) == 'vel' OR strtolower($item['category']) == 'vulnerability' ) { ?>
																	Risk:
																<?php } else { ?>
																	Severity:
																<?php } ?>
															</span>
															<span class="badge badge-pill badge-<?php echo $severityBadgeColor; ?> font-weight-bold text-uppercase xsmall ml-1">
																<?php if ( strtolower($item['category']) == 'vel' or strtolower($item['category']) == 'vulnerability' ) { ?>

																	<?php
																		if ( $item['severity'] == '1' ) {
																			$riskLevel = $lang['FRCA_RISKHIGH'];

																		} elseif ( $item['severity'] == '2' ) {
																			$riskLevel = $lang['FRCA_RISKMEDIUM'];

																		} elseif ( $item['severity'] == '3' ) {
																			$riskLevel = $lang['FRCA_RISKLOW'];

																		} else {
																			$riskLevel = $lang['FRCA_RISKUC'];
																			//$riskLevel = '4';
																		}
																		echo $riskLevel;
																	?>

																<?php } else { ?>
																	<?php echo $item['severity']; ?>
																<?php } ?>
															</span>
														</div>
													</div><!--/.row .problem-status-bar-->


													<?php //if ( strtolower($item['category']) == 'vel' ) { ?>
													<?php if ( isset($item['velaccuracy']) ) { ?>
														<div class="progress" style="height:0.35rem;" data-toggle="popover" data-trigger="focus hover" data-placement="auto" title="Vulnerability Report Match Accuracy : <?php echo $item['velaccuracy']; ?>%" data-content="Due to the nature of reporting vulnerable extensions there is the possiblity of false-positives, this rating indicates the calculated match accuracy of this extension compared to a reported vulnerability. Please check the developer website to confirm this vulnerability report.">
															<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width:<?php echo $item['velaccuracy']; ?>%" aria-valuenow="<?php echo $item['velaccuracy']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
													<?php } else { ?>
														<div class="spacer" style="height:0.35rem;"></div>
													<?php } ?>



													<!--carousel-->
													<div id="problemCarousel-<?php echo $problemkey; ?>" class="carousel slide" data-ride="carousel" data-interval="false">

														<div class="carousel-inner problem-detail-panels 1mb-2">

															<div class="carousel-item 1border problem-detail-desc active">
																<div class="1carousel-caption 1d-none 1d-md-block 1border card-body">
																	<h4 class="h6 text-<?php echo $catColor; ?>">
																		<i class="fas fa-book-reader fa-fw"></i> <?php if (strtolower($item['category']) == 'vel') { echo 'Vulnerability'; } else { echo 'Problem'; } ?> Description

																	</h4>


																	<div class="card-body-text mb-3 pr-1" style="height:161px;overflow-y:auto;">
																		<?php echo strip_tags($item['description'], '<p>, <br>, <i>, <b>, <strong>, <u>'); ?>
																	</div>

																	<h5 class="h6 text-<?php echo $catColor; ?>" style="position:relative;">
																		<i class="fas fa-car-crash fa-fw"></i> <?php if (strtolower($item['category']) == 'vel') { echo 'Version Information'; } elseif (strtolower($item['category']) == 'fishikawa') { echo 'Additional Information'; } else { echo 'Likely Symptoms'; } ?>

																		<?php if ( strtolower($item['category']) == 'vel' or strtolower($item['category']) == 'vulnerability' ) { ?>
																			<?php
																				if ( empty($item['symptoms'][2]) and strtolower($item['velstatus']) == 'live' ) {

																					// has no patch/fix version & vel lists as live
																					$velStatus		= 'Not Fixed';
																					$velStatusColor	= 'danger';

																				} elseif ( !empty($item['symptoms'][2]) and strtolower($item['velstatus']) == 'resolved' ) {

																					// has patch/fix version & vel lists as resolved
																					$velStatus		= 'Fixed';
																					$velStatusColor	= 'success';

																				} else {

																					// has no patch/fix or status info or a is an invalid mix of patch/fix version & velstatus
																					$velStatus		= $lang['FRCA_U'];
																					$velStatusColor	= 'warning';

																				}
																			?>

																			<span class="badge badge-pill badge-<?php echo $velStatusColor; ?> text-capitalize xsmall ml-1" style="position:absolute;top:0;right:0;">
																				<?php echo $velStatus; ?>
																			</span>
																		<?php } ?>

																	</h5>
																	<div class="1px-3 1py-2 1border-top problem-symptoms" style="height:100px;overflow-y:auto;">

																		<ul class="list-group list-group-flush">
																			<?php if ( strtolower($item['category']) == 'vel' ) { ?>

																				<li class="list-group-item px-1 py-1 border-bottom bg-light">Installed Version: <span class="float-right"><?php echo strip_tags($item['symptoms'][0]); ?></span></li>
																				<li class="list-group-item px-1 py-1 border-bottom">Vulnerable From: <span class="float-right"><?php echo strip_tags($item['symptoms'][1]); ?></span></li>
																				<li class="list-group-item px-1 py-1 border-bottom bg-light">
																					<?php
																						if ( empty($item['symptoms'][2]) ) {
																							// no patch version listed, assume it's still out there in the wild
																							$fixedAt		= 'Not Fixed';
																							$fixedAtColor	= 'danger';
																						} else {
																							$fixedAt 		= $item['symptoms'][2];
																							$fixedAtColor	= 'success';
																						}
																					?>
																					<strong>Fixed At: <span class="text-<?php echo $fixedAtColor; ?> float-right"><?php echo strip_tags($fixedAt); ?></span></strong>
																				</li>
																				<li class="list-group-item px-1 py-1 border-bottom">Effected Version(s): <span class="float-right"><?php echo strip_tags($item['symptoms'][3]); ?></span></li>

																			<?php } else { ?>

																				<?php foreach ($item['symptoms'] as $symptomkey => $symptom) { ?>
																					<?php if ( !empty($symptom) ) { ?>
																						<li class="list-group-item px-1 py-1 border-bottom <?php if ($symptomkey % 2 == '0') { echo 'bg-light'; } ?>"><?php echo strip_tags($symptom, '<p>, <br>, <i>, <b>, <strong>, <u>, <span>'); ?></li>
																					<?php } ?>
																				<?php } // end foreach symptoms ?>

																			<?php } ?>
																		</ul>

																	</div><!--/.problem-symptoms or VEL data -->

																</div>
															</div><!--/.problem-detail-desc panel-->


															<?php if ( (!empty($item['causes']) or !empty($item['effects'])) and strtolower($item['category']) != 'vel' and strtolower($item['category']) != 'fishikawa') { ?>
															<div class="carousel-item 1border problem-detail-info">
																<div class="1carousel-caption 1d-none 1d-md-block 1border card-body">
																	<h2 class="h6 text-<?php echo $catColor; ?>"><i class="fas fa-book-open fa-fw"></i> Most Likely Causes</h2>

																	<div class="card-body-text mb-3" style="height:161px;overflow-y:auto;">
																		<ul class="list-group list-group-flush">
																			<?php foreach ($item['causes'] as $causekey => $cause) { ?>
																				<?php if ( !empty($cause) ) { ?>
																					<li class="list-group-item px-1 py-2 border-bottom <?php if ($causekey % 2 == '0') { echo 'bg-light'; } ?>"><?php echo strip_tags($cause, '<p>, <br>, <i>, <b>, <strong>, <u>'); ?></li>
																				<?php } ?>
																			<?php } // end foreach causes ?>
																		</ul>

																	</div><!--/.card-body-text-->

																	<h5 class="h6 text-<?php echo $catColor; ?>"><i class="fas fa-window-restore fa-fw"></i> Likely Effected Resources</h5>
																	<div class="1px-3 1py-2 1border-top problem-effects" style="height:100px;overflow-y:auto;">

																		<ul class="list-group list-group-flush">
																			<?php foreach ($item['effects'] as $effectkey => $effect) { ?>
																				<?php if ( !empty($effect) ) { ?>
																					<li class="list-group-item px-1 py-1 border-bottom <?php if ($effectkey % 2 == '0') { echo 'bg-light'; } ?>"><?php echo strip_tags($effect, '<p>, <br>, <i>, <b>, <strong>, <u>, <span>'); ?></li>
																				<?php } ?>
																			<?php } // end foreach causes ?>
																		</ul>

																	</div><!--/.problem-effects-->

																</div>
															</div><!--/.problem-detail-info panel-->
															<?php } // if !empty info panel data ?>


															<div class="carousel-item 1border problem-detail-actions">
																<div class="1carousel-caption 1d-none 1d-md-block 1border card-body">
																	<h2 class="h6 text-<?php echo $catColor; ?>"><i class="fas fa-vote-yea fa-fw"></i> Suggested Actions</h2>

																	<div class="card-body-text 1mb-3" style="height:303px;overflow-y:auto;">

																		<ul class="list-group list-group-flush">
																			<?php foreach ($item['actions'] as $actionkey => $action) { ?>
																				<?php if ( !empty($action) ) { ?>
																					<li class="list-group-item px-1 py-1 border-bottom <?php if ($actionkey % 2 == '0') { echo 'bg-light'; } ?>"><?php echo strip_tags($action, '<p>, <br>, <i>, <b>, <strong>, <u>, <span>'); ?></li>
																				<?php } ?>
																			<?php } // end foreach actions ?>
																		</ul>



																	</div><!--/.card-body-text-->

																</div>
															</div><!--/.problem-detail-actions panel-->
														</div><!--/.carousel-inner .problem-detail-panels -->
													</div>
													<!--carousel-->

													<span class="xsmall float-right mr-1"><?php echo $item['problemcode']; ?></span>

												</div><!--/.card-body-->
												<div class="card-footer bg-light 1bg-transparent px-0 py-2 border-top-0 problem-detail-symptoms">

													<div class="1border-top 1border-warning">
														<ol class="carousel-indicators mx-auto my-0" style="width:30%;position:unset;">
															<li class="bg-dark" data-target="#problemCarousel-<?php echo $problemkey; ?>" data-slide-to="0" class="active"></li>
															<li class="bg-dark" data-target="#problemCarousel-<?php echo $problemkey; ?>" data-slide-to="1"></li>
															<?php if ( (!empty($item['causes']) or !empty($item['effects'])) and strtolower($item['category']) != 'vel' and strtolower($item['category']) != 'fishikawa' ) { ?>
																<li class="bg-dark" data-target="#problemCarousel-<?php echo $problemkey; ?>" data-slide-to="2"></li>
															<?php } ?>
														</ol>

														<a class="carousel-control-prev" href="#problemCarousel-<?php echo $problemkey; ?>" role="button" data-slide="prev" style="width:10%;background-color:transparent;padding-bottom:11px;align-items:flex-end;">
															<span class="carousel-control-prev-icon bg-dark" aria-hidden="true" style="border-radius:50%;width:16px;height:16px;"></span>
															<span class="sr-only">Previous</span>
														</a>
														<a class="carousel-control-next" href="#problemCarousel-<?php echo $problemkey; ?>" role="button" data-slide="next" style="width:10%;background-color:transparent;padding-bottom:11px;align-items:flex-end;">
															<span class="carousel-control-next-icon bg-dark" aria-hidden="true" style="border-radius:50%;width:16px;height:16px;"></span>
															<span class="sr-only">Next</span>
														</a>
													</div><!--/carousel navigation-->

												</div><!--/.card-footer .problem-detail-symptoms-->
											</div><!--/.card .problem-card-->

										</div><!--col-*-->
									<?php } //item endforach ?>

								</div><!--/.row .compact-problem-item-->

							<?php } // $layoutview expanded or compact(default) ?>


						</div><!--/.tab-pane-->

						<div class="tab-pane fade <?php echo $warnTabContentActive; ?>" id="moderate-items" role="tabpanel" aria-labelledby="moderate-items-tab">


							<h3 class="font-weight-light border-bottom">Moderate Issues</h3>
							<p>WARN Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>


						</div><!--/.tab-pane-->

						<div class="tab-pane fade<?php echo $infoTabContentActive; ?>" id="minor-items" role="tabpanel" aria-labelledby="minor-items-tab">

							<h3 class="font-weight-light border-bottom">Minor Issues</h3>
							<p>INFO Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid. Aliquip placeat salvia cillum iphone. Seitan aliquip quis cardigan american apparel, butcher voluptate nisi qui.</p>

						</div><!--/.tab-pane-->

						<div class="tab-pane fade <?php echo $bestTabContentActive; ?>" id="bestpractice-items" role="tabpanel" aria-labelledby="bestpractice-items-tab">

							<h3 class="font-weight-light border-bottom">Best Practice Suggestions</h3>
							<p>BEST Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>

						</div><!--/.tab-pane-->

					</div><!--/.tab-content-->





				</div><!--/.container-->
			</section>


	</main>



	<footer class="bg-light mt-5" id="frca-footer">
		<div class="container text-center p-3 xsmall">
			<p class="mb-2 d-print-none" data-html2canvas-ignore="true">
				<?php echo $lang['LICENSE_FOOTER'] .' '. $lang['LICENSE_LINK']; ?>
			</p>
			<p class="mb-1">
				<?php echo $lang['FRCA_JDISCLAIMER'] ?>
			</p>
		</div>
		<div class="container-fluid bg-frca-dark text-white py-2">
			<p class="p-0 m-0 xsmall text-center">
				<?php echo _RES_FRCA_SHORT .' '. _RES_FRCA_VERSION .' ('. _RES_FRCA_CODENAME . ') '. _RES_FRCA_COPYRIGHT_STMT; ?><br />
				<small class="text-center">
					<?php echo '[ Release : '. _RES_FRCA_RELEASE .' ] [ Language : '.$lang['languagecode'] .' ]'; ?>
				</small>
			</p>
		</div>
	</footer>



	<?php
	/**
	 * security notification
	 *
	 * dismissable toast/alert to replace the space-hog on page message
	 * 5s display if doIT = 1, else 10s display
	 * @RussW 21-May-2020
	 *
	 */
	?>
	<script>
		var doIT = '<?php echo @$_POST['doIT']; ?>';
		if (doIT == '1') {
			var timeleft = 5;
		} else {
			var timeleft = 19;
		}
		var noticeTimer = setInterval(function() {
			if (timeleft <= 0) {
				clearInterval(noticeTimer);
				document.getElementById("countdown").innerHTML = "0s";
			} else {
				document.getElementById("countdown").innerHTML = timeleft + "s";
			}
			timeleft -= 1;
		}, 1000);
	</script>

	<div role="alert" aria-live="assertive" aria-atomic="true" class="toast position-fixed shadow d-print-none" data-html2canvas-ignore="true" style="bottom: 10px; right: 10px; z-index: 9999; width: 90%; max-width: 390px;" data-delay="<?php if (@$_POST['doIT'] == '1') {
																																																											echo 6000;
																																																										} else {
																																																											echo 20000;
																																																										} ?>" data-animation="false" id="securityToast">
		<div class="toast-header bg-danger text-white">
			<i class="fas fa-exclamation-circle fa-lg mr-2"></i>
			<span class="mr-auto">Security Notification</span>
			<span class="text-white" id="countdown"></span>
			<button type="button" class="ml-2 mb-1 text-white close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body text-dark text-left">
			<?php echo $lang['FRCA_DELNOTE_LN2']; ?>
			<?php echo $lang['FRCA_DELNOTE_LN3']; ?>
		</div>
	</div>



	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>





	<?php if (@$_POST['doPDF'] == '1') { ?>
		<?php
		/**
		 * load the export to PDF libaries and options
		 *
		 * added @RussW 03-Jun-2020
		 * html2pdf bundle
		 * - includes html2canvas
		 * - includes jsPDF
		 * also loads the pace progress bar in the head when invoked as the generation can take a little
		 * time and end-users could get confused  or lost whilst the PDF is being generated
		 *
		 * exports to "landscape by default, as there is way too much information to show in portrait
		 *
		 */
		?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js" integrity="sha256-1UYP03Qm8NpJtVQjd6OTwT9DjfgsYrNa8w1szRxBeqQ=" crossOrigin="anonymous"></script>
		<script>
			var filename = '<?php echo $_SERVER['SERVER_NAME'] . '-'; ?>';
			var element = document.getElementById('fpa-body');
			var opt = {
				margin: 10,
				pagebreak: {
					mode: 'css',
					after: '.pdf-break-after',
					before: '.pdf-break-before'
				},
				enableLinks: false,
				filename: filename + Date.now() + '.pdf',
				image: {
					type: 'jpeg',
					quality: 0.95
				},
				html2canvas: {
					scale: 2
				},
				jsPDF: {
					unit: 'mm',
					format: 'a4',
					orientation: 'l',
					compress: 'true',
					userUnit: 'px'
				}
			};

			// promise-based execution
			html2pdf().set(opt).from(element).save();
		</script>
	<?php } // doPDF = 1
	?>

	<script>
		/**
		 * activate BS popovers, tooltips (on hover) and toasts
		 * requires : jQuery & popper
		 * @RussW 23-May-2020
		 *
		 */
		$(function() {
			$('[data-toggle="popover"]').popover();
			$('[data-toggle="tooltip"]').tooltip();
			$('.toast').toast('show');

			var offset = 64;
			$('.dropdown-menu a.dropdown-item').click(function(event) {
				event.preventDefault();
				$($(this).attr('href'))[0].scrollIntoView();
				scrollBy(0, -offset);
			});
		});


		/**
		 * post output functions
		 * 1. hide the FPA options panel/form
		 * 2. count post output characters, if over 20k post a message to split forum posts
		 * 3. add an event listener to copy post output to clipboard when button clicked
		 *
		 * only executes child functions if doIT = 1
		 * @RussW 23-May-2020
		 *
		 */
		function doPostActions() {
			var doIT = '<?php echo @$_POST['doIT']; ?>';

			if (doIT == '1') {

				// hide the options panel/form and change button text (overrides toggleFPA)
				var eleOptions = document.getElementById('fpaOptions');
				var textButton = document.getElementById('fpaButton');
				eleOptions.style.display = 'none';
				textButton.innerHTML = '<i class="fas fa-chevron-circle-right"></i> Open the FPA Options';


				// count and display post characters
				var maxCharCount = '19850';
				var eleCount = document.getElementById('postOUTPUT');
				var countMessage = '<?php echo $lang['FRCA_INS_8']; ?>';
				if (eleCount.value.length > maxCharCount) {
					document.getElementById('postCharCount').innerHTML = '<div class="alert alert-warning text-white 1bg-white small my-1 p-3"><i class="fas fa-exclamation-triangle fa-2x d-block mb-2 text-center"></i>' + countMessage + '</div><div class="text-right mb-2"><span class="xsmall text-muted">Post Length:</span> <span class="badge badge-pill badge-warning">' + document.getElementById('postOUTPUT').value.length + '</span></div>';
				} else {
					document.getElementById('postCharCount').innerHTML = '<div class="text-right mb-2"><span class="xsmall text-muted">Post Length:</span> <span class="badge badge-pill badge-light">' + document.getElementById('postOUTPUT').value.length + '</span></div>';
				}


				// copy post output to clipboard
				function copyPost() {
					var copyText = document.querySelector('#postOUTPUT');
					copyText.select();
					copyText.setSelectionRange(0, 99999); /*for mobile devices*/
					document.execCommand('copy');
				}
				document.querySelector('#copyPOST').addEventListener('click', copyPost);

			} // doIT = 1
		}
		doPostActions();
	</script>



</body>

</html>
