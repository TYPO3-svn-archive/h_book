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
 * A desired HBook Post domain object
 *
 * @package HBook
 * @subpackage tests
 * @version $ID:$
 */
class Tx_HBook_Domain_Fixture_DesiredPost extends Tx_HBook_Domain_Model_Post {

	public function __construct() {

		// @note: you can generate cool SPAM messages at http://blogoscoped.com/spam/
		$content = <<<SPAM
Hi Max!

Thank you for this great article. I really appreciate your crystal clear explanations.

But I have a problem. Please have look at

http://www.example.com/blog/my-project/test.html

In Line 20 you'll see that the execute() statement

<code>\$this->execute();</code>

will fail.

Could you explain why?

Thanks,

Stephen
SPAM;

		$this->setContent($content);
		$this->setAuthor('Stephen Doe');
		$this->setEmail('contact@stephen-doe.com');
	}
}
?>