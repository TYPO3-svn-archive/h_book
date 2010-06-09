<?php

/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2009 Martin Helmich <kontakt@martin-helmich.de>                 *
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



	/**
	 *
	 * A guestbook post.
	 * This class provides the domain model for the HBook guestbook post. It
	 * provides basic getter and setter function as well as a crude but
	 * effective spam detection mechanism.
	 *
	 * CLASS INFORMATION
	 *  @package    HBook
	 *  @subpackage Domain
	 *  @version    2010-01-05
	 *  @copyright  Martin Helmich <kontakt@martin-helmich.de>
	 *  @license    GNU Public License, version 2
	 *              http://opensource.org/licenses/gpl-license.php
	 *
	 * META ANNOTATIONS
	 *  @scope prototype
	 *  @entity
	 *
	 */

class Tx_HBook_Domain_Model_Post extends Tx_Extbase_DomainObject_AbstractEntity {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * @var string
		 * @validate Text, StringLength (minimum = 1)
		 */
	protected $content;

		/**
		 * @var string
		 * @validate Text, StringLength (minimum = 1)
		 */
	protected $author;

		/**
		 * @var string
		 * @validate Text, EmailAddress
		 */
	protected $email;

		/**
		 * @var boolean
		 */
	protected $emailVisible;

		/**
		 * @var int
		 */
	protected $ipAddress;

		/**
		 * @var boolean
		 */
	protected $approved;

		/**
		 * @var DateTime
		 */
	protected $crdate;





		/*
		 * CONSTRUCTORS
		 */





		 /**
		  *
		  * Constructs a new post.
		  * @return void
		  *
		  */

	public function __construct() {
		$this->crdate = new DateTime();
	}





		/*
		 * SETTERS
		 */





		/**
		 *
		 * Sets the content of the post.
		 * @param string $content The content of the post
		 * @return void
		 *
		 */

	public function setContent($content) { $this->content = trim($content); }



		/**
		 *
		 * Sets the author.
		 * @param string $author The author
		 * @return void
		 *
		 */

	public function setAuthor($author) { $this->author = trim($author); }



		/**
		 *
		 * Sets the email address.
		 * @param string $email The email address.
		 * @return void
		 *
		 */

	public function setEmail($email) { $this->email = $email; }



		/**
		 *
		 * Sets the email address to (in)visible.
		 * @param boolean $emailVisible TRUE to make the email address visible,
		 *                              FALSE to hide it.
		 * @return void
		 *
		 */

	public function setEmailVisible($emailVisible) { $this->emailVisible = $emailVisible ? TRUE : FALSE; }



		/**
		 * 
		 * The poster's IP address. The submitted address must be a string in
		 * dotted decimal notation, but the address will be saved as integer
		 * number.
		 *
		 * @param string $IPAddress The IP address as string in DDN.
		 * @return void
		 *
		 */

	public function setIPAddress($IPAddress) { $this->ipAddress = ip2long($IPAddress); }



		/**
		 *
		 * Sets the post to approved.
		 * @param boolean $approved TRUE, to approve to this post, FALSE to
		 *                          disapprove.
		 *
		 */

	public function setApproved($approved) { $this->approved = $approved; }





		/*
		 * GETTERS
		 */





		 /**
		  *
		  * Gets the content of this guestbook post.
		  * @return string
		  *
		  */

	public function getContent() { return $this->content; }



		/**
		 *
		 * Gets the author.
		 * @return string
		 *
		 */

	public function getAuthor() { return $this->author; }



		/**
		 *
		 * Gets the email address of the author.
		 * @return string
		 *
		 */

	public function getEmail() { return $this->email; }



		/**
		 *
		 * Gets the email address of the author. If the author did not agree to
		 * his address being displayed publicly, this methos returns an empty
		 * string.
		 * @return <type>
		 *
		 */
	
	public function getEmailIfVisible() { return $this->getEmailVisible() ? $this->getEmail() : ''; }



		/**
		 *
		 * Determines if the author's email address may be displayed or not.
		 * @return boolean
		 *
		 */

	public function getEmailVisible() { return $this->emailVisible ? true : false; }



		/**
		 *
		 * Determines if the post has already been approved by a moderator.
		 * @return boolean
		 *
		 */

	public function isApproved() { return $this->approved; }




		/**
		 *
		 * Returns the creation date of the guestbook post.
		 * @return DateTime
		 *
		 */

	public function getDate() { return $this->crdate; }



		/**
		 *
		 * Gets the spam rating for this post. The spam rating is influenced by
		 * the amount of links in the posts, the post content and the
		 * punctuation of post content (many spam comments do not contain no
		 * dots or commas whatsoever).
		 *
		 * @return int The spam rating
		 *
		 */

	public function getSpamRating() {
		return $this->spamProtect_getURLBBCodeCount() +
		       $this->spamProtect_getSuspiciousWordCount() +
			   $this->spamProtect_checkMissingPunctuation();
	}



