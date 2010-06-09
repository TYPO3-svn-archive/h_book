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

require_once(t3lib_extMgm::extPath('phpunit') . 'class.tx_phpunit_testcase.php');

include_once(dirname(__FILE__) . '/Fixtures/SpamPost.php');
include_once(dirname(__FILE__) . '/Fixtures/DesiredPost.php');

/**
 * Testcase for the Post domain
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 * @version 2010-06-02
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 *
 */
class Tx_HBook_Domain_PostTest extends Tx_Extbase_BaseTestCase {

	/**
	 *
	 * @var integer defines the threshold from which the post is classified as Spam
	 */
	private $spamRatingThreshold = 10;

	/**
	 * @test
	 * @author Thomas Juhnke <tommy@van-tomas.de>
	 */
	public function spamRatingForSpamPostIsGreaterThan10() {
		$post = new Tx_HBook_Domain_Fixture_SpamPost();
		$spam_rating = $post->getSpamRating();

		$this->assertGreaterThan($this->spamRatingThreshold, $spam_rating, 'Spam rating asserts to be greater than 10 but is '. $spam_rating);
	}

	/**
	 * @test
	 * @author Thomas Juhnek <tommy@van-tomas.de>
	 */
	public function spamRatingForDesiredPostIsLessThan10() {
		$post = new Tx_HBook_Domain_Fixture_DesiredPost();
		$spam_rating = $post->getSpamRating();

		$this->assertLessThan($this->spamRatingThreshold, $spam_rating, 'Spam rating asserts to be less than 10 but is '. $spam_rating);
	}
}
?>