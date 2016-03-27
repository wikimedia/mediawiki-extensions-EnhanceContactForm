<?php
/**
 * EnhanceContactForm -- improves Special:Contact by sending more information to
 * the recipient, specifically:
 *	-wiki URL ($wgServer)
 *	-wiki database name ($wgDBname)
 *	-reporter's IP address (regardless of whether they checked the "Include my IP address in this message" checkbox or not)
 *	-reporter's browser
 *  -the skin the reporter was using when they used Special:Contact
 *	-reporter's operating system
 *	-reporter's User-Agent string
 * MyInfo extension is required for the browser/OS/skin/UA detection.
 *
 * @file
 * @ingroup Extensions
 * @author Jack Phoenix <jack@shoutwiki.com>
 * @license https://en.wikipedia.org/wiki/Public_domain Public domain
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die();
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['other'][] = array(
	'name' => 'EnhanceContactForm',
	'version' => '0.6',
	'author' => 'Jack Phoenix',
	'url' => 'https://www.mediawiki.org/wiki/Extension:EnhanceContactForm',
	'descriptionmsg' => 'enhancecontactform-desc',
);

$wgHooks['ContactForm'][] = 'enhanceContactForm';

/**
 * Add extra info to the e-mail which gets sent to the staff.
 * @return Boolean: true
 */
function enhanceContactForm( &$to, &$replyto, &$subject, &$text ) {
	global $wgDBname, $wgRequest, $wgServer;

	$text = 'Contact message by the user: ' . $wgRequest->getText( 'wpText' ) . "\n\n\n\n\n\n\n";
	// Now add the custom stuff
	$text .= 'URL of the wiki: ' . $wgServer . "\n";
	$text .= 'Database name: ' . $wgDBname . "\n";
	$text .= 'IP address of the reporter: ' . $wgRequest->getIP() . "\n";

	if ( class_exists( 'MyInfo' ) ) {
		$myinfo = new MyInfo();
		$myinfo->browser = get_browser( null, true );
		$myinfo->info = browser_detection( 'full' );
		$myinfo->info[] = browser_detection( 'moz_version' );
		$text .= 'Browser: ' . $myinfo->getBrowser() . "\n";
		$text .= 'Operating system: ' . $myinfo->getOs() . "\n";
		$text .= 'Skin: ' . $myinfo->getSkin() . "\n";
		$text .= 'User-Agent string: ' . $myinfo->getUAgent() . "\n";
	}

	return true;
}