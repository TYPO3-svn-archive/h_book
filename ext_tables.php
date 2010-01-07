<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages("tx_hbook_entries");

$TCA["tx_hbook_entries"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries',		
		'label' => 'author',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate DESC",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_hbook_entries.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, author, text, comment, ip, email, homepage, icq, aim, msn",
	)
);

$TCA["tx_hbook_ips"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:h_book/locallang_db.xml:tx_hbook_ips',		
		'label' => 'ip',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate DESC",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"endtime" => "endtime",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_hbook_ips.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, endtime, ip",
	)
);




$TCA['tt_content']['types']
['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']= 'layout,select_key,pages,recursive';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1',
 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');




/*if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txhbookM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}*/


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:h_book/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","HBook");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_hbook_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_hbook_pi1_wizicon.php';
?>