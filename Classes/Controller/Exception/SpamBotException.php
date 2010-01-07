<?php

class Tx_HBook_Controller_Exception_SpamBotException extends Tx_Extbase_MVC_Exception {
	public function __construct() { parent::__construct("Based on an analysis of form inputs, this post was most likely made by an automatic bot."); }
}

?>
