<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_hbook_domain_model_post'] = array(
	'ctrl' => $TCA['tx_hbook_domain_model_post']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden, approved, content, author, email, email_visible, ip_address'
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type' => 'check'
			)
		),
		'approved' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post.approved',
			'config'  => array(
				'type' => 'check'
			)
		),
		'content' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post.content',
			'config'  => array(
				'type' => 'text',
				'eval' => 'required',
				'rows' => 30,
				'cols' => 80,
			)
		),
		'author' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post.author',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 256
			)
		),
		'email' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post.email',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 256
			)
		),
		'email_visible' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post.email_visible',
			'config'  => array(
				'type' => 'check'
			)
		),
		'ip_address' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_post.ip_address',
			'config'  => array(
				'type' => 'user',
				'userFunc' => 'user_long2ip'
			)
		),
	),
	'types' => array(
		'1' => array('showitem' => 'hidden, approved, content, author, email, email_visible, ip_address')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);

$TCA['tx_hbook_domain_model_smilie'] = array(
	'ctrl' => $TCA['tx_hbook_domain_model_smilie']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden, code, image'
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type' => 'check'
			)
		),
		'code' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_smilie.code',
			'config'  => array(
				'type' => 'input',
				'size' => 10,
				'eval' => 'trim,required',
				'max'  => 10
			)
		),
		'image' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:h_book/Resources/Private/Language/locallang_db.xml:tx_hbook_domain_model_smilie.image',
			'config'  => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size'      => 3000,
				'uploadfolder'  => 'uploads/tx_hbook/smilies',
				'show_thumbs'   => 1,
				'size'          => 1,
				'maxitems'      => 1,
				'minitems'      => 0
			)
		),
	),
	'types' => array(
		'1' => array('showitem' => 'hidden, code, image')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);

?>