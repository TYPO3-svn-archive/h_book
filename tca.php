<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_hbook_entries"] = Array (
	"ctrl" => $TCA["tx_hbook_entries"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,author,text,comment,ip,email,homepage,location,icq,msn,aim"
	),
	"feInterface" => $TCA["tx_hbook_entries"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"author" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.author",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"text" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.text",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"comment" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.comment",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"ip" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.ip",		
			"config" => Array (
				"type" => "input",	
				"size" => "15",	
				"max" => "15",	
				"eval" => "required,trim",
			)
		),
		"email" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.email",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"homepage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.homepage",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"location" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.location",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"icq" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.icq",		
			"config" => Array (
				"type" => "input",	
				"size" => "9",
				"max" => "9"
			)
		),
		"msn" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.msn",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"aim" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_entries.aim",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, author, text, comment, ip, email, homepage, location, icq, msn, aim")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_hbook_ips"] = Array (
	"ctrl" => $TCA["tx_hbook_ips"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,endtime,ip"
	),
	"feInterface" => $TCA["tx_hbook_ips"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"ip" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:h_book/locallang_db.xml:tx_hbook_ips.ip",		
			"config" => Array (
				"type" => "input",	
				"size" => "15",	
				"max" => "15",	
				"eval" => "required,trim",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, ip")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "endtime")
	)
);
?>