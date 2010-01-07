<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Martin Helmich <typo3@martin-helmich.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'HBook' for the 'hbook' extension.
 *
 * @author	   Martin Helmich <typo3@martin-helmich.de>
 * @version    04-09-2007
 * @package    TYPO3
 * @subpackage h_book
 */
class tx_hbook_pi1 extends tslib_pibase {
	var $prefixId = 'tx_hbook_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_hbook_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'h_book';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content The PlugIn content
	 * @param	array		$conf    The PlugIn configuration
	 * @return	                     The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		
		$this->init();

		$this->create_clearSpamcode();

		foreach($this->codes as $theCode) {
			$theCode = strtoupper($theCode);
			switch($theCode) {
				case 'DISPLAY': $content = $this->display($content); break;
				case 'NEW':		$content = $this->create($content); break;
				case 'EDIT':	$content = $this->edit($content); break;
			}
		}

		return $this->pi_wrapInBaseClass($content);
	}
	
	/***************************************************************************
	 * DISPLAY FUNCTIONS
	 ***************************************************************************/
	
	/**
	 * Lists all guestbook entries.
	 * This function displays a list of all guestbook entries. Also displays page navigation specified by TypoScript.
	 * @param  string $content The plugin content
	 * @return string          The plugin content
	 */
	function display($content) {
		$template = $this->cObj->fileResource($this->conf['templateFile']);
		$template = $this->cObj->getSubpart($template, "###ENTRIES###");
		$entryTemplate = $this->cObj->getSubpart($template, "###ENTRY###");
		
		$enabledFields = t3lib_div::trimExplode(',',$this->conf['enableAdditionalFields']);
		
		$selectConf = $this->conf['display.']['select.'];
		$selectConf['pidInList'] = $this->hbook_getPid();
		
		$page = $this->piVars['page']?intval($this->piVars['page']):1;
		if($page < 1) $page = 1;
		
		if($page && $selectConf['max'])
			$selectConf['begin'] = $selectConf['max'] * (intval($page) - 1);
		
		$res = $this->cObj->exec_getQuery('tx_hbook_entries',$selectConf);
		
		unset($selectConf['max']);
		unset($selectConf['begin']);
		$numResults = $GLOBALS['TYPO3_DB']->sql_num_rows( $this->cObj->exec_getQuery('tx_hbook_entries',$selectConf) );
		
		if($numResults > $this->conf['display.']['select.']['max']) {
			if($this->conf['display.']['select.']['max'])
				$pageCount = ceil($numResults / $this->conf['display.']['select.']['max']);
			else $pageCount = 1;
			
			$prevPage  = ($page > 1)?$this->pi_getPageLink($GLOBALS['TSFE']->id,'', array($this->prefixId=>array("page"=>$page-1))):'';
			$nextPage  = ($page < $pageCount)?$this->pi_getPageLink($GLOBALS['TSFE']->id,'', array($this->prefixId=>array("page"=>$page+1))):'';
			$firstPage = ($page > 1)?$this->pi_getPageLink($GLOBALS['TSFE']->id,'', array($this->prefixId=>array("page"=>1))):'';
			$lastPage  = ($page < $pageCount)?$this->pi_getPageLink($GLOBALS['TSFE']->id,'', array($this->prefixId=>array("page"=>$pageCount))):'';
			
			$pageLink_conf = $this->conf['display.']['pagebrowser.']['item.'];
			$pageLink_conf['typolink.']['parameter'] = $firstPage;
			$pagebrowser = $this->cObj->stdWrap($this->conf['display.']['pagebrowser.']['item.']['first'], $pageLink_conf);
			
			$pageLink_conf = $this->conf['display.']['pagebrowser.']['item.'];
			$pageLink_conf['typolink.']['parameter'] = $prevPage;
			$pagebrowser .= $this->cObj->stdWrap($this->conf['display.']['pagebrowser.']['item.']['prev'], $pageLink_conf);
			
			$iStart = 1;
			$iStop = $pageCount;
			
			if($this->conf['display.']['pagebrowser.']['item.']['maxTotalItem'] > 0 && $this->conf['display.']['pagebrowser.']['item.']['maxTotalItem'] < $pageCount) {
				$offset = $this->conf['display.']['pagebrowser.']['item.']['maxTotalItem'];
				$iStart = $page - $offset; if ($iStart < 1) $iStart = 1;
				$iStop  = $page + $offset; if ($iStop > $pageCount) $iStop = $pageCount;
			}
			
			for($i=$iStart; $i <= $iStop; $i ++) {
				$pageLink = "";
			
				if($this->piVars['page'] != $i) {
					$pageLink_params[$this->prefixId]['page'] = $i;
					$pageLink = $this->pi_getPageLink($GLOBALS['TSFE']->id,'', $pageLink_params);
				}
				
				$pageLink_conf = $this->conf['display.']['pagebrowser.']['item.'];
				$pageLink_conf['typolink.']['parameter'] = $pageLink;
				$pageLinkString = $this->cObj->stdWrap($i, $pageLink_conf);
				
				$pagebrowser .= $pageLinkString;
			}
			
			$pageLink_conf = $this->conf['display.']['pagebrowser.']['item.'];
			$pageLink_conf['typolink.']['parameter'] = $nextPage;
			$pagebrowser .= $this->cObj->stdWrap($this->conf['display.']['pagebrowser.']['item.']['next'], $pageLink_conf);
			
			$pageLink_conf = $this->conf['display.']['pagebrowser.']['item.'];
			$pageLink_conf['typolink.']['parameter'] = $lastPage;
			$pagebrowser .= $this->cObj->stdWrap($this->conf['display.']['pagebrowser.']['item.']['last'], $pageLink_conf);
			
			$pagebrowser = $this->cObj->stdWrap($pagebrowser, $this->conf['display.']['pagebrowser.']);
		}
		
		while($arr = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$tmpData = $this->cObj->data;
			$this->cObj->data = $arr;
		
			$emailConf = $this->conf['display.']['labels.']['email.'];
			$hpConf    = $this->conf['display.']['labels.']['homepage.'];
			$icqConf   = $this->conf['display.']['labels.']['icq.'];
			$msnConf   = $this->conf['display.']['labels.']['msn.'];
			$aimConf   = $this->conf['display.']['labels.']['aim.'];
			$editConf  = $this->conf['display.']['labels.']['edit.'];
			$locConf   = $this->conf['display.']['location.'];
			
			if(!in_array('email',$enabledFields)) $emailConf['if.']['directReturn'] = "0";
			if(!in_array('homepage',$enabledFields)) $hpConf['if.']['directReturn'] = "0";
			if(!in_array('location',$enabledFields)) $locConf['if.']['directReturn'] = "0";
			if(!in_array('icq',$enabledFields))	$icqConf['if.']['directReturn'] = "0";
			if(!in_array('msn',$enabledFields))	$msnConf['if.']['directReturn'] = "0";
			if(!in_array('aim',$enabledFields)) $aimConf['if.']['directReturn'] = "0";
		
			$marker = Array(
				"###LABEL_WRITTENON###"		=> $this->pi_getLL('tx_hbook_pi1.display.writtenon'),
				"###LABEL_BY###"			=> $this->pi_getLL('tx_hbook_pi1.display.by'),
				
				"###DATE###"				=> $this->cObj->stdWrap($arr['crdate'], $this->conf['display.']['date.']),
				"###AUTHOR###"				=> $this->cObj->stdWrap($arr['author'], $this->conf['display.']['author.']),
				"###CONTENT###"				=> $this->hbook_parseText('text', $arr['text']),
				"###COMMENT###"				=> $this->hbook_parseText('comment', $arr['comment']),
				"###EMAIL###"				=> $this->cObj->cObjGetSingle($this->conf['display.']['labels.']['email'], $emailConf),
				"###HOMEPAGE###"			=> $this->cObj->cObjGetSingle($this->conf['display.']['labels.']['homepage'], $hpConf),
				"###LOCATION###"			=> $this->cObj->cObjGetSingle($this->conf['display.']['location'], $locConf),
				"###ICQ###"					=> $this->cObj->cObjGetSingle($this->conf['display.']['labels.']['icq'], $icqConf),
				"###AIM###"					=> $this->cObj->cObjGetSingle($this->conf['display.']['labels.']['aim'], $aimConf),
				"###MSN###"					=> $this->cObj->cObjGetSingle($this->conf['display.']['labels.']['msn'], $msnConf),
				"###EDIT###"				=> $this->cObj->cObjGetSingle($this->conf['display.']['labels.']['edit'], $editConf)
			);
			$entry = $this->cObj->substituteMarkerArrayCached($entryTemplate, $marker);
			
			if(! $arr['comment']) $entry = $this->cObj->substituteSubpart($entry, "###COMMENTAREA###", "");
			
			$entries .= $entry;
			
			$this->cObj->data = $tmpData;
		}
		
		$template = $this->cObj->substituteSubpart($template, "###ENTRY###", $entries);
		
		$marker = Array(
			"###PAGEBROWSER###"		=> $pagebrowser
		);
		$template = $this->cObj->substituteMarkerArray($template, $marker);
		
		$content .= $template;
	
		return $content;
	}
	
	
	
	
	/***************************************************************************
	 * ENTRY CREATION FUNCTIONS
	 ***************************************************************************/
	 
	 
	 
	/**
	 * Outputs the form for creating a new guestbook entry.
	 * @param  string $content The plugin content
	 * @return string          The plugin content
	 */
	function create($content) {
		if($this->piVars['spamcode']) $this->create_displaySpamcode($this->piVars['spamcode']);
	
		$ipblocked = $this->conf['create.']['ipblocking']?$this->hbook_getIPBlocked():FALSE;
	
		$template = $this->cObj->fileResource($this->conf['templateFile']);
		
		if($ipblocked) {
			$template = $this->cObj->getSubpart($template, "###IPBLOCKED###");
			
			$marker = Array(
				"###LABEL_NEWENTRY###"		=> $this->pi_getLL('tx_hbook_pi1.create'),
				"###LABEL_IPBLOCKED###"		=> $this->pi_getLL('tx_hbook_pi1.create.ipblocked')
			);
			
			return $content .= $this->cObj->substituteMarkerArrayCached($template, $marker);
		}
		
		if( $this->piVars['create'] ) $content = $this->create_save($content);
		
		$template = $this->cObj->getSubpart($template, "###NEW###");
		
		$jspath = t3lib_extMgm::siteRelPath('h_book').'pi1/h_book.javascript.js';
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey].='<script language="javascript" type="text/javascript" src="'.$jspath.'"></script>';
		
		if($this->conf['create.']['spamCode'] == 1) {
			$code = $this->create_getRandomSpamcode($this->conf['create.']['spamCode.']['length']);
			$insertArray = array(
				'pid'		=> $this->conf['storagePID'],
				'hash'		=> $code,
				'tstamp'	=> time()
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_hbook_checkhash',$insertArray);
			
			$spamcodeUid = mysql_insert_id();
			/*$linkParams[$this->prefixId] = array(
				'spamcode'		=> $spamcodeUid
			);
			$imgCode = '<img src="'.$this->pi_getPageLink($GLOBALS['TSFE']->id,'',$linkParams).'" alt="" />';*/
			
			$tmpData = $this->cObj->data;
			$this->cObj->data = $insertArray;
			$imgCode = $this->cObj->cObjGetSingle($this->conf['create.']['captcha'],$this->conf['create.']['captcha.']);
			
			$this->cObj->data = $tmpData;
		}
		else {
			$template = $this->cObj->substituteSubpart($template,"###SPAMCODE###","");
		}
		
		$enabledFields = t3lib_div::trimExplode(',',$this->conf['enableAdditionalFields']);
		
		if(!in_array('email',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_EMAIL###","");
		if(!in_array('homepage',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_HOMEPAGE###","");
		if(!in_array('location',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_LOCATION###","");
		if(!in_array('icq',$enabledFields))	$template = $this->cObj->substituteSubpart($template,"###SP_ICQ###","");
		if(!in_array('msn',$enabledFields))	$template = $this->cObj->substituteSubpart($template,"###SP_MSN###","");
		if(!in_array('aim',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_AIM###","");
		
		$required = t3lib_div::trimExplode(',', $this->conf['create.']['required']);
		$marker = Array(
			"###LABEL_NAME###"		=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.name") , $this->conf['create.'][in_array('name',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_EMAIL###"		=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.email") , $this->conf['create.'][in_array('email',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_HOMEPAGE###"	=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.homepage") , $this->conf['create.'][in_array('homepage',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_LOCATION###"	=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.location") , $this->conf['create.'][in_array('location',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_ICQ###"		=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.icq") , $this->conf['create.'][in_array('icq',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_MSN###"		=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.msn") , $this->conf['create.'][in_array('msn',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_AIM###"		=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.aim") , $this->conf['create.'][in_array('aim',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_TEXT###"		=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.text") , $this->conf['create.'][in_array('text',$required)?'requiredLabel.':'label.'] ),
			"###LABEL_SPAMCODE###"	=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.spamcode") , $this->conf['create.']['label.'] ),
			"###LABEL_SPAMCODEDESC###" => $this->pi_getLL('tx_hbook_pi1.create.spamcode.desc'),
			"###LABEL_SUBMIT###"	=> $this->cObj->stdWrap( $this->pi_getLL("tx_hbook_pi1.create.submit") , $this->conf['create.']['submit.'] ),
			"###LABEL_NEW###"		=> $this->pi_getLL("tx_hbook_pi1.create"),
		
			"###NAME###"			=> $this->piVars['create']['author'],
			"###EMAIL###"			=> $this->piVars['create']['email'],
			"###HOMEPAGE###"		=> $this->piVars['create']['homepage'],
			"###LOCATION###"		=> $this->piVars['create']['location'],
			"###ICQ###"				=> $this->piVars['create']['icq'],
			"###MSN###"				=> $this->piVars['create']['msn'],
			"###AIM###"				=> $this->piVars['create']['aim'],
			"###TEXT###"			=> $this->piVars['create']['text'],
		
			"###PATH###"			=> t3lib_extMgm::siteRelPath('h_book'),
			"###SMILIES###"			=> $this->create_smileyList(),
			"###ACTION###"			=> $this->conf['create.']['extScript']?$this->conf['create.']['extScript']:'',
			"###SIZE###"			=> $this->conf['create.']['inputField.']['width'],
			"###INPUT_STYLE###"		=> $this->conf['create.']['inputField.']['param'],
			"###SPAMCODE_LENGTH###"	=> $this->conf['create.']['spamCode.']['length'],
			"###IMG_SPAMCODE###"	=> $imgCode,
			"###SPAMCODE_UID###"	=> $spamcodeUid
		);
		$content .= $this->cObj->substituteMarkerArrayCached($template, $marker);
		
		return $content;
	}
	
	
	/**
	 * Saves an entry to be created to database.
	 * @param  string $content The plugin content
	 * @return string          The plugin content
	 */
	function create_save($content) {
		$ipblocked = $this->conf['create.']['ipblocking']?$this->hbook_getIPBlocked():FALSE;
		if($ipblocked) return $content;
		
		$required = t3lib_div::trimExplode(',', $this->conf['create.']['required']);
		$error = Array();
		
		$enabledFields = t3lib_div::trimExplode(',',$this->conf['enableAdditionalFields']);
		
		if(in_array('name', $required) && empty($this->piVars['create']['author'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noName');
		if(in_array('email', $required) && !in_array('email',$enabledFields) && empty($this->piVars['create']['email'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noEmail');
		if(in_array('text', $required) && empty($this->piVars['create']['text'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noText');
		if(in_array('homepage', $required) && !in_array('homepage',$enabledFields) && empty($this->piVars['create']['homepage'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noHomepage');
		if(in_array('location', $required) && !in_array('location',$enabledFields) && empty($this->piVars['create']['location'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noLocation');
		if(in_array('icq', $required) && !in_array('icq',$enabledFields) && empty($this->piVars['create']['icq'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noICQ');
		if(in_array('msn', $required) && !in_array('msn',$enabledFields) && empty($this->piVars['create']['msn'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noMSN');
		if(in_array('aim', $required) && !in_array('aim',$enabledFields) && empty($this->piVars['create']['aim'])) $error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noAIM');
		
		$spam = 0;
		if($this->conf['create.']['filter']) {
			$spamWords = t3lib_div::trimExplode(',', $this->conf['create.']['filter.']['words']);
			
			foreach($spamWords as $spamWord) {
				if(preg_match("/$spamWord/i", $this->piVars['create']['text'])) {
					$spam ++;
				}
			}
		}
		
		if($spam > 0) {
			$error[] = $this->pi_getLL('tx_hbook_pi1.create.error.noSpam.block');
		}
		
		if($this->conf['create.']['spamCode'] == 1) {
			$this->piVars['create']['spamcode_uid'] = intval($this->piVars['create']['spamcode_uid']);
		
			$spamcode_subm = $this->piVars['create']['spamcode'];
			$spamcode_ref  = $this->create_getSpamCode($this->piVars['create']['spamcode_uid']);
			
			if($spamcode_subm != $spamcode_ref) {
				$error[] = $this->pi_getLL('tx_hbook_pi1.create.error.wrongSpamcode');
			}
			else {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_hbook_checkhash','uid="'.intval($this->piVars['create']['spamcode_uid']).'"');
			}
		}
		
		if(count($error) > 0) {
			foreach($error as $sError) {
				$errorString .= $this->cObj->stdWrap($sError, $this->conf['create.']['errorList.']['item.']);
			}
			$errorString = $this->cObj->stdWrap($errorString, $this->conf['create.']['errorList.'] );
			
			$template = $this->cObj->fileResource($this->conf['templateFile']);
			$template = $this->cObj->getSubpart($template, "###WINDOW###");
			
			$marker = Array(
				"###TITLE###"		=> $this->pi_getLL('tx_hbook_pi1.create.error'),
				"###MESSAGE###"		=> $errorString
			);
			
			return $content.$this->cObj->substituteMarkerArrayCached($template, $marker);
		}
		
		$insert = Array(
			'pid'		=> $this->hbook_getPid(),
			'tstamp'	=> time(),
			'crdate'	=> time(),
			'author'	=> $this->piVars['create']['author'],
			'text'		=> $this->piVars['create']['text'],
			'ip'		=> $_SERVER['REMOTE_ADDR'],
			'email'		=> in_array('email',$enabledFields)?$this->piVars['create']['email']:'',
			'homepage'	=> in_array('homepage',$enabledFields)?$this->piVars['create']['homepage']:'',
			'location'	=> in_array('location',$enabledFields)?$this->piVars['create']['location']:'',
			'icq'		=> in_array('icq',$enabledFields)?$this->piVars['create']['icq']:'',
			'msn'		=> in_array('msn',$enabledFields)?$this->piVars['create']['msn']:'',
			'aim'		=> in_array('aim',$enabledFields)?$this->piVars['create']['aim']:'',
		);
		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_hbook_entries',
			$insert
		);
		
		if($this->conf['create.']['redirect']) {
			$newLink = $this->pi_getPageLink( $this->conf['create.']['redirect'] );
			header("Location: $newLink"); die();
		}
		
		return $content;
	}
	
	/**
	 * Creates a spam protection code of variable length.
	 * This function generates a spam protection code the visitor creating an
	 * guestbook entry has to copy manually from an image to verify him/her being
	 * human and not a spam bot.
	 * @param  int    $length The desired spam code length
	 * @return string         The spamcode randomly composed of letters and numbers
	 */
	function create_getRandomSpamcode($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$result = "";
		
		for($i=0; $i < $length; $i ++) {
			$result .= $chars{rand(0,strlen($chars)-1)};
		}
		
		return $result;
	}
	
	/**
	 * Loads a spam protection code from database by UID.
	 * @param  int    $uid The spam protection code UID
	 * @return string      The spam protection code
	 */
	function create_getSpamCode($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'hash',
			'tx_hbook_checkhash',
			'uid="'.intval($uid).'"'
		);
		list($hash) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		return $hash;
	}
	
	/**
	 * Outputs a spam protection code as JPEG.
	 * @param  int  $uid The spam protection code UID
	 * @return void
	 */
	function create_displaySpamcode($uid) {
		$hash = $this->create_getSpamCode($uid);
	
		header('Content-Type: image/jpeg');
		
		$img = imagecreate(100,14);
		imagefill($img,0,0,imagecolorallocate($img,$this->conf['create.']['spamCode.']['background.']['r'],$this->conf['create.']['spamCode.']['background.']['g'],$this->conf['create.']['spamCode.']['background.']['b']));
		imagestring($img,2,0,0,$hash,imagecolorallocate($img,$this->conf['create.']['spamCode.']['text.']['r'],$this->conf['create.']['spamCode.']['text.']['g'],$this->conf['create.']['spamCode.']['text.']['b']));
		
		imagegif($img);
		die();
	}
	
	/**
	 * Deletes all spam protection codes that are older than one hour from database.
	 * @return void
	 */
	function create_clearSpamcode() {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'tx_hbook_checkhash',
			'tstamp < UNIX_TIMESTAMP() - 3600'
		);
	}
	
	/**
	 * Generates the smilie list for the entry generation form.
	 * @return string The smilie list HTML code
	 */
	function create_smileyList() {
		$smilies = $this->hbook_getSmilies();
		
		foreach($smilies as $tag=>$smilie) {
			$content .= '<a href="javascript:void(0);" onclick="insert(\'newentry_text\',\''.$tag.'\');">'.$smilie.'</a> ';
		}
		
		return $content;
	}
	
	
	
	/***************************************************************************
	 * ENTRY CREATION FUNCTIONS
	 ***************************************************************************/
	 
	 
	 
	/**
	 * Outputs the entry editing form.
	 * @param  string $content The plugin content
	 * @return string          The plugin content
	 */
	function edit($content) {
		if(! $this->piVars['edit']['uid'] ) return $content;
	
		$selectConf = Array(
			"pidInList"		=> $this->hbook_getPid(),
			"uidInList"		=> $this->piVars['edit']['uid'],
			"selectFields"	=> '*'
		);
		$res = $this->cObj->exec_getQuery('tx_hbook_entries',$selectConf);
		
		if($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 0) return $content;
		
		if($this->piVars['edit']['ipblock']) $this->edit_execIpBlock($this->piVars['edit']['ipblock'], TRUE);
		if($this->piVars['edit']['ipunblock']) $this->edit_execIpBlock($this->piVars['edit']['ipunblock'], FALSE);
		
		if($this->piVars['edit']['save']) {
			$content = $this->edit_save($content);
			$res = $this->cObj->exec_getQuery('tx_hbook_entries',$selectConf);
		}
		
		$template = $this->cObj->fileResource($this->conf['templateFile']);
		$template = $this->cObj->getSubpart($template, "###EDIT###");
		
		$enabledFields = t3lib_div::trimExplode(',',$this->conf['enableAdditionalFields']);
		
		if(!in_array('email',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_EMAIL###","");
		if(!in_array('homepage',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_HOMEPAGE###","");
		if(!in_array('location',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_LOCATION###","");
		if(!in_array('icq',$enabledFields))	$template = $this->cObj->substituteSubpart($template,"###SP_ICQ###","");
		if(!in_array('msn',$enabledFields))	$template = $this->cObj->substituteSubpart($template,"###SP_MSN###","");
		if(!in_array('aim',$enabledFields)) $template = $this->cObj->substituteSubpart($template,"###SP_AIM###","");
		
		$arr = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		$marker = Array(
			"###ACTION###"			=> '',
			"###NAME###"			=> $arr['author'],
			"###EMAIL###"			=> $arr['email'],
			"###HOMEPAGE###"		=> $arr['homepage'],
			"###LOCATION###"		=> $arr['location'],
			"###ICQ###"				=> $arr['icq'],
			"###MSN###"				=> $arr['msn'],
			"###AIM###"				=> $arr['aim'],
			"###TEXT###"			=> $arr['text'],
			"###COMMENT###"			=> $arr['comment'],
			"###IPADDRESS###"		=> $arr['ip'],
			"###IP_BLOCK###"		=> $this->edit_ipBlock($arr),
			"###UID###"				=> $arr['uid'],
			
			"###LABEL_EDIT###"		=> $this->pi_getLL('tx_hbook_pi1.edit'),
			"###LABEL_NAME###"		=> $this->pi_getLL('tx_hbook_pi1.edit.name'),
			"###LABEL_EMAIL###"		=> $this->pi_getLL('tx_hbook_pi1.edit.email'),
			"###LABEL_HOMEPAGE###"	=> $this->pi_getLL('tx_hbook_pi1.edit.homepage'),
			"###LABEL_LOCATION###"	=> $this->pi_getLL("tx_hbook_pi1.edit.location"),
			"###LABEL_ICQ###"		=> $this->pi_getLL("tx_hbook_pi1.edit.icq"),
			"###LABEL_MSN###"		=> $this->pi_getLL("tx_hbook_pi1.edit.msn"),
			"###LABEL_AIM###"		=> $this->pi_getLL("tx_hbook_pi1.edit.aim"),
			"###LABEL_TEXT###"		=> $this->pi_getLL('tx_hbook_pi1.edit.text'),
			"###LABEL_COMMENT###"	=> $this->pi_getLL('tx_hbook_pi1.edit.comment'),
			"###LABEL_DELETE###"	=> $this->pi_getLL('tx_hbook_pi1.edit.delete'),
			"###LABEL_SAVE###"		=> $this->pi_getLL('tx_hbook_pi1.edit.save'),
			"###LABEL_IPADDRESS###"	=> $this->pi_getLL('tx_hbook_pi1.edit.ip'),
		
			"###PATH###"			=> t3lib_extMgm::siteRelPath('h_book'),
			"###SMILIES###"			=> $this->create_smileyList(),
			"###ACTION###"			=> $this->conf['create.']['extScript']?$this->conf['create.']['extScript']:'',
			"###SIZE###"			=> $this->conf['create.']['inputField.']['width'],
			"###INPUT_STYLE###"		=> $this->conf['create.']['inputField.']['param']
		);
	
		return $content.$this->cObj->substituteMarkerArrayCached($template, $marker);
	}
	
	/**
	 * Saves changes to an entry to database.
	 * @param  string $content The plugin content
	 * @return string          The plugin content
	 */
	function edit_save($content) {
		$enabledFields = t3lib_div::trimExplode(',',$this->conf['enableAdditionalFields']);
	
		$arr = Array(
			'author'		=> $this->piVars['edit']['author'],
			'text'			=> $this->piVars['edit']['text'],
			'comment'		=> $this->piVars['edit']['comment'],
			'email'			=> in_array('email',$enabledFields)?$this->piVars['edit']['email']:'',
			'homepage'		=> in_array('homepage',$enabledFields)?$this->piVars['edit']['homepage']:'',
			'location'		=> in_array('location',$enabledFields)?$this->piVars['edit']['location']:'',
			'icq'			=> in_array('icq',$enabledFields)?$this->piVars['edit']['icq']:'',
			'aim'			=> in_array('aim',$enabledFields)?$this->piVars['edit']['aim']:'',
			'msn'			=> in_array('msn',$enabledFields)?$this->piVars['edit']['msn']:'',
			'deleted'		=> $this->piVars['edit']['deleted'],
		);
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_hbook_entries',
			'uid="'.intval($this->piVars['edit']['uid']).'"',
			$arr
		);
	
		return $content;
	}
	
	/**
	 * Gibt f�r das Bearbeitungsformular den Link aus, um die IP-Adresse zu (ent)sperren.
	 * Generates the editing form link to (un)lock the author's IP address.
	 * @param  array  $arr The record entry
	 * @return string      The link HTML code
	 */
	function edit_ipBlock($arr) {
		if($this->hbook_getEntryIPBlocked($arr['ip'])) {
			$linkParams[$this->prefixId]['edit']['uid'] = $arr['uid'];
			$linkParams[$this->prefixId]['edit']['ipunblock'] = $arr['ip'];
			$link = $this->pi_getPageLink( $GLOBALS['TSFE']->id, '', $linkParams);
			
			$conf = $this->conf['edit.']['ip.']['block.'];
			$conf['typolink.']['parameter'] = $link;
			
			return $this->cObj->stdWrap($this->pi_getLL('tx_hbook_pi1.edit.ip.blocked'),$conf);
		}
		else {
			$linkParams[$this->prefixId]['edit']['uid'] = $arr['uid'];
			$linkParams[$this->prefixId]['edit']['ipblock'] = $arr['ip'];
			$link = $this->pi_getPageLink( $GLOBALS['TSFE']->id, '', $linkParams);
			
			$conf = $this->conf['edit.']['ip.']['block.'];
			$conf['typolink.']['parameter'] = $link;
			
			return $this->cObj->stdWrap($this->pi_getLL('tx_hbook_pi1.edit.ip.block'),$conf);
		}
	}
	
	/**
	 * (Un)Locks an IP address.
	 * @param  string  $ip    The IP address to be (un)locked
	 * @param  boolean $block TRUE, if the IP address is to be unlocked, otherwise FALSE
	 * @return void
	 */
	function edit_execIpBlock($ip, $block) {
		if($block) {
			$arr = Array(
				"pid"			=> $this->hbook_getPid(),
				"tstamp"		=> time(),
				"crdate"		=> time(),
				"endtime"		=> time() + intval($this->conf['ipBlockDuration']),
				"ip"			=> $ip
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_hbook_ips',
				$arr
			);
		}
		else {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_hbook_ips',
				'ip="'.$ip.'" AND deleted="0" AND pid="'.$this->hbook_getPid().'"'
			);
		}
	}
	
	/***************************************************************************
	 * MISCELLANEOUS FUNCTIONS
	 ***************************************************************************/
	
	/**
	 * Generates the HTML substitute for a single [IMG] tag.
	 * This function is designed as a callback function.
	 * @param  array  $hits The regular expression matches
	 * @return string       The HTML image code
	 */
	function hbook_substImg($hits) {
		$imgCode = $this->conf["display."]["text."]["image."];
		$imgCode["file"] = $hits[1];
		$imgCode["longdescURL"] = $hits[1];
		
		return $this->cObj->IMAGE($imgCode);
	}
	
	/**
	 * Parses the BBCodes in an entry text.
	 * @param  string $mode Defines the parsing mode of the text. May either be 'text' or 'comment'.
	 * @param  string $text The text to be parsed
	 * @return string       The parsed text
	 */
	function hbook_parseText($mode, $text) {
		$text = $this->cObj->stdWrap($text, $this->conf['display.'][$mode.'.']);
	
		$GLOBALS['tx_hbook_pi1'] = $this;
	
		$linkWrap1 = $this->cObj->wrap('$1',$this->conf['display.']['text.']['parseFunc.']['makelinks.']['http.']['wrap']);
		$linkWrap2 = $this->cObj->wrap('$2',$this->conf['display.']['text.']['parseFunc.']['makelinks.']['http.']['wrap']);
	
		$text = preg_replace("/\[b\](.*?)\[\/b\]/i", "<strong>$1</strong>", $text);
		$text = preg_replace("/\[i\](.*?)\[\/i\]/i", "<em>$1</em>", $text);
		$text = preg_replace("/\[u\](.*?)\[\/u\]/i", "<u>$1</u>", $text);
		$text = preg_replace("/\[url\](.*?)\[\/url\]/i", "<a href=\"$1\">$linkWrap1</a>", $text);
		$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/i", "<a href=\"$1\">$linkWrap2</a>", $text);
		$text = preg_replace_callback(
			"/\[img](.*?)\[\/img\]/i",
			create_function('$hits','
	$imgCode = $GLOBALS["tx_hbook_pi1"]->conf["display."]["text."]["image."];
	return $GLOBALS["tx_hbook_pi1"]->cObj->stdWrap($hits[1],$imgCode);'),
			$text
		);
		$smilies = $this->hbook_getSmilies();
		$text = str_replace(array_keys($smilies), array_values($smilies), $text);
		
		return $text;
	}
	
	/**
	 * Reads all smilies from TypoScripts and returns them as associative array by the pattern [smiley code]=>[substitution HTML code]
	 * @return array  All smilies as associative array
	 */
	function hbook_getSmilies() {
		$result = Array();
		$smileyArr = $this->conf['smilies.']['data.'];
		
		foreach($smileyArr as $key=>$smiley) {
			if(!is_array($smiley)) {
				$smileyData = $smileyArr[$key.'.'];
				$code = $this->cObj->cObjGetSingle($smiley, $smileyData);
				$tag = $smileyData['tag'];
				$result[$tag] = $code;
			}
		}
		
		return $result;
	}
	
	/**
	 * Determines if the current user's IP address is currently blocked by the admin.
	 * @return boolean  TRUE, if the IP address is blocked, otherwise FALSE.
	 */
	function hbook_getIPBlocked() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_hbook_ips',
			'pid="'.$this->hbook_getPid().'" AND hidden="0" AND deleted="0" AND ip="'.$_SERVER['REMOTE_ADDR'].'" AND endtime > UNIX_TIMESTAMP()'
		);
		return $GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0;
	}
	
	/**
	 * Determines if a specific IP address is crrently blocked by the admin.
	 * @param  string  $ipaddress The IP address to be checked
	 * @return boolean            TRUE, if the IP address is blocked, otherwise FALSE.
	 */
	function hbook_getEntryIPBlocked($ipaddress) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_hbook_ips',
			'pid="'.$this->hbook_getPid().'" AND hidden="0" AND deleted="0" AND ip="'.$ipaddress.'" AND endtime > UNIX_TIMESTAMP()'
		);
		return $GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0;
	}
	
	/**
	 * Returns the general entry storage page.
	 * @return int  The record storage page UID.
	 */
	function hbook_getPid() {
		if($this->conf['local'] == '0')
			if($this->conf['storagePID'])   return $this->conf['storagePID'];
		return $GLOBALS['TSFE']->id;
	}
	
	/**
	 * Returns the entry editing page UID.
	 * @return int The entry editing page UID
	 */
	function hbook_getEditPid() {
		if($this->conf['edit.']['page_uid']) return $this->conf['edit.']['page_uid'];
		return $GLOBALS['TSFE']->id;
	}
	
	/**
	 * Returns a standard MySQL-WHERE clause querying the storage page and deleted and hidden status.
	 * @return string A standard MySQL-WHERE clause.
	 */
	function hbook_getWhereClause() {
		return 'pid="'.$this->hbook_getPid().'" AND deleted="0" AND hidden="0"';
	}
	
	/**
	 * Initializes the configuration vars.
	 * @return void
	 */
	function init() {
		$this->codes = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display', 's_general');
		$this->codes = t3lib_div::trimExplode(',', $this->codes);
	
		// Anzuzeigende Einträge
			$limit = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'limit', 's_general'));
			$limit = (strlen($limit)>0)?$limit:intval($this->conf['display.']['select.']['max']);
			$this->conf['display.']['select.']['max'] = $limit?$limit:0;
			
		// Lokal
			$local = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'local', 's_general'));
			$local = (strlen($local)>0)?$local:intval($this->conf['local']);
			$this->conf['local'] = $local?$local:0;
			
		// StoragePID
			$sPID = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'storagePID', 's_general'));
			$sPID = (strlen($sPID)>0)?$sPID:intval($this->conf['storagePID']);
			$this->conf['storagePID'] = $sPID?$sPID:0;
			
		// create.redirect
			$redirect = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'redirect', 's_create'));
			$redirect = (strlen($redirect)>0)?$redirect:intval($this->conf['create.']['redirect']);
			$this->conf['create.']['redirect'] = $redirect?$redirect:FALSE;
			
		// create.required
			$required = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'required', 's_create');
			$required = (strlen($required)>0)?$required:$this->conf['create.']['required'];
			$this->conf['create.']['required'] = $required?$required:'';
			
		// create.filter
			$filter = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'filter', 's_create');
			$filter = (strlen($filter)>0)?$filter:intval($this->conf['create.']['filter']);
			$this->conf['create.']['filter'] = $filter?$filter:'1';
			
		// create.filter.words
			$filterWords = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'filterWords', 's_create');
			$filterWords = (strlen($filterWords)>0)?$filterWords:$this->conf['create.']['filter.']['words'];
			$this->conf['create.']['filter.']['words'] = $filterWords?$filterWords:'1';
		
		// display.select.orderBy
			$orderby = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'orderby', 's_display');
			$orderby = (strlen($orderby)>0)?$orderby:$this->conf['display.']['select.']['orderBy'];
			$this->conf['display.']['select.']['orderBy'] = $orderby?$orderby:'1';
			
		// editPid
			$editPid = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'editPid', 's_general'));
			if(strlen($editPid)>0) {
				$this->conf['display.']['labels.']['edit.']['typolink.']['parameter'] = $editPid;
				unset($this->conf['display.']['labels.']['edit.']['typolink.']['parameter.']['field']);
			}
			$editPid = (strlen($editPid)>0)?$editPid:intval($this->conf['edit.']['page_uid']);
			$this->conf['edit.']['page_uid'] = $editPid?$editPid:FALSE;
			
		// maxAge
			$maxAge = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maxAge', 's_display'));
			if($maxAge) {
				$maxAgeSec = $maxAge * 24 * 60 * 60;
				$checkDate = time() - $maxAgeSec;
				if($this->conf['display.']['select.']['where'] ) {
					$this->conf['display.']['select.']['where'] = '('.$this->conf['display.']['select.']['where'].') AND crdate > '.$checkDate;
				}
				else $this->conf['display.']['select.']['where'] = 'crdate > '.$checkDate;
			}
			
		// maxPageNum
			$maxPageNum = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maxPageNum', 's_display'));
			if(strlen($maxPageNum)>0) {
				$this->conf['display.']['pagebrowser.']['item.']['maxTotalItem'] = $maxPageNum;
			}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/h_book/pi1/class.tx_hbook_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/h_book/pi1/class.tx_hbook_pi1.php']);
}

?>
