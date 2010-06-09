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
 * The post controller for the HBook extension. This controller controls the
 * display and creation of guestbook posts.
 *
 * @author Martin Helmich <kontakt@martin-helmich.de>
 * @version 2010-01-05
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 *
 */
class Tx_HBook_Controller_PostController extends Tx_Extbase_MVC_Controller_ActionController {

		/**
		 * @var Tx_HBook_Domain_Model_PostRepository
		 */
	private $postRepository;

		/*
		 * ACTIONS
		 */

		/**
		 * Initializes the current action
		 * @return void
		 */
	public function initializeAction() {
		$this->postRepository = t3lib_div::makeInstance('Tx_HBook_Domain_Repository_PostRepository');
	}

		/**
		 * Index action for this controller. Displays a list of blogs.
		 * @param int $page
		 * @return string The rendered view
		 */
	public function indexAction($page=1) {
		$this->view->assign('postCount', $this->postRepository->getPostCount());
		$this->view->assign('posts', $this->postRepository->findFromOffset($this->getPostsPerPage(), $page));
	}

		/**
		 * @param Tx_HBook_Domain_Model_Post $post
		 * @dontvalidate $post
		 */
	public function newAction(Tx_HBook_Domain_Model_Post $post = NULL) {
		// @see http://forge.typo3.org/issues/show/6004
		if ($post == NULL) {
			$post = t3lib_div::makeInstance('Tx_HBook_Domain_Model_Post');
		}
		$this->view->assign('post', $post);
	}



		/**
		 *
		 * Creates a new post.
		 *
		 * @param Tx_HBook_Domain_Model_Post $post The new post that is to be
		 *                                         created.
		 * @param string $fruitSalad A variable to filter out spam bots. The
		 *                           input field for this parameter is hidden by
		 *                           default, so a human visitor should never
		 *                           fill this input field.
		 *                           To avoid suspicion, this parameter is named
		 *                           after a salad.
		 *
		 */

	public function createAction(Tx_HBook_Domain_Model_Post $post, $fruitSalad) {

		if($fruitSalad != "") throw new Tx_HBook_Controller_Exception_SpamBotException();
		$spamRating = $post->getSpamRating();

		if($spamRating < $this->settings['killSpamRating']) {
			$post->setApproved  ( $spamRating < $this->settings['suspicouosSpamRating'] );
			$post->setIPAddress ( t3lib_div::getIndpEnv('REMOTE_ADDR') );
			$this->postRepository->add($post);
		} else throw new Tx_HBook_Controller_Exception_SpamException();

		$this->redirect('index');
	}

		/**
		 * @param Tx_Extbase_Exception $exception
		 * @dontvalidate $exception
		 */
	public function handleErrorAction(Tx_Extbase_Exception $exception) {
		$this->view->assign('exception', $exception);
	}


		/*
		 * HELPER METHODS
		 */

	protected function getPostsPerPage() {
		return intval($this->settings['postsPerPage']);
	}

}

?>