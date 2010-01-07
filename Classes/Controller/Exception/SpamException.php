<?php

class Tx_HBook_Controller_Exception_SpamException extends Tx_Extbase_MVC_Exception {
	public function __construct() { parent::__construct("Based on a content analysis, this post most likely is spam."); }
}

?>