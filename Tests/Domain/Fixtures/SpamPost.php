<?php

/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2010 Thomas Juhnke <tommy@van-tomas.de>                         *
 *      All rights reserved                                             *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */

include_once(t3lib_extMgm::extPath('h_book') .'/Classes/Domain/Model/Post.php');

/**
 * A Spam HBook Post domain object
 *
 * @package HBook
 * @subpackage tests
 * @version $ID:$
 */
class Tx_HBook_Domain_Fixture_SpamPost extends Tx_HBook_Domain_Model_Post {

	public function __construct() {

		// @note: you can generate cool SPAM messages at http://blogoscoped.com/spam/
		$content = <<<SPAM
DO YOU FEEL B0R3D0M?
---------------------------------------------------------------
N0 MORE! those times are over. GET INSTANT PLAYING TEXAS HOLDEM POKER ONLINE 100%!!!!
Did you ever think..... it doesnt w0rk!?
Now it does! Read on--Texas Holdem Poker2000(TM) this will help you GUARANTEED

My friends told me: "Boredom Boredom" to which I had one reply
PLAYING TEXAS HOLDEM POKER ONLINE 24 Hours
ONLY with our Texas Holdem Poker2000(TM)!!!
Dr. Texas Holdem Poker Player says: "I saw it work like magic." (Texas Holdem Poker Player is TRUSTED source
in the field.)

Try it for free NOW!
JUST $79... to good to be troo? I thouhgt s0 too  ....at first.
Try it now. CLICK HERE
www.buy-texas-holdem-poker2000.com
SPAM;

		$this->setContent($content);
		$this->setAuthor('Texas Holdem Poker');
		$this->setEmail('texas_holdem [url]http://www.royal-holdem-poker.com[/url]');
	}
}
?>