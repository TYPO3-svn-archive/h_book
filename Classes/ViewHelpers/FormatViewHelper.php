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
	 * A guestbook post
	 *
	 * @package HBook
	 * @subpackage Domain
	 * @version $Id:$
	 * @copyright Copyright belongs to the respective authors
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
	 * @scope prototype
	 * @entity
	 *
	 */

class Tx_HBook_ViewHelpers_FormatViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	protected $bbCodes = array(
		'/\[b\](.*?)\[\/b\]/i' => '<b>\\1</b>',
		'/\[bold\](.*?)\[\/bold\]/i' => '<b>\\1</b>',
		'/\[fett\](.*?)\[\/fett\]/i' => '<b>\\1</b>',
		'/\[i\](.*?)\[\/i\]/i' => '<i>\\1</i>',
		'/\[italic\](.*?)\[\/italic\]/i' => '<i>\\1</i>',
		'/\[kursiv\](.*?)\[\/kursiv\]/i' => '<i>\\1</i>',
		'/\[url=(.*?)\](.*?)\[\/url\]/i' => '<a href="\\1" target="_blank">\\2</i>',
		'/\[link=(.*?)\](.*?)\[\/link\]/i' => '<a href="\\1" target="_blank">\\2</i>',
	);

		/**
		 * @param boolean $smilies
		 * @return string
		 */
	public function render($smilies = TRUE) {
		$content = $this->renderChildren();

		$smilies = t3lib_div::makeInstance('Tx_HBook_Domain_Repository_SmilieRepository')->findAll();
		foreach($smilies as $smilie)
			$content = str_replace($smilie->getCode(), '<img src="'.$smilie->getImagePath().'" alt="'.$smilie->getCode().'" style="vertical-align: middle;" />', $content);
		$content = preg_replace(array_keys($this->bbCodes), array_values($this->bbCodes), $content);

		return nl2br($content);
	}

}
?>