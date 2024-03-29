<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
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

/**
 * A repository for Posts
 */
class Tx_HBook_Domain_Repository_PostRepository extends Tx_Extbase_Persistence_Repository {

	public function findFromOffset($postsPerPage, $pageNumber = 1) {
		$query = $this->createQuery();
		return $query->setOrderings(array('crdate' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING))
			->setLimit($postsPerPage)
			->setOffset(($pageNumber-1) * $postsPerPage)
			->execute();
	}

	public function getPostCount() {
		return $this->createQuery()->count();
	}

}
?>