<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
 * Configure the Plugin to call the 
 * right combination of Controller and Action according to
 * the user input (default settings, FlexForm, URL etc.)
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,																		// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	'Pi1',																			// A unique name of the plugin in UpperCamelCase
	array('Post' => 'index,new,create'),
	array('Post' => 'new,create')
);

function user_long2ip($PA, $fObj) {
	return long2ip($PA['itemFormElValue']);
}

?>