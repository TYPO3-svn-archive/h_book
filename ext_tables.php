<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

/**
 * Registers a Plugin to be listed in the Backend. You also have to configure the Dispatcher in ext_localconf.php.
 */
Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY, // The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	'Pi1', // A unique name of the plugin in UpperCamelCase
	'A Guestbook' // A title shown in the backend dropdown field
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'HBook Guestbook');
t3lib_extMgm::addStaticFile($_EXTKEY, 'Resources/Public/Stylesheets', 'HBook Guestbook Default CSS');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_list.xml');

$TCA['tx_hbook_domain_model_post'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post',
		'label' 			=> 'author',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array( 'disabled' => 'hidden' ),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_hbook_domain_model_post.gif'
	)
);

$TCA['tx_hbook_domain_model_smilie'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_smilie',
		'label' 			=> 'code',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array( 'disabled' => 'hidden' ),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tca.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_hbook_domain_model_smilie.gif'
	)
);

?>