		/**
		 *
		 * Converts !|)!07!( 4nl) (#!1l)!5# 1337-5|>34|< to regular speech.
		 * Well, who can read the sentence above? Don't worry, a translation
		 * table is in the function below.
		 *
		 * @return string The post content without 1337-5|>3@|<.
		 *
		 */

	protected function getContentWithout1337Speak() {
		$replace = array(
			'4'   => 'a', '@'  => 'a', '/\\' => 'a', '/-\\' => 'a',
			'8'   => 'b', '|3' => 'b', 'ß'   => 'b', 'l3'   => 'b',
			'('   => 'c', '['  => 'c', '©'   => 'c',
			'|)'  => 'd', 'l)' => 'd',
			'3'   => 'e', '€'  => 'e',
			'l='  => 'f', '|=' => 'f',
			'6'   => 'g', '9'  => 'g',
			'|-|' => 'h', '#'  => 'h', 'l-l' => 'h',
			'!'   => 'i',
			'|<'  => 'k',
			'/\\/\\' => 'm', '/V\\' => 'm',
			'|\\|' => 'n', '/\\/' => 'n',
			'0' => '0', '()' => 'o',
			'|°' => 'p', '|>' => 'p',
			'O_' => 'q', '0_' => 'q',
			'|2' => 'r', '2' => 'r', '?' => 'r',
			'5' => 's',
			'7' => 't',
			'|_|' => 'u',
			'\\/\\/' => 'w', 'vv' => 'w', '\\a/' => 'w',
			'\\/' => 'v', '|/' => 'v', '\\|' => 'v',
			'><' => 'x',
			'1'   => 'l', '|'  => 'l'
		);
		return str_replace(array_keys($replace), array_values($replace), $this->getContentWithoutWhitespace());
	}



		/**
		 *
		 * Returns the post content without any bb codes in it.
		 * @return string The post content without any bb codes in it.
		 *
		 */

	protected function getContentWithoutBBCode() {
		$content = $this->getContent();
		$content = preg_replace('/\[(.*?)\]/g','',$content);
		return $content;
	}



		/**
		 *
		 * Gets the post content without any whitespace in it. This is needed
		 * for the spam detection mechanism (well, writing "v i a g r a" does
		 * not help you anymore...)
		 *
		 * @return string The post content without whitespace
		 *
		 */

	protected function getContentWithoutWhitespace() {
		return preg_replace('/\s+/', '', $this->getContent());
	}



		/**
		 *
		 * The amount of [url=...] bb codes in the post content. A high amount
		 * of these codes often is an indication for spam.
		 * @return int The amount of [url=...] bb codes in the content.
		 *
		 */

	protected function spamProtect_getURLBBCodeCount() {
		preg_match_all('/\[url(=.*?)?\]/', $this->getContentWithoutWhitespace(), $matches);
		return count($matches[0]);
	}



		/**
		 *
		 * Evaluates, if the post content contains any suspicious words and
		 * returns a rating based on the frequency of these worde. Words like
		 * "viagra" and "penis enlargement"score lots of points...
		 *
		 * @return int A rating based on the frequency of suspicious words.
		 *
		 */

	protected function spamProtect_getSuspiciousWordCount() {
		$badWords = array('viagra'=>99, 'cialis'=>99, 'casino'=>5, 'poker'=>5, 'blackjack'=>5, 'penis'=>99,
			'enlargement'=>5, 'medicine'=>4, 'cheap'=>4, 'buy'=>4, 'free'=>2, 'porn'=>5, 'pron'=>5,
			'chicks'=>4, 'sluts'=>4, 'sex'=>4, 'cheep'=>3, 'price'=>3, 'hentai'=>5, 'swiss'=>3, 'watch'=>3,
			'rolex'=>3, 'weight'=>3, 'fat'=>3, 'nazi'=>3, 'hitler'=>5, 'siegheil'=>5, 'breitling'=>3,
			'dick'=>4, 'fick'=>5, 'online'=>1
		);
		$content = strtolower($this->getContentWithout1337Speak());
		$ranking = 0;
		foreach($badWords as $badWord => $rating)
			if(strpos($content, $badWord) !== FALSE) $ranking += $rating;
		return $ranking;
	}



		/**
		 *
		 * Evaluates the punctuation within the posts content. The longer the
		 * post, the more unrealistic it is for this post not to contain any
		 * punctuation at all. So, for every 300 characters without a single
		 * dot, question or exclamation mark or comma, the post scores one spam
		 * point.
		 *
		 * @return int The punctuation spam rating.
		 *
		 */

	protected function spamProtect_checkMissingPunctuation() {
		$content = $this->getContentWithoutBBCode();
		$content = strip_tags($content);
		$count   = 0;

		$lengthModifier = strlen($this->getContent())/300 + 1;
		$punctuation = array('.',',','?','!');
		foreach($punctuation as $i)
			if(strpos($content, $i) !== FALSE) $count += 1;
		return $count == 0 ? $lengthModifier : 0;
	}
	
}
?>