<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2007-2008 Fabrizio Branca (branca@punkt.de)
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


require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_category.php';
require_once t3lib_extMgm::extPath('pt_gsacategories').'res/class.tx_ptgsacategories_categoryAccessor.php';  

require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php'; // debugging class with trace() function
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php'; // abstract object collection class
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php'; // general exception class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php'; // general static library class
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php'; // Assertion class



/**
 * Category collection
 * 
 * $Id: class.tx_ptgsacategories_categoryCollection.php,v 1.8 2008/07/01 08:22:55 ry44 Exp $
 *
 * @author      Fabrizio Branca <branca@punkt.de>
 * @since       2007-11-19
 * @package     TYPO3
 * @subpackage  tx_ptgsacategories
 */
class tx_ptgsacategories_categoryCollection extends tx_pttools_objectCollection {

	protected $restrictedClassName = 'tx_ptgsacategories_category';

	
	
	/**
	 * Overwrite restrictedClassName
	 *
	 * @param 	string	restrictedClassName
	 * @return 	void
	 * @throws	tx_pttools_exception	if classname is not a subclass of the preset restrictedClassName or if the collection contains items that are not instances of the new class
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2008-05-30
	 */
	final public function set_restrictedClassName($restrictedClassName) {
		
		// check if the new restrictedClassName is a subclass of the old one
		if (!is_null($this->restrictedClassName) && !is_subclass_of($restrictedClassName, $this->restrictedClassName)) {
			throw new tx_pttools_exception('Classname must be a subclass of "'.$this->restrictedClassName.'"!');
		}
		
		// check if all items of the collection (if any) are instance of the new restricedClassName
		foreach ($this as $item) {
			if (!($item instanceof $restrictedClassName)) {
				throw new tx_pttools_exception('Collection contains at least one item that is not an instance of the class you want to set');
			}
		}
		
		$this->restrictedClassName = $restrictedClassName;
	}
	
	
    /**
     * Fill the collection with all root nodes
     *
     * @param	string   order root nodes by given db field (added by Michael Knoll)
     * @return 	void
     * @author 	Fabrizio Branca <branca@punkt.de>
     * @since 	2008-01-22
     */
    public function getRoodnodes($orderBy = '') {
        $catAccessor = tx_ptgsacategories_categoryAccessor::getInstance();
        $dataArray = $catAccessor->selectRootCategories($orderBy);
		$categoryClassName = t3lib_div::makeInstanceClassName($this->restrictedClassName);
        foreach ($dataArray as $data) {
            $this->addItem(new $categoryClassName('', $data));
        }
    }
    
    
    
    /**
     * Returns a collection of parent categories of an article
     *
     * @param 	tx_ptgsashop_baseArticle 	article object
     * @return 	void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2008-05-05
     */
    public function getParentCategoriesForArticle(tx_ptgsashop_baseArticle $article) {

    	tx_pttools_assert::isEmpty($this->itemsArr, array('message' => 'Collection must be empty!'));
    	
    	$cat_art_array = tx_ptgsacategories_categoryAccessor::getInstance()->selectCategoriesConnectionsForArticle($article->get_id());
		  	
		$categoryClassName = t3lib_div::makeInstanceClassName($this->restrictedClassName);
    	foreach ($cat_art_array as $cat_uid) {
    		$this->addItem(new $categoryClassName($cat_uid));
    	}
    }
        
} // end class




/*******************************************************************************
 *   TYPO3 XCLASS INCLUSION (for class extension/overriding)
 ******************************************************************************/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_categoryCollection.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pt_gsacategories/res/class.tx_ptgsacategories_categoryCollection.php']);
}

?>