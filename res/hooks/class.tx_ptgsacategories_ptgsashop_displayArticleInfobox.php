<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2008 Fabrizio Branca (branca@punkt.de)
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

require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryCollection.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'pi1/class.tx_ptgsacategories_pi1.php';
require_once t3lib_extMgm::extPath('pt_gsashop').'pi2/class.tx_ptgsashop_pi2.php';
require_once t3lib_extMgm::extPath('pt_gsashop').'res/class.tx_ptgsashop_article.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';



class tx_ptgsacategories_ptgsashop_displayArticleInfobox {
	
	
	/**
	 * Add marker with categories to the marker array
	 *
	 * @param 	tx_ptgsashop_pi2 	reference to the calling plugin object
	 * @param 	array 				markerArray
	 * @param 	tx_ptgsashop_article 	articleObj
	 * @return 	array				markerArray
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-06-23
	 */
	public function displayArticleInfobox_MarkerArrayHook(tx_ptgsashop_pi2 $pObj, array $markerArray, tx_ptgsashop_article $articleObj) {

		$categoryCollection = new tx_ptgsacategories_categoryCollection();
		$categoryCollection->getParentCategoriesForArticle($articleObj);
		
		$linkParameterName = $GLOBALS['TSFE']->cObj->stdWrap(tx_pttools_div::getTS('config.tx_ptgsacategories.articleBox.linkParameterName'), tx_pttools_div::getTS('config.tx_ptgsacategories.articleBox.linkParameterName.'));
				
		$markerArray['categories'] = array();
		foreach ($categoryCollection as $category) { /* @var $category tx_ptgsacategories_category */
			$typoLink = tx_pttools_div::getTS('config.tx_ptgsacategories.articleBox.categoryLink.');
			
			$typoLink['additionalParams'] = $GLOBALS['TSFE']->cObj->stdWrap($typoLink['additionalParams'], $typoLink['additionalParams.']);
			$typoLink['additionalParams'] .= '&'.tx_ptgsacategories_pi1::convertPipedGpvarString($linkParameterName) . '=' . $category->get_uid(); // TODO: hash values!

			$markerArray['categories'][] = $GLOBALS['TSFE']->cObj->typolink($category->get_title(), $typoLink);	
		}
		
		return $markerArray;
	}
	
}


?>