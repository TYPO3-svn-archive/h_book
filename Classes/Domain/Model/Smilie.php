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
	 * A guestbook smilie.
	 * This class provides the domain model for an HBook smilie.
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

class Tx_HBook_Domain_Model_Smilie extends Tx_Extbase_DomainObject_AbstractEntity {





		/*
		 * ATTRIBUTES
		 */





		/**
		 * @var string
		 */
	protected $code;

		/**
		 * @var string
		 */
	protected $image;





		/*
		 * CONSTRUCTORS
		 */





		 /**
		  *
		  * Constructs the smilie.
		  *
		  */

	public function __construct() {}





		/*
		 * GETTERS
		 */





		/**
		 *
		 * Gets the smilie code, i.e. the code that will be replaced with the
		 * actual smilie image.
		 * @return string The smilie code.
		 *
		 */

	public function getCode() { return $this->code; }



		/**
		 *
		 * Gets the filename of the smilie image.
		 * @return string The filename of the smilie image.
		 *
		 */

	public function getImage() { return $this->image; }



		/**
		 *
		 * Gets the complete filepath of the smilie image, relative to the
		 * SITE_PATH. This method first checks if the smilie image is found in
		 * the official upload directory (usually /uploads/tx_hbook/smilies). If
		 * the smilie does not exist there, look in the extensions' Resources/
		 * directory
		 *
		 * @return <type>
		 *
		 */

	public function getImagePath() {
		$fileName = $GLOBALS['TCA']['tx_hbook_domain_model_smilie']['columns']['image']['config']['uploadfolder'] . '/' . $this->getImage();
		return file_exists($fileName) ? $fileName : t3lib_extMgm::siteRelPath('h_book').'Resources/Public/img/smilies/' . $this->getImage();
	}
	
}
?>
